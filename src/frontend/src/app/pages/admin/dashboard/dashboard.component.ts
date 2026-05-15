/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para el componente de Dashboard.
 */
import { Component, OnInit } from '@angular/core';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';
import { SidebarComponent } from '../../../shared/layout/sidebar/sidebar.component';
import { AuthService } from '../../../services/auth.service';
import { Usuario } from '../../../models/usuario.model';
import { UsuarioService } from '../../../services/usuario.service';
import { Estadisticas } from '../../../models/estadisticas.model';

@Component({
  selector: 'app-dashboard',
  imports: [FooterComponent, SidebarComponent],
  templateUrl: './dashboard.component.html',
  styleUrl: './dashboard.component.css'
})
export class DashboardComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  estadisticas: Estadisticas | null = null;

  constructor(private authService: AuthService, private usuarioService: UsuarioService) {
    this.usuario_actual = this.authService.getUsuarioActual();
  }

  ngOnInit(): void {
    this.usuarioService.getEstadisticas().subscribe({
      next: (data) => {
        // Verificar que la respuesta sea un objeto válido y no un error
        if (data && !((data as any).error)) {
          this.estadisticas = data;
        } else {
          console.error('La API devolvió un formato de estadísticas inválido');
        }
      },
      error: (err) => console.error('Error al cargar estadísticas', err)
    });
  }

  get porcentajeResueltos(): number {
    if (!this.estadisticas || this.estadisticas.total_tickets == 0) return 0;
    return Math.round((this.estadisticas.tickets_resueltos / this.estadisticas.total_tickets) * 100);
  }
}
