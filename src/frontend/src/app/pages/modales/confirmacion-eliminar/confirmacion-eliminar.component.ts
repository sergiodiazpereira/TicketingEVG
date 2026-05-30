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
/**
 * Componente modal de confirmación premium unificado.
 * Permite advertir y validar acciones críticas antes de su ejecución.
 * 
 * @component
 */
export class ConfirmacionEliminarComponent {
  /** Tipo de confirmación a desplegar, que rige los textos, iconos y botones en la vista. */
  @Input() tipo: 'categoria' | 'operario' | 'desasignar' | 'cancelar-ticket' | 'cambio-categoria-desasignar' | 'cerrar-sesion' | 'eliminar-multiple' | 'eliminar-multiple-operarios' = 'categoria';
  
  /** Evento emitido al cancelar la confirmación. */
  @Output() cancelar = new EventEmitter<void>();
  
  /** Evento emitido al aprobar y confirmar la acción. */
  @Output() confirmar = new EventEmitter<void>();

  /**
   * Cierra el modal emitiendo el evento de cancelación.
   */
  cerrar() {
    this.cancelar.emit();
  }
}
