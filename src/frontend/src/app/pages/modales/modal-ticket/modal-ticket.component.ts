import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';
import { TicketService } from '../../../services/ticket.service';

/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente unificado para mostrar los detalles de un ticket en un modal.
 */
@Component({
  selector: 'app-modal-ticket',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './modal-ticket.component.html',
  styleUrl: './modal-ticket.component.css'
})
export class ModalTicketComponent {
  @Input() ticket: any;
  @Input() rolUsuario: string = 'profesor'; // 'profesor', 'responsable', 'trabajador', 'alumno'
  @Input() operarios: any[] = []; // Lista de operarios para que el responsable asigne

  @Output() cerrar = new EventEmitter<void>();
  @Output() resolver = new EventEmitter<any>();
  @Output() asignar = new EventEmitter<{idTicket: number, idOperario: number}>();

  /** Feedback interno del modal */
  mensajeFeedback: string | null = null;
  esMensajeError = false;
  procesando = false;

  constructor(private ticketService: TicketService) {}

  onCerrar() {
    this.cerrar.emit();
  }

  /**
   * Marca el ticket como resuelto llamando directamente a la API.
   * Disponible para cualquier rol mientras el ticket no esté ya resuelto.
   */
  onResolver() {
    this.procesando = true;
    this.ticketService.actualizarEstado(this.ticket.id, 'resuelto').subscribe({
      next: (res) => {
        this.procesando = false;
        if (res.status === 'success') {
          this.ticket.estado = 'resuelto';
          this.resolver.emit(this.ticket);
          this.mostrarMensaje('Ticket marcado como resuelto.', false);
        } else {
          this.mostrarMensaje(res.message || 'Error al resolver el ticket.', true);
        }
      },
      error: () => {
        this.procesando = false;
        this.mostrarMensaje('Error de conexión al resolver el ticket.', true);
      }
    });
  }

  /**
   * Cancela el ticket (solo el creador puede hacerlo, si está pendiente).
   */
  onCancelar() {
    this.procesando = true;
    this.ticketService.actualizarEstado(this.ticket.id, 'resuelto').subscribe({
      next: (res) => {
        this.procesando = false;
        if (res.status === 'success') {
          this.ticket.estado = 'resuelto';
          this.resolver.emit(this.ticket);
          this.mostrarMensaje('Ticket cancelado correctamente.', false);
        } else {
          this.mostrarMensaje(res.message || 'Error al cancelar el ticket.', true);
        }
      },
      error: () => {
        this.procesando = false;
        this.mostrarMensaje('Error de conexión.', true);
      }
    });
  }

  onNoAplica() {
    this.onCerrar();
  }

  onAsignar(event: any) {
    const idOperario = event.target.value;
    if (idOperario)
      this.asignar.emit({ idTicket: this.ticket.id, idOperario: parseInt(idOperario) });
  }

  private mostrarMensaje(texto: string, esError: boolean) {
    this.mensajeFeedback = texto;
    this.esMensajeError = esError;
    setTimeout(() => this.mensajeFeedback = null, 4000);
  }
}
