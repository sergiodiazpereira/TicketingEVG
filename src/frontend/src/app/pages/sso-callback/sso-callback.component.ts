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
      const token = params['token'];
      
      if (token) {
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

}
