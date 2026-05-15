import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-confirmacion-eliminar',
  imports: [CommonModule],
  templateUrl: './confirmacion-eliminar.component.html',
  styleUrl: './confirmacion-eliminar.component.css'
})
export class ConfirmacionEliminarComponent {
  @Input() tipo: 'categoria' | 'operario' = 'categoria';
  @Output() cancelar = new EventEmitter<void>();
  @Output() confirmar = new EventEmitter<void>();

  cerrar() {
    this.cancelar.emit();
  }
}
