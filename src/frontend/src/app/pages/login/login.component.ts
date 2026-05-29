/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador de Login con redirección SSO a la Intranet Escolar.
 */
import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit {
  error: string = '';
  redirigiendo: boolean = false;

  constructor(
    private router: Router,
    private route: ActivatedRoute
  ) { }

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      const token = params['auth_token'] || params['token'];
      if (token) {
        // La Intranet nos ha devuelto un token: procesarlo en el callback
        this.router.navigate(['/sso-callback'], { queryParams: { token } });
      } else if (params['error'] === 'sso_failed') {
        // El SSO falló: mostrar error sin redirigir (evita el bucle)
        this.error = 'No se pudo validar tu sesión con la Intranet Escolar. Por favor, inténtalo de nuevo.';
      }
      // En cualquier otro caso: mostrar pantalla con botón, NO redirigir automáticamente
    });
  }

  /**
   * Inicia la redirección manual al portal SSO de la Intranet.
   */
  iraAlIntranet(): void {
    this.redirigiendo = true;
    window.location.href = 'https://17.daw.esvirgua.com';
  }
}
