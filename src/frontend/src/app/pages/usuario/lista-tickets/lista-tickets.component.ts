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
          // Mocks por si la API falla
          this.tickets = [
            { id: 'TKT-1001', titulo: 'Fallo conexión Wi-Fi', descripcion: 'La red no aparece...', fecha_creacion: '2026-05-15', tipo: 'incidencia', estado: 'pendiente', prioridad: 'a', id_Usuario_Creador: 1, id_Categoria: 1 },
            { id: 'TKT-1002', titulo: 'Instalación de software', descripcion: 'Necesito SPSS...', fecha_creacion: '2026-05-14', tipo: 'peticion', estado: 'resuelto', prioridad: 'm', id_Usuario_Creador: 1, id_Categoria: 2 }
          ];
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
