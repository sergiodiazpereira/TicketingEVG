/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para gestionar las Categorías del sistema de ticketing.
 */
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';
import { SidebarComponent } from '../../../shared/layout/sidebar/sidebar.component';
import { AuthService } from '../../../services/auth.service';
import { Usuario } from '../../../models/usuario.model';
import { Categoria } from '../../../models/categoria.model';
import { ConfirmacionEliminarComponent } from '../../modales/confirmacion-eliminar/confirmacion-eliminar.component';
import { FormularioCategoriaComponent } from '../../modales/formulario-categoria/formulario-categoria.component';
import { CategoriasService } from '../../../services/categorias.service';

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
  categoriaAEliminarId: number | null = null;

  /** Mensaje de feedback para el usuario (éxito o error). */
  mensajeFeedback: string | null = null;
  esMensajeError = false;

  constructor(
    private authService: AuthService,
    private categoriasService: CategoriasService
  ) {
    this.usuario_actual = this.authService.getUsuarioActual();
  }

  ngOnInit(): void {
    this.cargarCategorias();
  }

  /**
   * Obtiene la lista de categorías del sistema a través de la API del backend.
   */
  cargarCategorias(): void {
    this.categoriasService.obtenerCategorias().subscribe({
      next: (data: any) => this.categorias = data,
      error: (err: any) => {
        console.error('Error al cargar categorías', err);
        this.mostrarMensaje('No se pudo conectar con el servidor para cargar las categorías.', true);
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

  /**
   * Envía los datos de la categoría (nombre y descripción) al servidor para crearla o actualizarla.
   * @param datos Datos capturados en el formulario modal.
   */
  guardarCategoria(datos: any) {
    if (datos.id) {
      // Actualizar categoría existente
      this.categoriasService.actualizarCategoria(datos).subscribe({
        next: (res: any) => {
          if (res.status === 'success') {
            this.mostrarMensaje('Categoría actualizada correctamente.', false);
            this.cerrarModalFormulario();
            this.cargarCategorias();
          } else {
            this.mostrarMensaje(res.message || 'Error al actualizar la categoría.', true);
          }
        },
        error: (err: any) => {
          console.error('Error de conexión', err);
          this.mostrarMensaje('Error de conexión al intentar actualizar la categoría.', true);
        }
      });
    } else {
      // Crear nueva categoría
      this.categoriasService.crearCategoria(datos).subscribe({
        next: (res: any) => {
          if (res.status === 'success') {
            this.mostrarMensaje('Categoría creada correctamente.', false);
            this.cerrarModalFormulario();
            this.cargarCategorias();
          } else {
            this.mostrarMensaje(res.message || 'Error al crear la categoría.', true);
          }
        },
        error: (err: any) => {
          console.error('Error de conexión', err);
          this.mostrarMensaje('Error de conexión al intentar crear la categoría.', true);
        }
      });
    }
  }

  abrirModalEliminar(categoria: any) {
    this.categoriaAEliminarId = categoria.id;
    this.mostrarModalEliminar = true;
  }

  cerrarModal() {
    this.mostrarModalEliminar = false;
    this.categoriaAEliminarId = null;
  }

  /**
   * Confirma la eliminación de la categoría seleccionada si no tiene incidencias asociadas.
   */
  confirmarEliminar() {
    if (!this.categoriaAEliminarId) return;

    this.categoriasService.eliminarCategoria(this.categoriaAEliminarId).subscribe({
      next: (res: any) => {
        if (res.status === 'success') {
          this.mostrarMensaje('Categoría eliminada correctamente.', false);
          this.cerrarModal();
          this.cargarCategorias();
        } else {
          this.mostrarMensaje(res.message || 'No se pudo eliminar la categoría.', true);
          this.cerrarModal();
        }
      },
      error: (err: any) => {
        console.error('Error de conexión', err);
        this.mostrarMensaje('Error de conexión al intentar eliminar la categoría.', true);
        this.cerrarModal();
      }
    });
  }

  /** Muestra un mensaje de feedback temporal durante 4 segundos. */
  private mostrarMensaje(texto: string, esError: boolean) {
    this.mensajeFeedback = texto;
    this.esMensajeError = esError;
    setTimeout(() => this.mensajeFeedback = null, 4000);
  }
}
