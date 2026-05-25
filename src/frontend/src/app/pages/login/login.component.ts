/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para la gestión de acceso al sistema (Login) mediante Google Sign-In.
 */
import { Component, OnInit, OnDestroy } from '@angular/core';
import { Router } from '@angular/router';
import { SocialAuthService, SocialUser, SocialLoginModule, GoogleSigninButtonModule } from '@abacritt/angularx-social-login';
import { AuthService } from '../../services/auth.service';
import { Subscription } from 'rxjs';

@Component({
  selector: 'app-login',
  standalone: true,
  imports: [GoogleSigninButtonModule],
  templateUrl: './login.component.html',
  styleUrl: './login.component.css'
})
export class LoginComponent implements OnInit, OnDestroy {
  error: string = '';
  private sub!: Subscription;

  constructor(
    private router: Router, 
    private authService: AuthService,
    private socialAuthService: SocialAuthService
  ) {}

  ngOnInit() {
    this.sub = this.socialAuthService.authState.subscribe((user: SocialUser) => {
      if (user?.idToken) {
        this.authService.loginConGoogle(user.idToken).subscribe({
          next: (res) => {
            this.router.navigate(['/acceso']);
          },
          error: (err) => {
            console.error('Error al hacer login con Google', err);
            this.error = 'No se pudo iniciar sesión con Google.';
          }
        });
      }
    });
  }

  ngOnDestroy() {
    if (this.sub) this.sub.unsubscribe();
  }
}
