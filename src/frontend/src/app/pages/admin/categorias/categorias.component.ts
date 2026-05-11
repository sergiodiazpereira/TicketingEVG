/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para el componente de Categorías.
 */
import { Component } from '@angular/core';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';
import { SidebarComponent } from '../../../shared/layout/sidebar/sidebar.component';
import { AuthService } from '../../../services/auth.service';
import { Usuario } from '../../../models/usuario.model';

@Component({
  selector: 'app-categorias',
  imports: [FooterComponent, SidebarComponent],
  templateUrl: './categorias.component.html',
  styleUrl: './categorias.component.css'
})
export class CategoriasComponent {
  usuario_actual: Usuario | null = null;

  constructor(private authService: AuthService) {
    this.usuario_actual = this.authService.getUsuarioActual();
  }
}
