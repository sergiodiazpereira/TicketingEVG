/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para el componente Sidebar.
 */
import { Component, OnInit } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../../services/auth.service';
import { environment } from '../../../../enviroments/environment';
import { ConfirmacionEliminarComponent } from '../../../pages/modales/confirmacion-eliminar/confirmacion-eliminar.component';
import { IconComponent } from '../../../components/icon/icon.component';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [RouterLink, RouterLinkActive, CommonModule, ConfirmacionEliminarComponent, IconComponent],
  templateUrl: './sidebar.component.html',
  styleUrl: './sidebar.component.css'
})
export class SidebarComponent implements OnInit {
  landingUrl = environment.ssoLandingIntranet;
  estaColapsado: boolean = false;
  iniciarSinTransicion: boolean = true;
  mostrarModalLogout = false;

  constructor(private authService: AuthService) {}

  ngOnInit() {
    if (typeof window !== 'undefined' && window.localStorage) {
      this.estaColapsado = localStorage.getItem('sidebarCollapsed') === 'true';
    }
    // Desactivar el bloqueo de transiciones después de que se monte la vista inicial
    setTimeout(() => {
      this.iniciarSinTransicion = false;
    }, 100);
  }

  toggleSidebar() {
    this.estaColapsado = !this.estaColapsado;
    if (typeof window !== 'undefined' && window.localStorage) {
      localStorage.setItem('sidebarCollapsed', String(this.estaColapsado));
    }
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
