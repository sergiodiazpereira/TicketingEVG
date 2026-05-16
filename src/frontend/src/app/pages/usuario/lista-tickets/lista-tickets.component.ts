import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../services/auth.service';
import { TicketService } from '../../../services/ticket.service';
import { ModalTicketComponent } from '../../modales/modal-ticket/modal-ticket.component';
import { Ticket } from '../../../models/ticket.model';
import { Usuario } from '../../../models/usuario.model';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';

/**
 * Proyecto: TicketingEVG
 * Alumno: Sergio Díaz Pereira
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente para el listado detallado de mis tickets.
 */
@Component({
  selector: 'app-lista-tickets',
  standalone: true,
  imports: [CommonModule, RouterModule, ModalTicketComponent, FooterComponent],
  templateUrl: './lista-tickets.component.html',
  styleUrl: './lista-tickets.component.css'
})
export class ListaTicketsComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  tickets: Ticket[] = [];
  
  /** Gestión del modal de detalles */
  mostrarModalTicket: boolean = false;
  ticketSeleccionado: any = null;

  constructor(
    private authService: AuthService,
    private ticketService: TicketService
  ) {}

  ngOnInit(): void {
    this.usuario_actual = this.authService.getUsuarioActual();
    this.cargarTickets();
  }

  cargarTickets(): void {
    if (this.usuario_actual) {
      this.ticketService.getTicketsPorUsuario(this.usuario_actual.id).subscribe({
        next: (data) => {
          this.tickets = data;
        },
        error: (err) => {
          console.error('Error al cargar tickets', err);
          this.tickets = [];
        }
      });
    }
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
