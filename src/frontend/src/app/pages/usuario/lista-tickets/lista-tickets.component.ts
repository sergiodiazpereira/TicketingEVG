import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { FormsModule } from '@angular/forms';
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
  imports: [CommonModule, RouterModule, FormsModule, ModalTicketComponent, FooterComponent],
  templateUrl: './lista-tickets.component.html',
  styleUrl: './lista-tickets.component.css'
})
export class ListaTicketsComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  tickets: Ticket[] = [];
  
  /** Variables para búsqueda y filtrado */
  terminoBusqueda: string = '';
  filtroEstado: string = 'todos';
  ticketsFiltrados: Ticket[] = [];

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
          this.filtrarTickets();
        },
        error: (err) => {
          console.error('Error al cargar tickets', err);
          this.tickets = [];
          this.ticketsFiltrados = [];
        }
      });
    }
  }

  filtrarTickets(): void {
    const busqueda = this.terminoBusqueda.toLowerCase().trim();
    this.ticketsFiltrados = this.tickets.filter(ticket => {
      // Filtrar por término de búsqueda (asunto, descripción o ID)
      const coincideBusqueda = !busqueda || 
        ticket.titulo.toLowerCase().includes(busqueda) ||
        ticket.descripcion.toLowerCase().includes(busqueda) ||
        ticket.id.toLowerCase().includes(busqueda);

      // Filtrar por estado o tipo
      let coincideFiltro = true;
      if (this.filtroEstado !== 'todos') {
        if (this.filtroEstado === 'incidencia' || this.filtroEstado === 'peticion') {
          coincideFiltro = ticket.tipo === this.filtroEstado;
        } else if (this.filtroEstado === 'proceso') {
          coincideFiltro = ticket.estado === 'proceso' || ticket.estado === 'asignado';
        } else {
          coincideFiltro = ticket.estado === this.filtroEstado;
        }
      }

      return coincideBusqueda && coincideFiltro;
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
}
