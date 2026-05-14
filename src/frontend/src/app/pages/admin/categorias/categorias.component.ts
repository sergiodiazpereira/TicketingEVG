/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para el componente de Categorías.
 */
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';
import { SidebarComponent } from '../../../shared/layout/sidebar/sidebar.component';
import { AuthService } from '../../../services/auth.service';
import { Usuario } from '../../../models/usuario.model';
import { CategoriaService } from '../../../services/categoria.service';
import { Categoria } from '../../../models/categoria.model';

@Component({
  selector: 'app-categorias',
  standalone: true,
  imports: [CommonModule, FooterComponent, SidebarComponent],
  templateUrl: './categorias.component.html',
  styleUrl: './categorias.component.css'
})
export class CategoriasComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  categorias: Categoria[] = [];

  constructor(
    private authService: AuthService, 
    private categoriaService: CategoriaService
  ) {
    this.usuario_actual = this.authService.getUsuarioActual();
  }

  ngOnInit(): void {
    this.categoriaService.getCategorias().subscribe({
      next: (data) => {
        // Asegurar que data sea un array para evitar errores en @for
        this.categorias = Array.isArray(data) ? data : [];
      },
      error: (err) => {
        console.error('Error al cargar categorías', err);
        this.categorias = [];
      }
    });
  }
}
