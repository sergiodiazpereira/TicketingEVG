/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para el componente de Operarios.
 */
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { AuthService } from '../../../services/auth.service';
import { Usuario } from '../../../models/usuario.model';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';
import { SidebarComponent } from '../../../shared/layout/sidebar/sidebar.component';
import { UsuarioService } from '../../../services/usuario.service';

@Component({
  selector: 'app-operarios',
  standalone: true,
  imports: [CommonModule, FooterComponent, SidebarComponent],
  templateUrl: './operarios.component.html',
  styleUrl: './operarios.component.css'
})
export class OperariosComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  operarios: Usuario[] = [];

  constructor(
    private authService: AuthService, 
    private usuarioService: UsuarioService
  ) {
    this.usuario_actual = this.authService.getUsuarioActual();
  }

  ngOnInit(): void {
    this.usuarioService.getOperarios().subscribe({
      next: (data) => {
        // Asegurar que data sea un array para evitar errores en @for
        this.operarios = Array.isArray(data) ? data : [];
      },
      error: (err) => {
        console.error('Error al cargar operarios', err);
        this.operarios = [];
      }
    });
  }
}
