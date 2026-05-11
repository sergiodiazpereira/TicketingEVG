/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para el componente de Operarios.
 */
import { Component } from '@angular/core';
import { AuthService } from '../../../services/auth.service';
import { Usuario } from '../../../models/usuario.model';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';
import { SidebarComponent } from '../../../shared/layout/sidebar/sidebar.component';

@Component({
  selector: 'app-operarios',
  imports: [FooterComponent, SidebarComponent],
  templateUrl: './operarios.component.html',
  styleUrl: './operarios.component.css'
})
export class OperariosComponent {
  usuario_actual: Usuario | null = null;

  constructor(private authService: AuthService) {
    this.usuario_actual = this.authService.getUsuarioActual();
  }
}
