/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio de autenticación con usuarios estáticos.
 */
import { Injectable, Inject, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { Usuario } from '../models/usuario.model';

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private usuarios: Usuario[] = [
    { id: 1, nombre: 'Administrador', email: 'admin@evg.es', password: 'admin', rol: 'admin' },
    { id: 2, nombre: 'Joseph Joel', email: 'joseph@evg.es', password: 'joseph', rol: 'admin' },
    { id: 3, nombre: 'Profesor Test', email: 'profesor@evg.es', password: 'profesor', rol: 'profesor' },
    { id: 4, nombre: 'Responsable Test', email: 'responsable@evg.es', password: 'responsable', rol: 'responsable' },
    { id: 5, nombre: 'Trabajador Test', email: 'trabajador@evg.es', password: 'trabajador', rol: 'trabajador' }

  ];

  private usuarioAutenticado: Usuario | null = null;

  constructor(@Inject(PLATFORM_ID) private platformId: Object) {
    if (isPlatformBrowser(this.platformId)) {
      const storedUser = localStorage.getItem('usuario_actual');
      if (storedUser) {
        this.usuarioAutenticado = JSON.parse(storedUser);
      }
    }
  }

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
      if (isPlatformBrowser(this.platformId)) {
        localStorage.setItem('usuario_actual', JSON.stringify(usuario));
      }
      return usuario;
    }
    return null;
  }

  /**
   * Cierra la sesion del usuario actual.
   */
  logout(): void {
    this.usuarioAutenticado = null;
    if (isPlatformBrowser(this.platformId)) {
      localStorage.removeItem('usuario_actual');
    }
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
