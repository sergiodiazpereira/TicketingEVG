import { Component, OnInit, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../../services/auth.service';
import { TicketService } from '../../../services/ticket.service';
import { UsuarioService } from '../../../services/usuario.service';
import { ModalTicketComponent } from '../../modales/modal-ticket/modal-ticket.component';
import { Ticket } from '../../../models/ticket.model';
import { Usuario } from '../../../models/usuario.model';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';

/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente para el listado detallado de mis tickets.
 */
@Component({
  selector: 'app-lista-tickets',
  standalone: true,
  imports: [CommonModule, RouterModule, FormsModule, ModalTicketComponent, FooterComponent],
  templateUrl: './lista-tickets.component.html',
  styleUrl: './lista-tickets.component.css'
})
export class ListaTicketsComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  tickets: Ticket[] = [];
  
  /** Variables para búsqueda y filtrado */
  terminoBusqueda: string = '';
  filtroTipo: string = 'todos';
  filtroEstado: string = 'todos';
  ticketsFiltrados: Ticket[] = [];

  /** Estado de los desplegables personalizados */
  desplegableTipoAbierto = false;
  desplegableEstadoAbierto = false;
  desplegableCreadorAbierto = false;

  filtroCreador: string = 'todos';

  get creadoresUnicos(): { id: number; nombre: string }[] {
    const mapa = new Map<number, string>();
    this.tickets.forEach(t => {
      if (t.id_usuario_creador) {
        if (Number(t.id_usuario_creador) !== Number(this.usuario_actual?.id)) {
          mapa.set(Number(t.id_usuario_creador), t.creador_nombre || `Usuario #${t.id_usuario_creador}`);
        }
      }
    });
    return Array.from(mapa.entries()).map(([id, nombre]) => ({ id, nombre })).sort((a, b) => a.nombre.localeCompare(b.nombre));
  }

  /** Gestión del modal de detalles */
  mostrarModalTicket: boolean = false;
  ticketSeleccionado: any = null;
  operarios: Usuario[] = [];

  constructor(
    private authService: AuthService,
    private ticketService: TicketService,
    private usuarioService: UsuarioService
  ) {}

  ngOnInit(): void {
    this.usuario_actual = this.authService.getUsuarioActual();
    this.cargarTickets();
    this.cargarOperarios();
  }

  cargarOperarios(): void {
    this.usuarioService.getOperarios().subscribe({
      next: (data) => this.operarios = data,
      error: (err) => console.error('Error al cargar operarios', err)
    });
  }

  getNombreEncargado(idEncargado: any): string {
    if (!idEncargado) return '';
    const tech = this.operarios.find(op => Number(op.id) === Number(idEncargado));
    return tech ? tech.nombre : `Técnico #${idEncargado}`;
  }

  onAsignarTicket(event: {idTicket: number, idOperario: number}) {
    this.ticketService.asignarTicket(event.idTicket.toString(), event.idOperario).subscribe({
      next: (res) => {
        if (res.status === 'success') {
          if (this.ticketSeleccionado) {
            this.ticketSeleccionado.id_usuario_encargado = event.idOperario;
            this.ticketSeleccionado.estado = 'asignado';
          }
          this.cargarTickets(); // Recargar la lista
        } else {
          alert('Error al asignar el ticket: ' + res.message);
        }
      },
      error: () => alert('Error de conexión al asignar.')
    });
  }

  cargarTickets(): void {
    if (this.usuario_actual) {
      if (this.usuario_actual.rol === 'administrador') {
        this.ticketService.getTickets().subscribe({
          next: (data) => {
            this.tickets = data;
            this.filtrarTickets();
          },
          error: (err) => {
            console.error('Error al cargar tickets globales', err);
            this.tickets = [];
            this.ticketsFiltrados = [];
          }
        });
      } else {
        this.ticketService.getTicketsPorUsuario(this.usuario_actual.id).subscribe({
          next: (data) => {
            this.tickets = data;
            this.filtrarTickets();
          },
          error: (err) => {
            console.error('Error al cargar tickets por usuario', err);
            this.tickets = [];
            this.ticketsFiltrados = [];
          }
        });
      }
    }
  }

  filtrarTickets(): void {
    const busqueda = this.terminoBusqueda.toLowerCase().trim();
    this.ticketsFiltrados = this.tickets.filter(ticket => {
      // Filtrar por término de búsqueda (asunto, descripción o ID)
      const coincideBusqueda = !busqueda || 
        ticket.titulo.toLowerCase().includes(busqueda) ||
        ticket.descripcion.toLowerCase().includes(busqueda) ||
        ticket.id.toString().toLowerCase().includes(busqueda);

      // Filtrar por tipo
      let coincideTipo = true;
      if (this.filtroTipo !== 'todos') {
        coincideTipo = ticket.tipo === this.filtroTipo;
      }

      // Filtrar por estado
      let coincideEstado = true;
      if (this.filtroEstado !== 'todos') {
        coincideEstado = ticket.estado === this.filtroEstado;
      }

      // Filtrar por creador (solo para administradores)
      let coincideCreador = true;
      if (this.usuario_actual && this.usuario_actual.rol === 'administrador') {
        if (this.filtroCreador === 'mis-tickets') {
          coincideCreador = Number(ticket.id_usuario_creador) === Number(this.usuario_actual.id);
        } else if (this.filtroCreador !== 'todos') {
          coincideCreador = Number(ticket.id_usuario_creador) === Number(this.filtroCreador);
        }
      }

      return coincideBusqueda && coincideTipo && coincideEstado && coincideCreador;
    });
  }

  abrirModalTicket(ticket: any) {
    this.ticketSeleccionado = ticket;
    this.mostrarModalTicket = true;
  }

  cerrarModalTicket() {
    this.mostrarModalTicket = false;
    this.ticketSeleccionado = null;
  }

  toggleDesplegableTipo(event: Event): void {
    event.stopPropagation();
    this.desplegableTipoAbierto = !this.desplegableTipoAbierto;
    this.desplegableEstadoAbierto = false;
    this.desplegableCreadorAbierto = false;
  }

  toggleDesplegableEstado(event: Event): void {
    event.stopPropagation();
    this.desplegableEstadoAbierto = !this.desplegableEstadoAbierto;
    this.desplegableTipoAbierto = false;
    this.desplegableCreadorAbierto = false;
  }

  seleccionarTipo(tipo: string): void {
    this.filtroTipo = tipo;
    this.desplegableTipoAbierto = false;
    this.filtrarTickets();
  }

  seleccionarEstado(estado: string): void {
    this.filtroEstado = estado;
    this.desplegableEstadoAbierto = false;
    this.filtrarTickets();
  }

  getTipoEtiqueta(): string {
    const mapa: Record<string, string> = {
      todos: 'TODOS',
      incidencia: 'INCIDENCIAS',
      peticion: 'PETICIONES'
    };
    return mapa[this.filtroTipo] || 'TODOS';
  }

  getEstadoEtiqueta(): string {
    const mapa: Record<string, string> = {
      todos: 'TODOS',
      pendiente: 'PENDIENTES',
      asignado: 'ASIGNADOS',
      proceso: 'EN PROCESO',
      resuelto: 'RESUELTOS'
    };
    return mapa[this.filtroEstado] || 'TODOS';
  }

  toggleDesplegableCreador(event: Event): void {
    event.stopPropagation();
    this.desplegableCreadorAbierto = !this.desplegableCreadorAbierto;
    this.desplegableTipoAbierto = false;
    this.desplegableEstadoAbierto = false;
  }

  seleccionarCreador(creador: string): void {
    this.filtroCreador = creador;
    this.desplegableCreadorAbierto = false;
    this.filtrarTickets();
  }

  getCreadorEtiqueta(): string {
    if (this.filtroCreador === 'todos') return 'TODOS';
    if (this.filtroCreador === 'mis-tickets') return 'MIS TICKETS CREADOS';
    const idNum = Number(this.filtroCreador);
    const c = this.creadoresUnicos.find(x => x.id === idNum);
    return c ? c.nombre.toUpperCase() : `USUARIO #${this.filtroCreador}`;
  }

  logout(): void {
    // Revertido/comentado por el usuario
    // this.authService.logout();
  }

  @HostListener('document:click', ['$event'])
  cerrarDesplegables(): void {
    this.desplegableTipoAbierto = false;
    this.desplegableEstadoAbierto = false;
    this.desplegableCreadorAbierto = false;
  }
}
