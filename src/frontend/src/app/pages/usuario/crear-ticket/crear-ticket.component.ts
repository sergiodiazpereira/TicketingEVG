/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente para crear un nuevo ticket de soporte.
 */
import { Component, OnInit, HostListener } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { TicketService } from '../../../services/ticket.service';
import { AuthService } from '../../../services/auth.service';
import { CategoriasService } from '../../../services/categorias.service';
import { UsuarioService } from '../../../services/usuario.service';
import { Categoria } from '../../../models/categoria.model';
import { Usuario } from '../../../models/usuario.model';

@Component({
  selector: 'app-crear-ticket',
  standalone: true,
  imports: [CommonModule, RouterModule, ReactiveFormsModule],
  templateUrl: './crear-ticket.component.html',
  styleUrl: './crear-ticket.component.css'
})
export class CrearTicketComponent implements OnInit {
  formulario!: FormGroup;
  enviando = false;
  mensajeError: string | null = null;
  mensajeExito: string | null = null;

  /** Estado de los desplegables personalizados */
  desplegableTipoAbierto = false;
  desplegablePrioridadAbierto = false;
  desplegableCategoriaAbierto = false;

  /** Fecha mínima para el selector de fecha límite (hoy) */
  fechaMinima = '';

  /** Mapa de prioridad: valor del select → código de la BD */
  readonly PRIORIDAD_MAP: Record<string, string> = {
    baja: 'b',
    media: 'm',
    alta: 'a'
  };

  /** Categorías cargadas dinámicamente desde la BD */
  categorias: Categoria[] = [];
  cargandoCategorias = true;

  /** Operarios para administradores o responsables */
  operarios: Usuario[] = [];
  esAdminOResponsable = false;

  constructor(
    private fb: FormBuilder,
    private ticketService: TicketService,
    private authService: AuthService,
    private categoriasService: CategoriasService,
    private usuarioService: UsuarioService,
    private router: Router
  ) {}

  ngOnInit(): void {
    const hoy = new Date();
    const anio = hoy.getFullYear();
    const mes = String(hoy.getMonth() + 1).padStart(2, '0');
    const dia = String(hoy.getDate()).padStart(2, '0');
    this.fechaMinima = `${anio}-${mes}-${dia}`;

    this.formulario = this.fb.group({
      tipo: ['incidencia', Validators.required],
      prioridad: ['media', Validators.required],
      id_categoria: ['', Validators.required],
      id_usuario_encargado: [''],
      ubicacion: [''],
      fecha_limite: [''],
      titulo: ['', [Validators.required, Validators.minLength(5)]],
      descripcion: ['', [Validators.required, Validators.minLength(10)]]
    });

    // Cargar categorías desde la BD
    this.categoriasService.obtenerCategorias().subscribe({
      next: (data: any) => {
        this.categorias = data;
        this.cargandoCategorias = false;
      },
      error: () => {
        this.cargandoCategorias = false;
        this.mensajeError = 'No se pudieron cargar las categorías.';
      }
    });

    // Cargar operarios si el usuario actual es admin o responsable
    const usuario = this.authService.getUsuarioActual();
    if (usuario && (usuario.rol === 'admin' || usuario.rol === 'responsable')) {
      this.esAdminOResponsable = true;
      this.usuarioService.getOperarios().subscribe({
        next: (data: any) => this.operarios = data,
        error: () => console.error('Error al cargar operarios')
      });
    }
  }

  /** Envía el formulario al backend si es válido. */
  onEnviar(): void {
    if (this.formulario.invalid) {
      this.formulario.markAllAsTouched();
      this.mensajeError = 'Por favor, rellena todos los campos obligatorios.';
      return;
    }

    const usuario = this.authService.getUsuarioActual();
    if (!usuario) {
      this.mensajeError = 'No hay sesión activa. Por favor, inicia sesión de nuevo.';
      return;
    }

    const valores = this.formulario.value;
    const payload: any = {
      tipo: valores.tipo,
      prioridad: this.PRIORIDAD_MAP[valores.prioridad] ?? 'm',
      id_categoria: valores.id_categoria,
      titulo: valores.titulo,
      descripcion: valores.descripcion,
      id_usuario_creador: usuario.id,
      estado: 'pendiente',
      ubicacion: valores.ubicacion || null,
      fecha_prevista: valores.fecha_limite || null
    };

    if (this.esAdminOResponsable && valores.id_usuario_encargado) {
      payload.id_usuario_encargado = parseInt(valores.id_usuario_encargado);
    }

    this.enviando = true;
    this.mensajeError = null;

    this.ticketService.crearTicket(payload).subscribe({
      next: (res) => {
        this.enviando = false;
        if (res.status === 'success') {
          this.mensajeExito = `Ticket ${res.id} creado correctamente. Redirigiendo...`;
          setTimeout(() => this.router.navigate(['/portal-tickets/tickets']), 1500);
        } else {
          this.mensajeError = res.message || 'Error al crear el ticket.';
        }
      },
      error: () => {
        this.enviando = false;
        this.mensajeError = 'Error de conexión con el servidor. Inténtalo de nuevo.';
      }
    });
  }

  toggleDesplegableTipo(event: Event): void {
    event.stopPropagation();
    this.desplegableTipoAbierto = !this.desplegableTipoAbierto;
    this.desplegablePrioridadAbierto = false;
    this.desplegableCategoriaAbierto = false;
  }

  toggleDesplegablePrioridad(event: Event): void {
    event.stopPropagation();
    this.desplegablePrioridadAbierto = !this.desplegablePrioridadAbierto;
    this.desplegableTipoAbierto = false;
    this.desplegableCategoriaAbierto = false;
  }

  toggleDesplegableCategoria(event: Event): void {
    event.stopPropagation();
    this.desplegableCategoriaAbierto = !this.desplegableCategoriaAbierto;
    this.desplegableTipoAbierto = false;
    this.desplegablePrioridadAbierto = false;
  }

  seleccionarTipo(tipo: string): void {
    this.formulario.get('tipo')?.setValue(tipo);
    this.desplegableTipoAbierto = false;
  }

  seleccionarPrioridad(prioridad: string): void {
    this.formulario.get('prioridad')?.setValue(prioridad);
    this.desplegablePrioridadAbierto = false;
  }

  seleccionarCategoria(id: number): void {
    this.formulario.get('id_categoria')?.setValue(id);
    this.formulario.get('id_categoria')?.markAsTouched();
    this.desplegableCategoriaAbierto = false;
  }

  getTipoEtiqueta(): string {
    const val = this.formulario.get('tipo')?.value;
    return val === 'peticion' ? 'PETICIÓN DE SERVICIO' : 'INCIDENCIA';
  }

  getPrioridadEtiqueta(): string {
    const val = this.formulario.get('prioridad')?.value;
    const mapa: Record<string, string> = {
      baja: 'BAJA',
      media: 'MEDIA',
      alta: 'ALTA'
    };
    return mapa[val] || 'SELECCIONA PRIORIDAD';
  }

  getCategoriaEtiqueta(): string {
    if (this.cargandoCategorias) return 'Cargando categorías...';
    const id = this.formulario.get('id_categoria')?.value;
    const cat = this.categorias.find(c => c.id === id);
    return cat ? cat.nombre : 'Selecciona una categoría';
  }

  @HostListener('document:click', ['$event'])
  cerrarDesplegables(): void {
    this.desplegableTipoAbierto = false;
    this.desplegablePrioridadAbierto = false;
    this.desplegableCategoriaAbierto = false;
  }
}

