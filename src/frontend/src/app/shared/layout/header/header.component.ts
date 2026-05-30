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
import { ConfirmacionEliminarComponent } from '../../../pages/modales/confirmacion-eliminar/confirmacion-eliminar.component';

@Component({
  selector: 'app-header',
  standalone: true,
  imports: [CommonModule, RouterModule, ConfirmacionEliminarComponent],
  templateUrl: './header.component.html',
  styleUrl: './header.component.css',
  encapsulation: ViewEncapsulation.Emulated
})
export class HeaderComponent implements OnInit {
  landingUrl = environment.ssoLandingIntranet;
  mostrarModalLogout = false;

  constructor(private authService: AuthService) {}

  ngOnInit(): void {}

  get esAdministrador(): boolean {
    const usuario = this.authService.getUsuarioActual();
    return usuario?.rol === 'administrador';
  }

  abrirModalLogout(): void {
    this.mostrarModalLogout = true;
  }

  confirmarLogout(): void {
    this.mostrarModalLogout = false;
    this.authService.logout();
  }

  cancelarLogout(): void {
    this.mostrarModalLogout = false;
  }
}
