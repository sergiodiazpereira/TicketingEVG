/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio para la gestión de usuarios/operarios conectando con la API PHP.
 */
import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../enviroments/environment';
import { Usuario } from '../models/usuario.model';

@Injectable({
  providedIn: 'root'
})
export class UsuarioService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  /**
   * Obtiene la lista de operarios del sistema.
   * @returns Observable con el array de operarios.
   */
  getOperarios(): Observable<Usuario[]> {
    return this.http.get<Usuario[]>(`${this.apiUrl}?entidad=usuario&accion=listar_operarios`);
  }

  /**
   * Crea un nuevo operario en el sistema.
   * @param datos Datos del formulario (nombre, correo, rol, categorias).
   * @returns Observable con la respuesta del servidor.
   */
  crearUsuario(datos: any): Observable<any> {
    return this.http.post(`${this.apiUrl}?entidad=usuario&accion=guardar`, datos);
  }

  /**
   * Actualiza los datos de un operario existente.
   * @param id ID del operario.
   * @param datos Datos actualizados (nombre, correo, rol, categorias).
   * @returns Observable con la respuesta del servidor.
   */
  actualizarUsuario(id: number, datos: any): Observable<any> {
    return this.http.put(`${this.apiUrl}?entidad=usuario&accion=guardar`, { ...datos, id });
  }

  /**
   * Elimina un operario por su ID.
   * @param id ID del operario a eliminar.
   * @returns Observable con la respuesta del servidor.
   */
  eliminarUsuario(id: number): Observable<any> {
    return this.http.delete(`${this.apiUrl}?entidad=usuario&accion=eliminar&id=${id}`);
  }
}
