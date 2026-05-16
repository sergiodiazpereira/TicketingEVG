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
import { ConfirmacionEliminarComponent } from '../../modales/confirmacion-eliminar/confirmacion-eliminar.component';
import { FormularioOperarioComponent } from '../../modales/formulario-operario/formulario-operario.component';

@Component({
  selector: 'app-operarios',
  standalone: true,
  imports: [CommonModule, FooterComponent, SidebarComponent, ConfirmacionEliminarComponent, FormularioOperarioComponent],
  templateUrl: './operarios.component.html',
  styleUrl: './operarios.component.css'
})
export class OperariosComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  operarios: Usuario[] = [];
  mostrarModalEliminar = false;
  mostrarModalFormulario = false;
  operarioAEditar: any = null;
  /** ID del operario seleccionado para eliminar. */
  operarioAEliminarId: number | null = null;
  operarioExpandido: number | null = null;
  /** Mensaje de feedback para el usuario (éxito o error). */
  mensajeFeedback: string | null = null;
  esMensajeError = false;

  constructor(
    private authService: AuthService,
    private usuarioService: UsuarioService
  ) {
    this.usuario_actual = this.authService.getUsuarioActual();
  }

  ngOnInit(): void {
    this.cargarOperarios();
  }

  /** Carga la lista de operarios desde la API. */
  cargarOperarios(): void {
    this.usuarioService.getOperarios().subscribe({
      next: (data) => {
        this.operarios = data.map((op: any) => ({
          ...op,
          categorias_nombres: op.categorias_nombres ? op.categorias_nombres.split(', ') : []
        }));
      },
      error: (err) => {
        console.error('Error al cargar operarios', err);
        this.mostrarMensaje('No se pudo conectar con el servidor.', true);
      }
    });
  }

  toggleCategorias(id: number) {
    if (this.operarioExpandido === id)
      this.operarioExpandido = null;
    else
      this.operarioExpandido = id;
  }

  abrirModalFormulario(operario?: any) {
    this.operarioAEditar = operario || null;
    this.mostrarModalFormulario = true;
  }

  cerrarModalFormulario() {
    this.mostrarModalFormulario = false;
    this.operarioAEditar = null;
  }

  /**
   * Recibe los datos del modal y llama al servicio para crear o actualizar.
   * @param datos Objeto con nombre, correo, rol y categorias enviado por el formulario.
   */
  guardarOperario(datos: any) {
    if (datos.id) {
      // Actualizar operario existente
      this.usuarioService.actualizarUsuario(datos.id, datos).subscribe({
        next: (res) => {
          if (res.status === 'success') {
            this.mostrarMensaje('Operario actualizado correctamente.', false);
            this.cerrarModalFormulario();
            this.cargarOperarios();
          } else {
            this.mostrarMensaje(res.message || 'Error al actualizar el operario.', true);
          }
        },
        error: () => this.mostrarMensaje('Error de conexión al actualizar.', true)
      });
    } else {
      // Crear nuevo operario
      this.usuarioService.crearUsuario(datos).subscribe({
        next: (res) => {
          if (res.status === 'success') {
            this.mostrarMensaje('Operario creado correctamente.', false);
            this.cerrarModalFormulario();
            this.cargarOperarios();
          } else {
            this.mostrarMensaje(res.message || 'Error al crear el operario.', true);
          }
        },
        error: () => this.mostrarMensaje('Error de conexión al crear.', true)
      });
    }
  }

  abrirModalEliminar(operario: any) {
    this.operarioAEliminarId = operario.id;
    this.mostrarModalEliminar = true;
  }

  cerrarModal() {
    this.mostrarModalEliminar = false;
    this.operarioAEliminarId = null;
  }

  /** Confirma y ejecuta la eliminación del operario seleccionado. */
  confirmarEliminar() {
    if (!this.operarioAEliminarId) return;
    this.usuarioService.eliminarUsuario(this.operarioAEliminarId).subscribe({
      next: (res) => {
        if (res.status === 'success') {
          this.mostrarMensaje('Operario eliminado correctamente.', false);
          this.cerrarModal();
          this.cargarOperarios();
        } else {
          this.mostrarMensaje(res.message || 'No se pudo eliminar el operario.', true);
          this.cerrarModal();
        }
      },
      error: () => this.mostrarMensaje('Error de conexión al eliminar.', true)
    });
  }

  trackByOperarioId(index: number, operario: any): number {
    return operario.id;
  }

  /** Muestra un mensaje de feedback temporal durante 4 segundos. */
  private mostrarMensaje(texto: string, esError: boolean) {
    this.mensajeFeedback = texto;
    this.esMensajeError = esError;
    setTimeout(() => this.mensajeFeedback = null, 4000);
  }
}
