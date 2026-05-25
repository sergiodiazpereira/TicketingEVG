/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio de autenticación con usuarios estáticos.
 */
import { Injectable, Inject, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { Usuario } from '../models/usuario.model';

// A dummy jwt-decode to avoid type errors if not fully configured
function decodeToken(token: string): any {
  try {
    return JSON.parse(atob(token.split('.')[1]));
  } catch {
    return null;
  }
}

@Injectable({
  providedIn: 'root'
})
export class AuthService {
  private apiUrl = 'http://localhost/backend/index.php'; // Adjust this URL based on actual deployment
  private usuarioAutenticado: Usuario | null = null;

  constructor(
    @Inject(PLATFORM_ID) private platformId: Object,
    private http: HttpClient
  ) {
    if (isPlatformBrowser(this.platformId)) {
      const token = localStorage.getItem('token');
      if (token && this.isTokenValid(token)) {
        const payload = decodeToken(token);
        if (payload && payload.data) {
          this.usuarioAutenticado = payload.data as Usuario;
        }
      } else {
        this.logout();
      }
    }
  }

  /**
   * Intenta iniciar sesion con el token de Google.
   */
  loginConGoogle(idToken: string): Observable<{ status: string, token: string, usuario: Usuario }> {
    return this.http.post<{ status: string, token: string, usuario: Usuario }>(
      `${this.apiUrl}?entidad=auth&accion=google`,
      { token: idToken }
    ).pipe(
      tap(res => {
        if (res && res.token && isPlatformBrowser(this.platformId)) {
          localStorage.setItem('token', res.token);
          this.usuarioAutenticado = res.usuario;
        }
      })
    );
  }

  /**
   * Cierra la sesion del usuario actual.
   */
  logout(): void {
    this.usuarioAutenticado = null;
    if (isPlatformBrowser(this.platformId)) {
      localStorage.removeItem('token');
    }
  }

  /**
   * Obtiene el usuario autenticado actualmente.
   */
  getUsuarioActual(): Usuario | null {
    return this.usuarioAutenticado;
  }

  /**
   * Verifica si hay un usuario autenticado y el token es valido.
   */
  isAutenticado(): boolean {
    if (!isPlatformBrowser(this.platformId)) return false;
    const token = localStorage.getItem('token');
    return token ? this.isTokenValid(token) : false;
  }

  private isTokenValid(token: string): boolean {
    const payload = decodeToken(token);
    if (!payload) return false;
    return payload.exp > (Date.now() / 1000);
  }
}
