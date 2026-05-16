import { Component, EventEmitter, Input, Output } from '@angular/core';
import { CommonModule } from '@angular/common';

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
  @Output() resolver = new EventEmitter<number>();
  @Output() asignar = new EventEmitter<{idTicket: number, idOperario: number}>();

  onCerrar() {
    this.cerrar.emit();
  }

  onResolver() {
    this.resolver.emit(this.ticket.id);
  }

  onNoAplica() {
    console.log('Ticket marcado como No Aplica:', this.ticket.id);
    this.onCerrar();
  }


  onAsignar(event: any) {
    const idOperario = event.target.value;
    if (idOperario) {
      this.asignar.emit({ idTicket: this.ticket.id, idOperario: parseInt(idOperario) });
    }
  }
}
