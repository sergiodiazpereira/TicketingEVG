/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador de Login con redirección automática instantánea SSO (sin botones).
 */
import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit {
  error: string = '';

  constructor(
    private router: Router,
    private route: ActivatedRoute,
    private authService: AuthService
  ) { }

  ngOnInit() {
    // Si el usuario ya tiene una sesion valida, redirigir directamente sin expulsarlo
    if (this.authService.isAutenticado()) {
      this.router.navigate(['/portal-tickets']);
      return;
    }

    this.route.queryParams.subscribe(params => {
      if (params['error'] === 'sso_failed') {
        this.error = 'No se pudo validar tu sesión con la Intranet Escolar.';
      } else {
        // Retrasar 3 segundos para que el usuario pueda leer el mensaje
        setTimeout(() => {
          // Redirigir a la Intranet de la escuela (05.daw.esvirgua.com)
          window.location.href = 'https://05.daw.esvirgua.com/tfg-server/angular-tfg/dashboard-inicio';
        }, 3000);
      }
    });
  }
}
