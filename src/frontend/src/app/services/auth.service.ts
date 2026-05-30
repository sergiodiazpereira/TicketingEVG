/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio de autenticación mediante SSO conectado con la Intranet Escolar.
 */
import { Injectable, Inject, PLATFORM_ID } from '@angular/core';
import { isPlatformBrowser } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Observable, tap } from 'rxjs';
import { Usuario } from '../models/usuario.model';
import { environment } from '../../enviroments/environment';

// Descodificador JWT auxiliar
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
  private apiUrl = environment.apiUrl;
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
   * Valida el token de la intranet y sincroniza la sesión local en el backend.
   * 
   * @param tokenIntranet Token provisto por la Intranet.
   */
  loginConSSO(tokenIntranet: string): Observable<{ status: string, token: string, usuario: Usuario }> {
    return this.http.post<{ status: string, token: string, usuario: Usuario }>(
      `${this.apiUrl}?entidad=auth&accion=sso`,
      { token: tokenIntranet }
    ).pipe(
      tap(res => {
        if (res && res.token && isPlatformBrowser(this.platformId)) {
          // Guardar sólo el JWT interno de TicketingEVG. El token de la Intranet es de un solo uso.
          localStorage.setItem('token', res.token);
          this.usuarioAutenticado = res.usuario;
        }
      })
    );
  }

  /**
   * Cierra la sesión activa en el navegador.
   */
  logout(): void {
    this.usuarioAutenticado = null;
    if (isPlatformBrowser(this.platformId)) {
      localStorage.removeItem('token');
      window.location.href = 'https://17.daw.esvirgua.com';
    }
  }

  /**
   * Obtiene el perfil de datos del usuario autenticado localmente.
   */
  getUsuarioActual(): Usuario | null {
    return this.usuarioAutenticado;
  }

  /**
   * Valida si el usuario actual tiene una sesión activa y vigente.
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
