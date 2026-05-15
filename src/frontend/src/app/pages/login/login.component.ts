/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para la gestión de acceso al sistema (Login).
 */
import { Component } from '@angular/core';
import { Router } from '@angular/router';
import { FormsModule } from '@angular/forms';
import { AuthService } from '../../services/auth.service';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [FormsModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent {
  /** Correo electrónico introducido en el formulario */
  email: string = '';
  /** Contraseña introducida en el formulario */
  password: string = '';
  /** Mensaje de error a mostrar si falla la validación */
  error: string = '';

  /**
   * Inyecta los servicios necesarios para la navegación y validación.
   * @param router Servicio de enrutamiento de Angular.
   * @param authService Servicio de gestión de usuarios y autenticación.
   */
  constructor(private router: Router, private authService: AuthService) {}

  /**
   * Procesa el intento de inicio de sesión.
   * Si es exitoso, redirige a la pantalla de acceso. Si falla, muestra un error.
   */
  login() {
    const usuario = this.authService.login(this.email, this.password);
    if (usuario) {
      this.router.navigate(['/acceso']);
    } else {
      this.error = 'Credenciales incorrectas. Inténtalo de nuevo.';
    }
  }
}
