import { Component, EventEmitter, Input, Output, HostListener, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { FormsModule } from '@angular/forms';
import { TicketService } from '../../../services/ticket.service';
import { CategoriasService } from '../../../services/categorias.service';
import { ConfirmacionEliminarComponent } from '../confirmacion-eliminar/confirmacion-eliminar.component';

/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente unificado para mostrar los detalles de un ticket en un modal.
 */
@Component({
  selector: 'app-modal-ticket',
  standalone: true,
  imports: [CommonModule, FormsModule, ConfirmacionEliminarComponent],
  templateUrl: './modal-ticket.component.html',
  styleUrl: './modal-ticket.component.css',
  host: {
    '[class.modal-closing]': 'isClosing'
  }
})
export class ModalTicketComponent implements OnInit {
  @Input() ticket: any;
  @Input() rolUsuario: string = 'profesor'; // 'profesor', 'responsable', 'trabajador', 'alumno'
  @Input() operarios: any[] = []; // Lista de operarios para que el responsable asigne

  @Output() cerrar = new EventEmitter<void>();
  @Output() resolver = new EventEmitter<any>();
  @Output() asignar = new EventEmitter<{idTicket: number, idOperario: number}>();

  /** Feedback interno del modal */
  mensajeFeedback: string | null = null;
  esMensajeError = false;
  procesando = false;
  isClosing = false;
  mostrarConfirmacionDesasignar = false;
  mostrarConfirmacionCancelar = false;

  /** Variables para el editor dinámico del ticket */
  categorias: any[] = [];
  editando = false;
  tituloEditado = '';
  descripcionEditada = '';
  ubicacionEditada = '';
  categoriaEditada = 0;
  prioridadEditada = '';

  /** Variables para la sección de comentarios */
  comentariosList: any[] = [];
  nuevoComentarioText = '';

  constructor(
    private ticketService: TicketService,
    private categoriasService: CategoriasService
  ) {}

  ngOnInit() {
    this.categoriasService.obtenerCategorias().subscribe({
      next: (res: any) => this.categorias = res,
      error: (err) => console.error('Error al cargar categorías', err)
    });
    this.cargarComentarios();
  }

  cargarComentarios() {
    if (!this.ticket || !this.ticket.id) return;
    this.ticketService.obtenerComentarios(this.ticket.id).subscribe({
      next: (res: any) => {
        if (res.status === 'success') {
          this.comentariosList = res.data;
        }
      },
      error: (err) => console.error('Error al cargar comentarios', err)
    });
  }

  mostrarChatMovil = false;

  @HostListener('click')
  onHostClick() {
    this.onCerrar();
  }

  toggleChatMovil(event?: Event) {
    if (event) {
      event.stopPropagation();
    }
    this.mostrarChatMovil = !this.mostrarChatMovil;
  }

  onCerrar() {
    this.mostrarChatMovil = false;
    this.isClosing = true;
    setTimeout(() => {
      this.cerrar.emit();
    }, 200);
  }

  /**
   * Marca el ticket como resuelto llamando directamente a la API.
   * Disponible para cualquier rol mientras el ticket no esté ya resuelto.
   */
  onResolver() {
    this.procesando = true;
    this.ticketService.actualizarEstado(this.ticket.id, 'resuelto').subscribe({
      next: (res) => {
        this.procesando = false;
        if (res.status === 'success') {
          this.ticket.estado = 'resuelto';
          this.resolver.emit(this.ticket);
          this.mostrarMensaje('Ticket marcado como resuelto.', false);
        } else {
          this.mostrarMensaje(res.message || 'Error al resolver el ticket.', true);
        }
      },
      error: () => {
        this.procesando = false;
        this.mostrarMensaje('Error de conexión al resolver el ticket.', true);
      }
    });
  }

  onEnProceso() {
    this.procesando = true;
    this.ticketService.actualizarEstado(this.ticket.id, 'proceso').subscribe({
      next: (res) => {
        this.procesando = false;
        if (res.status === 'success') {
          this.ticket.estado = 'proceso';
          this.resolver.emit(this.ticket);
          this.mostrarMensaje('Ticket marcado como en proceso.', false);
        } else {
          this.mostrarMensaje(res.message || 'Error al iniciar proceso.', true);
        }
      },
      error: () => {
        this.procesando = false;
        this.mostrarMensaje('Error de conexión al iniciar proceso.', true);
      }
    });
  }

  /**
   * Cancela el ticket. Abre el modal de confirmación.
   */
  onCancelar() {
    this.mostrarConfirmacionCancelar = true;
  }

  confirmarCancelar() {
    this.mostrarConfirmacionCancelar = false;
    this.procesando = true;
    this.ticketService.actualizarEstado(this.ticket.id, 'no aplica').subscribe({
      next: (res: any) => {
        this.procesando = false;
        if (res.status === 'success') {
          this.ticket.estado = 'no aplica';
          this.resolver.emit(this.ticket);
          this.mostrarMensaje('Ticket cancelado correctamente.', false);
        } else {
          this.mostrarMensaje(res.message || 'Error al cancelar el ticket.', true);
        }
      },
      error: () => {
        this.procesando = false;
        this.mostrarMensaje('Error de conexión.', true);
      }
    });
  }

  onDesasignar(event: MouseEvent) {
    event.stopPropagation();
    this.mostrarConfirmacionDesasignar = true;
  }

  confirmarDesasignar() {
    this.mostrarConfirmacionDesasignar = false;
    this.procesando = true;
    this.ticketService.asignarTicket(this.ticket.id.toString(), 0).subscribe({
      next: (res: any) => {
        this.procesando = false;
        if (res.status === 'success') {
          this.ticket.id_usuario_encargado = null;
          this.ticket.encargado_nombre = null;
          this.ticket.estado = 'pendiente';
          this.resolver.emit(this.ticket);
          this.mostrarMensaje('Técnico desasignado correctamente.', false);
        } else {
          this.mostrarMensaje(res.message || 'Error al desasignar el técnico.', true);
        }
      },
      error: () => {
        this.procesando = false;
        this.mostrarMensaje('Error de conexión al desasignar.', true);
      }
    });
  }

  onAsignar(event: any) {
    const idOperario = event.target.value;
    if (idOperario)
      this.asignar.emit({ idTicket: this.ticket.id, idOperario: parseInt(idOperario) });
  }

  get nombreEncargado(): string {
    if (!this.ticket || !this.ticket.id_usuario_encargado) {
      return '';
    }
    const tech = this.operarios.find(op => Number(op.id) === Number(this.ticket.id_usuario_encargado));
    return tech ? tech.nombre : `TRABAJADOR-${this.ticket.id_usuario_encargado}`;
  }

  get operariosFiltrados(): any[] {
    if (!this.ticket || !this.ticket.id_categoria) {
      return this.operarios;
    }
    const idCat = Number(this.ticket.id_categoria);
    return this.operarios.filter(op => {
      if (!op.categorias_ids) return false;
      const ids = String(op.categorias_ids).split(',').map(Number);
      return ids.includes(idCat);
    });
  }

  get esTecnico(): boolean {
    return this.rolUsuario === 'administrador' || 
           this.rolUsuario === 'responsable' || 
           this.rolUsuario === 'trabajador';
  }

  get puedeEditar(): boolean {
    if (!this.ticket || this.ticket.estado === 'resuelto' || this.ticket.estado === 'no aplica') {
      return false;
    }
    if (this.esTecnico) {
      return true;
    }
    return (this.ticket.estado === 'pendiente' || this.ticket.estado === 'asignado');
  }

  onActivarEdicion() {
    if (!this.puedeEditar) return;
    this.editando = true;
    this.tituloEditado = this.ticket.titulo;
    this.descripcionEditada = this.ticket.descripcion;
    this.ubicacionEditada = this.ticket.ubicacion || '';
    this.categoriaEditada = Number(this.ticket.id_categoria);
    this.prioridadEditada = this.ticket.prioridad;
  }

  onCancelarEdicion() {
    this.editando = false;
  }

  onGuardarEdicion() {
    if (!this.tituloEditado || this.tituloEditado.trim().length < 5) {
      this.mostrarMensaje('El título debe tener al menos 5 caracteres.', true);
      return;
    }
    if (!this.descripcionEditada || this.descripcionEditada.trim().length < 10) {
      this.mostrarMensaje('La descripción debe tener al menos 10 caracteres.', true);
      return;
    }
    if (!this.categoriaEditada) {
      this.mostrarMensaje('Por favor, selecciona una categoría.', true);
      return;
    }

    this.procesando = true;
    const payload: any = {
      titulo: this.tituloEditado,
      descripcion: this.descripcionEditada,
      id_categoria: this.categoriaEditada,
      prioridad: this.prioridadEditada,
      ubicacion: this.ubicacionEditada || null
    };

    this.ticketService.actualizarTicket(this.ticket.id.toString(), payload).subscribe({
      next: (res: any) => {
        this.procesando = false;
        if (res.status === 'success') {
          this.ticket.titulo = this.tituloEditado;
          this.ticket.descripcion = this.descripcionEditada;
          this.ticket.id_categoria = this.categoriaEditada;
          this.ticket.prioridad = this.prioridadEditada;
          this.ticket.ubicacion = this.ubicacionEditada;
          
          // Map category name
          const cat = this.categorias.find(c => Number(c.id) === Number(this.categoriaEditada));
          if (cat) {
            this.ticket.categoria_nombre = cat.nombre;
          }
          
          this.editando = false;
          this.resolver.emit(this.ticket);
          this.mostrarMensaje('Ticket actualizado correctamente.', false);
        } else {
          this.mostrarMensaje(res.message || 'Error al actualizar el ticket.', true);
        }
      },
      error: () => {
        this.procesando = false;
        this.mostrarMensaje('Error de conexión al guardar cambios.', true);
      }
    });
  }

  onAgregarComentario() {
    if (!this.nuevoComentarioText || !this.nuevoComentarioText.trim()) return;
    const texto = this.nuevoComentarioText.trim();
    this.procesando = true;
    this.ticketService.guardarComentario(this.ticket.id, texto).subscribe({
      next: (res: any) => {
        this.procesando = false;
        if (res.status === 'success') {
          this.nuevoComentarioText = '';
          this.cargarComentarios();
        } else {
          this.mostrarMensaje(res.message || 'Error al añadir comentario.', true);
        }
      },
      error: () => {
        this.procesando = false;
        this.mostrarMensaje('Error de conexión al añadir comentario.', true);
      }
    });
  }

  onKeydownEnter(event: any) {
    if (!event.shiftKey) {
      event.preventDefault();
      this.onAgregarComentario();
    }
  }

  private mostrarMensaje(texto: string, esError: boolean) {
    this.mensajeFeedback = texto;
    this.esMensajeError = esError;
    setTimeout(() => this.mensajeFeedback = null, 4000);
  }
}
