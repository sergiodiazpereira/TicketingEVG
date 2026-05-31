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
  private toastsSubject = new BehaviorSubject<ToastMessage[]>([]);
  public toasts$: Observable<ToastMessage[]> = this.toastsSubject.asObservable();
  private nextId = 0;

  mostrarMensaje(texto: string, esError: boolean = false) {
    const id = this.nextId++;
    const currentToasts = this.toastsSubject.value;
    this.toastsSubject.next([...currentToasts, { texto, esError, id }]);

    setTimeout(() => {
      this.removerMensaje(id);
    }, 4000);
  }

  removerMensaje(id: number) {
    const currentToasts = this.toastsSubject.value;
    this.toastsSubject.next(currentToasts.filter(t => t.id !== id));
  }
}
