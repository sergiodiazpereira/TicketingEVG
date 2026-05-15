import { Component, Input, Output, EventEmitter } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-formulario-operario',
  imports: [CommonModule],
  templateUrl: './formulario-operario.component.html',
  styleUrl: './formulario-operario.component.css'
})
export class FormularioOperarioComponent {
  @Input() operario: any = null;
  @Output() cerrar = new EventEmitter<void>();
  @Output() guardar = new EventEmitter<void>();
}
