/**
 * Proyecto: TicketingEVG
 * Alumno: Manuel Vega Purificación
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
import { forkJoin } from 'rxjs';
import { IconComponent } from '../../../components/icon/icon.component';

@Component({
  selector: 'app-operarios',
  standalone: true,
  imports: [CommonModule, FooterComponent, SidebarComponent, ConfirmacionEliminarComponent, FormularioOperarioComponent, IconComponent],
  templateUrl: './operarios.component.html',
  styleUrl: './operarios.component.css'
})
export class OperariosComponent implements OnInit {
  usuario_actual: Usuario | null = null;
  operarios: Usuario[] = [];
  mostrarModalEliminar = false;
  mostrarModalFormulario = false;
  operarioAEditar: any = null;
  operarioAEliminarId: number | null = null;
  operarioExpandido: number | null = null;

  // Propiedades para eliminación masiva
  operariosSeleccionados: number[] = [];
  mostrarModalEliminarVarios = false;

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
    const operario = this.operarios.find(op => op.id === id);
    if (!operario || !operario.categorias_nombres || operario.categorias_nombres.length === 0) {
      return;
    }
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
    if (this.operarioAEditar) {
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

  // Métodos para selección masiva y eliminación en bloque de operarios
  toggleSeleccion(id: number): void {
    const index = this.operariosSeleccionados.indexOf(id);
    if (index > -1) {
      this.operariosSeleccionados.splice(index, 1);
    } else {
      this.operariosSeleccionados.push(id);
    }
  }

  isSeleccionada(id: number): boolean {
    return this.operariosSeleccionados.includes(id);
  }

  estanTodosSeleccionados(): boolean {
    return this.operarios.length > 0 && this.operariosSeleccionados.length === this.operarios.length;
  }

  toggleSeleccionarTodos(): void {
    if (this.estanTodosSeleccionados()) {
      this.operariosSeleccionados = [];
    } else {
      this.operariosSeleccionados = this.operarios.map(op => op.id);
    }
  }

  abrirModalEliminarVarios() {
    if (this.operariosSeleccionados.length === 0) return;
    this.mostrarModalEliminarVarios = true;
  }

  cerrarModalEliminarVarios() {
    this.mostrarModalEliminarVarios = false;
  }

  confirmarEliminarVarios() {
    if (this.operariosSeleccionados.length === 0) return;
    
    // Ejecutar la petición para cada operario seleccionado
    const llamadas = this.operariosSeleccionados.map(id => this.usuarioService.eliminarUsuario(id));
    
    forkJoin(llamadas).subscribe({
      next: (resultados: any[]) => {
        let exitos = 0;
        let fallidos = 0;
        let mensajeErrorUltimo = '';

        resultados.forEach((res) => {
          if (res.status === 'success') {
            exitos++;
          } else {
            fallidos++;
            mensajeErrorUltimo = res.message || '';
          }
        });

        this.operariosSeleccionados = [];
        this.cerrarModalEliminarVarios();
        this.cargarOperarios();

        if (exitos > 0 && fallidos === 0) {
          this.mostrarMensaje(`Se han revocado los permisos de ${exitos} operarios correctamente.`, false);
        } else if (exitos > 0 && fallidos > 0) {
          this.mostrarMensaje(`Se revocaron ${exitos} operarios, pero ${fallidos} no pudieron modificarse (por tickets activos asignados).`, true);
        } else {
          this.mostrarMensaje(mensajeErrorUltimo || `No se pudo revocar a ninguno de los ${fallidos} operarios seleccionados.`, true);
        }
      },
      error: (err: any) => {
        console.error('Error en revocación múltiple', err);
        this.mostrarMensaje('Error de conexión al intentar revocar los permisos de los operarios.', true);
        this.cerrarModalEliminarVarios();
      }
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
