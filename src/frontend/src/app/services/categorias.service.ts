/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio para gestionar datos de categorías.
 */

import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../enviroments/environment';
import { Categoria } from '../models/categoria.model';

@Injectable({
  providedIn: 'root'
})
export class CategoriasService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) {}

  /**
   * Obtiene la lista de categorías.
   */
  obtenerCategorias(): Observable<Categoria[]> {
    return this.http.get<Categoria[]>(`${this.apiUrl}?entidad=categoria&accion=listar`);
  }

  /**
   * Crea una nueva categoría.
   */
  crearCategoria(categoria: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}?entidad=categoria&accion=guardar`, categoria);
  }

  /**
   * Actualiza una categoría existente.
   */
  actualizarCategoria(categoria: any): Observable<any> {
    return this.http.post<any>(`${this.apiUrl}?entidad=categoria&accion=guardar`, categoria);
  }

  /**
   * Elimina una categoría.
   */
  eliminarCategoria(id: number): Observable<any> {
    return this.http.delete<any>(`${this.apiUrl}?entidad=categoria&accion=borrar&id=${id}`);
  }
}
