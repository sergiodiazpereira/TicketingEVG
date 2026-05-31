/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente gráfico para renderizar las alertas flotantes globales.
 */
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ToastService, ToastMessage } from '../../services/toast.service';
import { ChangeDetectorRef } from '@angular/core';

@Component({
  selector: 'app-toast',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './toast.component.html',
  styleUrls: ['./toast.component.css']
})
export class ToastComponent implements OnInit {
  toasts: ToastMessage[] = [];

  constructor(private toastService: ToastService, private cdr: ChangeDetectorRef) {}

  /**
   * Se suscribe al Observable del ToastService para actualizar la vista dinámicamente.
   */
  ngOnInit() {
    this.toastService.toasts$.subscribe(toasts => {
      this.toasts = toasts;
      this.cdr.detectChanges();
    });
  }

  /**
   * Cierra (descarta) una alerta manualmente.
   * @param event Evento de clic para evitar bubbling
   * @param id Identificador de la alerta
   */
  cerrar(event: Event, id: number) {
    event.stopPropagation();
    this.toastService.removerMensaje(id);
  }
}
