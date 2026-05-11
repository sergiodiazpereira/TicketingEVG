/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio de autenticación con usuarios estáticos.
 */
import { Injectable } from '@angular/core';
import { Usuario } from '../models/usuario.model';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private usuarios: Usuario[] = [
    { id: 1, nombre: 'Administrador', email: 'admin@evg.es', password: 'admin', rol: 'admin' },
    { id: 2, nombre: 'Joseph Joel', email: 'joseph@evg.es', password: 'joseph', rol: 'admin' },
    { id: 3, nombre: 'Profesor Test', email: 'profesor@evg.es', password: 'profesor', rol: 'profesor' },
    { id: 4, nombre: 'Operario Test', email: 'operario@evg.es', password: 'operario', rol: 'operario' }
  ];

  private usuarioAutenticado: Usuario | null = null;

  constructor() { }

  /**
   * Intenta iniciar sesion con las credenciales proporcionadas.
   * @param email Correo del usuario.
   * @param password Contrasena del usuario.
   * @returns El usuario si las credenciales son validas, null en caso contrario.
   */
  login(email: string, password: string): Usuario | null {
    const usuario = this.usuarios.find(u => u.email === email && u.password === password);
    if (usuario) {
      this.usuarioAutenticado = usuario;
      return usuario;
    }
    return null;
  }

  /**
   * Cierra la sesion del usuario actual.
   */
  logout(): void {
    this.usuarioAutenticado = null;
  }

  /**
   * Obtiene el usuario autenticado actualmente.
   */
  getUsuarioActual(): Usuario | null {
    return this.usuarioAutenticado;
  }

  /**
   * Verifica si hay un usuario autenticado.
   */
  isAutenticado(): boolean {
    return this.usuarioAutenticado !== null;
  }
}
