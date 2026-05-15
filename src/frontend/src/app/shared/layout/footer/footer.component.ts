/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para el componente Footer.
 */
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';

interface UserData {
  nombre?: string;
  email?: string;
  foto?: string;
  rol?: string;
}

@Component({
  selector: 'app-footer',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './footer.component.html',
  styleUrl: './footer.component.css'
})
export class FooterComponent implements OnInit {
  nombre: string | null = null;
  email: string | null = null;
  foto: string | null = null;
  rol: string | null = null;
  
  rolLabel: string | null = null;
  rolClass: string = '';
  
  currentYear = new Date().getFullYear();

  private roles: { [key: string]: [string, string] } = {
    'admin': ['Administrador', 'badge-admin'],
    'profesor': ['Profesor/a', 'badge-profesor'],
    'alumno': ['Alumno/a', 'badge-alumno'],
    'staff': ['Personal', 'badge-staff'],
  };

  ngOnInit(): void {
    this.loadUserData();
  }

  private loadUserData(): void {
    const token = this.getCookie('auth_token');
    if (token) {
      const payload = this.obtenerPayloadJwt(token);
      if (payload && payload.data) {
        const datos = payload.data as UserData;
        this.nombre = datos.nombre || null;
        this.email = datos.email || null;
        this.foto = datos.foto || null;
        this.rol = datos.rol || null;
        
        if (this.rol && this.roles[this.rol]) {
          this.rolLabel = this.roles[this.rol][0];
          this.rolClass = this.roles[this.rol][1];
        } else if (this.rol) {
          this.rolLabel = this.rol;
          this.rolClass = '';
        }
      }
    }
  }

  private getCookie(name: string): string | null {
    if (typeof document === 'undefined') return null; // Prevenir errores en SSR
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) {
      return parts.pop()?.split(';').shift() || null;
    }
    return null;
  }

  private obtenerPayloadJwt(token: string): any {
    try {
      const partes = token.split('.');
      if (partes.length !== 3) return null;

      let b64 = partes[1].replace(/-/g, '+').replace(/_/g, '/');
      const pad = b64.length % 4;
      if (pad) {
        b64 += new Array(5 - pad).join('=');
      }

      // decodeURIComponent(escape(atob())) maneja caracteres utf-8 mejor
      const raw = decodeURIComponent(escape(atob(b64)));
      return JSON.parse(raw);
    } catch (e) {
      return null;
    }
  }
}
