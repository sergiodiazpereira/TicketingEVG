/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio para la gestión de tickets conectando con la API PHP.
 */
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable, map } from 'rxjs';
import { environment } from '../../enviroments/environment';
import { Ticket } from '../models/ticket.model';

@Injectable({
  providedIn: 'root'
})
export class TicketService {
  /** URL base de la API obtenida de los archivos de entorno */
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  /**
   * Obtiene todos los tickets del sistema a través de la API.
   * @returns Observable con la lista de tickets.
   */
  getTickets(): Observable<Ticket[]> {
    return this.http.get<Ticket[]>(`${this.apiUrl}?accion=listar`);
  }

  /**
   * Obtiene los tickets de un usuario específico.
   * @param usuarioId ID del usuario logueado.
   * @returns Observable filtrado.
   */
  getTicketsPorUsuario(usuarioId: number): Observable<Ticket[]> {
    return this.http.get<Ticket[]>(`${this.apiUrl}?accion=listar&usuario_id=${usuarioId}`);
  }

  /**
   * Registra un nuevo ticket en la base de datos.
   * @param ticket Objeto ticket con los datos del formulario.
   * @returns Observable con la respuesta del servidor.
   */
  crearTicket(ticket: Partial<Ticket>): Observable<any> {
    return this.http.post(`${this.apiUrl}?accion=crear`, ticket);
  }

  /**
   * Cambia el estado de un ticket.
   * @param id Identificador del ticket.
   * @param estado Nuevo estado.
   */
  actualizarEstado(id: string, estado: string): Observable<any> {
    return this.http.put(`${this.apiUrl}?accion=actualizar`, { id, estado });
  }

  /**
   * Elimina un ticket físicamente.
   * @param id Identificador del ticket.
   */
  eliminarTicket(id: string): Observable<any> {
    return this.http.delete(`${this.apiUrl}?accion=eliminar&id=${id}`);
  }
}
