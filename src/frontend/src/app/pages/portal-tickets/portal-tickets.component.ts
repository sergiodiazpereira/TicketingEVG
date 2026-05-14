/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Portal de gestión de tickets para usuarios (profesores/alumnos).
 */
import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { UpperCasePipe } from '@angular/common';
import { FormsModule } from '@angular/forms'; // Necesario para el formulario del modal
import { AuthService } from '../../services/auth.service';
import { TicketService } from '../../services/ticket.service';
import { Usuario } from '../../models/usuario.model';
import { Ticket } from '../../models/ticket.model';

@Component({
  selector: 'app-portal-tickets',
  standalone: true,
  imports: [RouterLink, UpperCasePipe, FormsModule], // Añadimos FormsModule
  templateUrl: './portal-tickets.component.html',
  styleUrl: './portal-tickets.component.css'
})
export class PortalTicketsComponent implements OnInit {
  /** Usuario que ha iniciado sesión actualmente */
  usuario_actual: Usuario | null = null;
  /** Lista de tickets del usuario */
  tickets: Ticket[] = [];
  
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
    private ticketService: TicketService
  ) {}

  /**
   * Inicialización del componente: recupera el usuario y carga sus tickets.
   */
  ngOnInit(): void {
    this.usuario_actual = this.authService.getUsuarioActual();
    if (this.usuario_actual) {
      this.cargarTickets();
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

    // Mapeo de prioridad de texto a código de base de datos
    const mapaPrioridad: any = { 'baja': 'b', 'media': 'm', 'alta': 'a' };

    // Preparamos el objeto ticket para el backend con el tipado correcto
    const ticketData: Partial<Ticket> = {
      titulo: this.nuevoTicket.titulo,
      descripcion: this.nuevoTicket.descripcion,
      tipo: this.nuevoTicket.tipo,
      prioridad: mapaPrioridad[this.nuevoTicket.prioridad] || 'm',
      id_Categoria: this.nuevoTicket.categoriaId,
      id_Usuario_Creador: this.usuario_actual.id,
      estado: 'pendiente'
    };

    // Llamamos al servicio para guardar
    this.ticketService.crearTicket(ticketData).subscribe({
      next: (res) => {
        console.log('Ticket creado:', res);
        this.cargarTickets(); // Recargamos la lista
        this.resetForm();     // Limpiamos el formulario
      },
      error: (err) => alert('Error al crear el ticket. Revisa la conexión con el backend.')
    });
  }

  /**
   * Traduce el código de prioridad ('a', 'm', 'b') a texto legible.
   * @param codigo Código de un solo carácter.
   */
  getPrioridadTexto(codigo: string): string {
    const mapa: any = { 'a': 'Alta', 'm': 'Media', 'b': 'Baja' };
    return mapa[codigo] || 'Media';
  }

  /**
   * Restablece los valores del formulario de creación a su estado inicial.
   */
  resetForm(): void {
    this.nuevoTicket = {
      titulo: '',
      descripcion: '',
      tipo: 'incidencia',
      prioridad: 'media',
      categoriaId: 1
    };
  }
}
