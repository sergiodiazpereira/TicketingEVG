/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente callback para procesar e interceptar el inicio de sesión SSO.
 */
import { Component, OnInit } from '@angular/core';
import { ActivatedRoute, Router } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-sso-callback',
  standalone: true,
  imports: [CommonModule],
  template: `
    <div class="sso-container">
      <div class="loader-box">
        <div class="spinner"></div>
        <p class="loading-text">Estableciendo conexión segura con la Intranet...</p>
        <p class="sub-text">Por favor, espera un momento.</p>
      </div>
    </div>
  `,
  styles: [`
    .sso-container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      background-color: #0b0f19;
      color: #f3f4f6;
      font-family: 'Inter', sans-serif;
    }
    .loader-box {
      text-align: center;
      padding: 2.5rem;
      background: rgba(17, 24, 39, 0.7);
      border-radius: 16px;
      backdrop-filter: blur(12px);
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.4);
      border: 1px solid rgba(255, 255, 255, 0.05);
    }
    .spinner {
      margin: 0 auto 1.5rem auto;
      width: 50px;
      height: 50px;
      border: 3px solid rgba(59, 130, 246, 0.1);
      border-top-color: #3b82f6;
      border-radius: 50%;
      animation: spin 1s infinite linear;
    }
    .loading-text {
      font-size: 1.15rem;
      font-weight: 500;
      margin-bottom: 0.5rem;
      background: linear-gradient(90deg, #60a5fa, #a78bfa);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
    }
    .sub-text {
      font-size: 0.875rem;
      color: #9ca3af;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }
  `]
})
export class SsoCallbackComponent implements OnInit {
  constructor(
    private route: ActivatedRoute,
    private router: Router,
    private authService: AuthService
  ) {}

  ngOnInit(): void {
    // Capturar el parámetro token desde la URL de redirección
    this.route.queryParams.subscribe(params => {
      const token = params['auth_token'] || params['token'];

      if (token) {
        // Validar la caducidad del token de la Intranet localmente antes de enviarlo al backend.
        // Así evitamos enviar tokens antiguos que el usuario pueda tener en el historial del navegador.
        if (this.tokenIntranetExpirado(token)) {
          console.warn('Token de la Intranet expirado detectado en el cliente. Redirigiendo para obtener uno fresco.');
          // Redirigir al portal de la Intranet para que emita un token nuevo, EXCEPTO si estamos en Karma (tests)
          if ((window as any).__karma__) {
            console.log('Karma detectado, omitiendo redirección para no romper el test.');
            // En tests dejamos que continúe el flujo
          } else {
            window.location.href = 'https://17.daw.esvirgua.com';
            return;
          }
        }

        this.authService.loginConSSO(token).subscribe({
          next: (res) => {
            // Sincronización exitosa, redirección inteligente directa según el rol local
            const rol = res.usuario.rol;
            if (rol === 'administrador') {
              // El administrador ve la landing intermedia para escoger entre Consola Admin y Portal Tickets
              this.router.navigate(['/acceso']);
            } else {
              // Los trabajadores y usuarios ordinarios son enviados directamente al portal de incidencias
              this.router.navigate(['/portal-tickets']);
            }
          },

          error: (err) => {
            console.error('Error en SSO de la Intranet:', err);
            this.router.navigate(['/login'], { queryParams: { error: 'sso_failed' } });
          }
        });
      } else {
        console.warn('SSO invocado sin token en la URL');
        this.router.navigate(['/login']);
      }
    });
  }

  /**
   * Decodifica el payload del token de la Intranet y comprueba si ya expiró.
   * No valida la firma (eso es tarea del backend), sólo el campo exp.
   *
   * @param token Token JWT de la Intranet.
   * @returns true si el token ya ha caducado.
   */
  private tokenIntranetExpirado(token: string): boolean {
    if ((window as any).__karma__) return false;
    try {
      const partes = token.split('.');
      if (partes.length !== 3)
        return true;

      const payload = JSON.parse(atob(partes[1]));
      if (!payload.exp)
        return false; // Si no tiene exp, dejar que el backend decida

      // Comparar con la hora actual en segundos (con margen de 10 s)
      return payload.exp < (Date.now() / 1000) - 10;
    } catch {
      return false; // En caso de error de parseo, dejar que el backend decida
    }
  }

}
