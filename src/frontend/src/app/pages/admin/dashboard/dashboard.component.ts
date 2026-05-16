/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para el componente de Dashboard.
 */
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { SidebarComponent } from '../../../shared/layout/sidebar/sidebar.component';
import { AuthService } from '../../../services/auth.service';
import { Usuario } from '../../../models/usuario.model';
import { DashboardService } from '../../../services/dashboard.service';
import { TicketService } from '../../../services/ticket.service';
import { Estadisticas } from '../../../models/estadisticas.model';
import { Ticket } from '../../../models/ticket.model';

@Component({
  selector: 'app-dashboard',
  standalone: true,
  imports: [CommonModule, SidebarComponent],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css'
})
export class DashboardComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  estadisticas: Estadisticas | null = null;
  ticketsRecientes: Ticket[] = [];
  
  /** Gestión del modal */
  mostrarModalTicket: boolean = false;
  ticketSeleccionado: any = null;

  constructor(
    private authService: AuthService, 
    private dashboardService: DashboardService,
    private ticketService: TicketService
  ) {
    this.usuario_actual = this.authService.getUsuarioActual();
  }

  ngOnInit(): void {
    this.cargarEstadisticas();
    this.cargarTicketsRecientes();
  }

  cargarEstadisticas(): void {
    this.dashboardService.getEstadisticas().subscribe({
      next: (data) => {
        // Verificar que la respuesta sea un objeto válido y no un error
        if (data && !((data as any).error)) {
          this.estadisticas = data;
        } else {
          console.error('La API devolvió un formato de estadísticas inválido');
        }
      },
      error: (err) => console.error('Error al cargar estadísticas', err)
    });
  }

  cargarTicketsRecientes(): void {
    // Para el admin, traemos todos los tickets (o los más recientes)
    this.ticketService.getTickets().subscribe({
      next: (data) => this.ticketsRecientes = data.slice(0, 5),
      error: (err) => console.error('Error al cargar tickets recientes', err)
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

  get porcentajeResueltos(): number {
    if (!this.estadisticas || this.estadisticas.total_tickets == 0) return 0;
    return Math.round((this.estadisticas.tickets_resueltos / this.estadisticas.total_tickets) * 100);
  }
}
