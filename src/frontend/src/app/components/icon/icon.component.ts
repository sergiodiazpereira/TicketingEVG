/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente global para renderizar iconos SVG de forma limpia.
 */
import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-icon',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './icon.component.html'
})
export class IconComponent {
  /** Nombre del icono a renderizar (ej. 'edit', 'save') */
  @Input() name!: string;
  /** Color del trazo del SVG */
  @Input() color: string = 'currentColor';
  /** Tamaño del icono en píxeles */
  @Input() size: string = '24';
  /** Grosor de la línea del SVG */
  @Input() strokeWidth: string = '2';
  /** Clases CSS adicionales */
  @Input() cssClass: string = '';
}
