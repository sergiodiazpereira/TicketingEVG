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
      this.tickets = this.ticketService.getTicketsPorUsuario(this.usuario_actual.id);
      this.calcularStats();
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

    // Preparamos el objeto ticket con el ID del usuario actual
    const ticketData: Omit<Ticket, 'id' | 'fechaCreacion'> = {
      ...this.nuevoTicket,
      usuarioId: this.usuario_actual.id,
      estado: 'pendiente'
    };

    // Llamamos al servicio para guardar
    this.ticketService.addTicket(ticketData);
    
    // Recargamos la lista y estadísticas
    this.cargarTickets();

    // Limpiamos el formulario
    this.resetForm();

    // Nota: El cierre del modal se gestiona mediante data-bs-dismiss en el botón del HTML
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
