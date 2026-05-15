import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';
import { SidebarComponent } from '../../../shared/layout/sidebar/sidebar.component';
import { AuthService } from '../../../services/auth.service';
import { Usuario } from '../../../models/usuario.model';
import { CategoriaService } from '../../../services/categoria.service';
import { Categoria } from '../../../models/categoria.model';
import { ConfirmacionEliminarComponent } from '../../modales/confirmacion-eliminar/confirmacion-eliminar.component';
import { FormularioCategoriaComponent } from '../../modales/formulario-categoria/formulario-categoria.component';

@Component({
  selector: 'app-categorias',
  standalone: true,
  imports: [CommonModule, FooterComponent, SidebarComponent, ConfirmacionEliminarComponent, FormularioCategoriaComponent],
  templateUrl: './categorias.component.html',
  styleUrl: './categorias.component.css'
})
export class CategoriasComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  categorias: Categoria[] = [];
  mostrarModalEliminar = false;
  mostrarModalFormulario = false;
  categoriaAEditar: any = null;

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
        this.categorias = Array.isArray(data) && data.length > 0 ? data : [
          { id: 1, nombre: 'Soporte Nivel 1', descripcion: '', operarios: 8, tickets: 15 },
          { id: 2, nombre: 'Soporte Nivel 2', descripcion: '', operarios: 5, tickets: 8 }
        ] as any;
      },
      error: (err) => {
        console.error('Error al cargar categorías', err);
        this.categorias = [
          { id: 1, nombre: 'Soporte Nivel 1', descripcion: '', operarios: 8, tickets: 15 },
          { id: 2, nombre: 'Soporte Nivel 2', descripcion: '', operarios: 5, tickets: 8 }
        ] as any;
      }
    });
  }

  abrirModalFormulario(categoria?: any) {
    this.categoriaAEditar = categoria || null;
    this.mostrarModalFormulario = true;
  }

  cerrarModalFormulario() {
    this.mostrarModalFormulario = false;
    this.categoriaAEditar = null;
  }

  guardarCategoria() {
    console.log('Categoría guardada (Simulación)', this.categoriaAEditar);
    this.cerrarModalFormulario();
  }

  abrirModalEliminar() {
    this.mostrarModalEliminar = true;
  }

  cerrarModal() {
    this.mostrarModalEliminar = false;
  }

  confirmarEliminar() {
    console.log('Categoría eliminada (Simulación)');
    this.cerrarModal();
  }
}

