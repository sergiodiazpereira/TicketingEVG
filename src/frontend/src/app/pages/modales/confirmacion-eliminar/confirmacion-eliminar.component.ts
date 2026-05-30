/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente modal de confirmación premium unificado.
 */
import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-confirmacion-eliminar',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './confirmacion-eliminar.component.html',
  styleUrl: './confirmacion-eliminar.component.css'
})
export class ConfirmacionEliminarComponent {
  @Input() tipo: 'categoria' | 'operario' | 'desasignar' | 'cancelar-ticket' | 'cambio-categoria-desasignar' | 'cerrar-sesion' | 'eliminar-multiple' | 'eliminar-multiple-operarios' = 'categoria';
  @Output() cancelar = new EventEmitter<void>();
  @Output() confirmar = new EventEmitter<void>();

  cerrar() {
    this.cancelar.emit();
  }
}
