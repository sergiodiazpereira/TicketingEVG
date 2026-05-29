/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para el componente Sidebar.
 */
import { Component, OnInit } from '@angular/core';
import { RouterLink, RouterLinkActive } from '@angular/router';

@Component({
  selector: 'app-sidebar',
  standalone: true,
  imports: [RouterLink, RouterLinkActive],
  templateUrl: './sidebar.component.html',
  styleUrl: './sidebar.component.css'
})
export class SidebarComponent implements OnInit {
  estaColapsado: boolean = false;
  iniciarSinTransicion: boolean = true;

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
}
