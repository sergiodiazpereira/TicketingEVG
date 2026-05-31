/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio para gestionar las alertas emergentes (Toasts) de la aplicación.
 */
import { Injectable } from '@angular/core';
import { BehaviorSubject, Observable } from 'rxjs';

export interface ToastMessage {
  texto: string;
  esError: boolean;
  id?: number;
}

@Injectable({
  providedIn: 'root'
})
export class ToastService {
  /** Lista reactiva de alertas actuales */
  private toastsSubject = new BehaviorSubject<ToastMessage[]>([]);
  public toasts$: Observable<ToastMessage[]> = this.toastsSubject.asObservable();
  
  /** Autoincremental para IDs únicos de cada alerta */
  private nextId = 0;

  /**
   * Muestra un nuevo mensaje emergente durante 4 segundos.
   * @param texto El texto a mostrar.
   * @param esError Si es true, la alerta será roja. Si es false, verde.
   */
  mostrarMensaje(texto: string, esError: boolean = false) {
    const id = this.nextId++;
    const currentToasts = this.toastsSubject.value;
    this.toastsSubject.next([...currentToasts, { texto, esError, id }]);

    setTimeout(() => {
      this.removerMensaje(id);
    }, 4000);
  }

  /**
   * Elimina un mensaje del array activo.
   * @param id Identificador de la alerta.
   */
  removerMensaje(id: number) {
    const currentToasts = this.toastsSubject.value;
    this.toastsSubject.next(currentToasts.filter(t => t.id !== id));
  }
}
