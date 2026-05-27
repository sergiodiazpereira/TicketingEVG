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
    private route: ActivatedRoute
  ) {}

  ngOnInit() {
    this.route.queryParams.subscribe(params => {
      if (params['error'] === 'sso_failed') {
        this.error = 'No se pudo validar tu sesión con la Intranet Escolar.';
      } else {
        // REDIRECCIÓN AUTOMÁTICA E INSTANTÁNEA (Bypass de Botones)
        // Redirige directamente al flujo callback del SSO inyectando tu token real de Joseph
        const tokenEjemplo = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpYXQiOjE3Nzk5MTQwNzAsImV4cCI6MTc4MDAwMDQ3MCwiZGF0YSI6eyJpZCI6MjYsIm5vbWJyZSI6Ikpvc2VwaCIsImFwZWxsaWRvcyI6IlF1aXNwZSBBbHZhcmV6IiwiZW1haWwiOiJqb3NlcGhxYTMxMzFAZ21haWwuY29tIiwiZm90byI6Imh0dHBzOi8vbGgzLmdvb2dsZXVzZXJjb250ZW50LmNvbS9hL0FDZzhvY0lVWElDTmV0QXVtcllQRlFzNHR4Umh3bjNPQjF6QmhxcFBMZGUxRW9SSzROaEx0UT1zOTYtYyIsInJvbGVzIjpbInN1cGVyX2FkbWluIl19fQ.kodU7Vnk7qNLlve3QbGZz9zk0v4k8hHYGP2eLdxXZuo';
        
        // En producción real, aquí se redirigirá directamente a la Intranet de la escuela:
        // window.location.href = 'https://05.proyectos.esvirgua.com/';
        
        this.router.navigate(['/sso-callback'], { queryParams: { token: tokenEjemplo } });
      }
    });
  }
}
