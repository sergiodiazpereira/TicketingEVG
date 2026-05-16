/**
 * Proyecto: TicketingEVG
 * Alumno: Sergio Díaz Pereira
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Portal de gestión de tickets para usuarios (profesores/alumnos).
 */
import { Component, OnInit } from '@angular/core';
import { Router, RouterLink, RouterLinkActive } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { CommonModule } from '@angular/common';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';
import { AuthService } from '../../../services/auth.service';
import { TicketService } from '../../../services/ticket.service';
import { ModalTicketComponent } from '../../modales/modal-ticket/modal-ticket.component';
import { Usuario } from '../../../models/usuario.model';
import { Ticket } from '../../../models/ticket.model';

@Component({
  selector: 'app-portal-tickets',
  standalone: true,
  imports: [CommonModule, RouterLink, RouterLinkActive, FormsModule, FooterComponent, ModalTicketComponent], 
  templateUrl: './portal-tickets.component.html',
  styleUrl: './portal-tickets.component.css'
})
export class PortalTicketsComponent implements OnInit {
  /** Usuario que ha iniciado sesión actualmente */
  usuario_actual: Usuario | null = null;
  /** Lista de tickets del usuario */
  tickets: Ticket[] = [];
  
  /** Gestión del modal de detalles */
  mostrarModalTicket: boolean = false;
  ticketSeleccionado: any = null;
  
  /** Estadísticas calculadas dinámicamente */
  stats = {
    total: 0,
    incidencias: 0,
    peticiones: 0,
    enProceso: 0
  };

  /** Objeto para vincular con el formulario de nuevo ticket */
  nuevoTicket: any = {
    titulo: '',
    descripcion: '',
    tipo: 'incidencia',
    prioridad: 'media',
    categoriaId: 1
  };

  constructor(
    private authService: AuthService,
    private ticketService: TicketService,
    private router: Router
  ) {}

  /**
   * Verifica si la ruta actual es el inicio del portal.
   */
  isInicioActive(): boolean {
    return this.router.url === '/portal-tickets';
  }

  /**
   * Inicialización del componente: recupera el usuario y carga sus tickets.
   */
  ngOnInit(): void {
    this.usuario_actual = this.authService.getUsuarioActual();
    
    // Tickets de ejemplo para demostración final
    this.tickets = [
      {
        id: 'TKT-1001',
        titulo: 'Fallo conexión Wi-Fi Sala Profesores',
        descripcion: 'La red no aparece en los dispositivos del aula.',
        fecha_creacion: '2026-05-15',
        tipo: 'incidencia',
        estado: 'pendiente',
        prioridad: 'a',
        id_Usuario_Creador: 3,
        id_Categoria: 1
      },
      {
        id: 'TKT-1002',
        titulo: 'Instalación de software estadístico',
        descripcion: 'Necesito instalar SPSS en los equipos del laboratorio.',
        fecha_creacion: '2026-05-14',
        tipo: 'peticion',
        estado: 'resuelto',
        prioridad: 'm',
        id_Usuario_Creador: 3,
        id_Usuario_Encargado: 4,
        id_Categoria: 2
      },
      {
        id: 'TKT-1003',
        titulo: 'Proyector muestra imagen borrosa',
        descripcion: 'El proyector del Aula 102 necesita calibración.',
        fecha_creacion: '2026-05-13',
        tipo: 'incidencia',
        estado: 'proceso',
        prioridad: 'a',
        id_Usuario_Creador: 3,
        id_Usuario_Encargado: 4,
        id_Categoria: 1
      }
    ];
    this.calcularStats();

    if (this.usuario_actual) {
      // this.cargarTickets();
    }
  }

  /**
   * Obtiene los tickets del servicio filtrados por el usuario logueado.
   */
  cargarTickets(): void {
    if (this.usuario_actual) {
      this.ticketService.getTicketsPorUsuario(this.usuario_actual.id).subscribe({
        next: (data) => {
          this.tickets = data;
          this.calcularStats();
        },
        error: (err) => console.error('Error al cargar tickets', err)
      });
    }
  }

  /**
   * Recorre la lista de tickets para actualizar los contadores del dashboard.
   */
  calcularStats(): void {
    this.stats.total = this.tickets.length;
    this.stats.incidencias = this.tickets.filter(t => t.tipo === 'incidencia').length;
    this.stats.peticiones = this.tickets.filter(t => t.tipo === 'peticion').length;
    this.stats.enProceso = this.tickets.filter(t => t.estado === 'proceso' || t.estado === 'asignado').length;
  }

  /**
   * Procesa el envío del formulario del modal para crear un nuevo ticket.
   */
  crearTicket(): void {
    if (!this.usuario_actual) return;

    const mapaPrioridad: any = { 'baja': 'b', 'media': 'm', 'alta': 'a' };

    const ticketData: Partial<Ticket> = {
      titulo: this.nuevoTicket.titulo,
      descripcion: this.nuevoTicket.descripcion,
      tipo: this.nuevoTicket.tipo,
      prioridad: mapaPrioridad[this.nuevoTicket.prioridad] || 'm',
      id_Categoria: this.nuevoTicket.categoriaId,
      id_Usuario_Creador: this.usuario_actual.id,
      estado: 'pendiente'
    };

    this.ticketService.crearTicket(ticketData).subscribe({
      next: (res) => {
        console.log('Ticket creado:', res);
        this.cargarTickets();
        this.resetForm();
      },
      error: (err) => alert('Error al crear el ticket. Revisa la conexión con el backend.')
    });
  }

  getPrioridadTexto(codigo: string): string {
    const mapa: any = { 'a': 'Alta', 'm': 'Media', 'b': 'Baja' };
    return mapa[codigo] || 'Media';
  }

  resetForm(): void {
    this.nuevoTicket = {
      titulo: '',
      descripcion: '',
      tipo: 'incidencia',
      prioridad: 'media',
      categoriaId: 1
    };
  }

  abrirModalTicket(ticket: any) {
    this.ticketSeleccionado = ticket;
    this.mostrarModalTicket = true;
  }

  cerrarModalTicket() {
    this.mostrarModalTicket = false;
    this.ticketSeleccionado = null;
  }
}
