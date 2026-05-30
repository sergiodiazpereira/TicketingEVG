/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente de cabecera (Header) unificado y compartido para el portal de usuarios.
 */
import { Component, OnInit, ViewEncapsulation } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule } from '@angular/router';
import { AuthService } from '../../../services/auth.service';
import { environment } from '../../../../enviroments/environment';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, RouterModule],
  templateUrl: './header.component.html',
  styleUrl: './header.component.css',
  encapsulation: ViewEncapsulation.Emulated
})
export class HeaderComponent implements OnInit {
  landingUrl = environment.ssoLandingIntranet;

  constructor(private authService: AuthService) {}

  ngOnInit(): void {}

  get esAdministrador(): boolean {
    const usuario = this.authService.getUsuarioActual();
    return usuario?.rol === 'administrador';
  }

  logout(): void {
    this.authService.logout();
  }
}
