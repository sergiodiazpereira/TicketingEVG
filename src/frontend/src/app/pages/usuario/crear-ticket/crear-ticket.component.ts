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
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators, AbstractControl, ValidationErrors } from '@angular/forms';
import { TicketService } from '../../../services/ticket.service';
import { AuthService } from '../../../services/auth.service';
import { CategoriasService } from '../../../services/categorias.service';
import { UsuarioService } from '../../../services/usuario.service';
import { Categoria } from '../../../models/categoria.model';
import { Usuario } from '../../../models/usuario.model';
import { environment } from '../../../../enviroments/environment';
import { HeaderComponent } from '../../../shared/layout/header/header.component';
import { IconComponent } from '../../../components/icon/icon.component';
import { ToastService } from '../../../services/toast.service';

@Component({
  selector: 'app-crear-ticket',
  standalone: true,
  imports: [CommonModule, RouterModule, ReactiveFormsModule, HeaderComponent, IconComponent],
  templateUrl: './crear-ticket.component.html',
  styleUrl: './crear-ticket.component.css'
})
export class CrearTicketComponent implements OnInit {
  landingUrl = environment.ssoLandingIntranet;
  formulario!: FormGroup;
  enviando = false;
  mensajeError: string | null = null;
  mensajeExito: string | null = null;

  /** Estado de los desplegables personalizados */
  desplegableTipoAbierto = false;
  desplegablePrioridadAbierto = false;
  desplegableCategoriaAbierto = false;
  desplegableEncargadoAbierto = false;

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
    private router: Router,
    private toastService: ToastService
  ) {}

  get isAdministrador(): boolean {
    return this.authService.getUsuarioActual()?.rol === 'administrador';
  }

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
      ubicacion: ['', this.trimmedUbicacionValidator()],
      fecha_limite: [''],
      titulo: ['', [this.trimmedValidator(5)]],
      descripcion: ['', [this.trimmedValidator(10)]]
    });

    // Cargar categorías desde la BD
    this.categoriasService.obtenerCategorias().subscribe({
      next: (data: any) => {
        this.categorias = data;
        this.cargandoCategorias = false;
      },
      error: () => {
        this.cargandoCategorias = false;
        this.mostrarMensajeError('No se pudieron cargar las categorías.');
      }
    });

    // Cargar operarios si el usuario actual es admin o responsable
    const usuario = this.authService.getUsuarioActual();
    if (usuario && (usuario.rol === 'administrador' || usuario.rol === 'responsable')) {
      this.esAdminOResponsable = true;
      this.usuarioService.getOperarios().subscribe({
        next: (data: any) => this.operarios = data,
        error: () => console.error('Error al cargar operarios')
      });
    }
  }

  trimmedValidator(minLength: number) {
    return (control: AbstractControl): ValidationErrors | null => {
      const value = control.value;
      if (value === null || value === undefined || value === '') {
        return { 'required': true };
      }
      const trimmed = value.trim();
      if (trimmed.length === 0) {
        return { 'onlySpaces': true };
      }
      if (trimmed.length < minLength) {
        return { 'minlength': true };
      }
      return null;
    };
  }

  trimmedUbicacionValidator() {
    return (control: AbstractControl): ValidationErrors | null => {
      const value = control.value;
      if (value === null || value === undefined || value === '') {
        return null;
      }
      const trimmed = value.trim();
      if (trimmed.length === 0) {
        return { 'onlySpaces': true };
      }
      return null;
    };
  }

  /** Envía el formulario al backend si es válido. */
  mostrarMensajeError(texto: string): void {
    this.mensajeError = texto;
    this.toastService.mostrarMensaje(texto, true);
    setTimeout(() => {
      if (this.mensajeError === texto) {
        this.mensajeError = null;
      }
    }, 4000);
  }

  onEnviar(): void {
    // Force recalculation of validations
    this.formulario.get('titulo')?.updateValueAndValidity();
    this.formulario.get('descripcion')?.updateValueAndValidity();
    this.formulario.get('ubicacion')?.updateValueAndValidity();

    if (this.formulario.invalid) {
      this.formulario.markAllAsTouched();
      return;
    }

    const usuario = this.authService.getUsuarioActual();
    if (!usuario) {
      this.toastService.mostrarMensaje('No hay sesión activa. Por favor, inicia sesión de nuevo.', true);
      return;
    }

    const valores = this.formulario.value;
    const tituloTrimeado = (valores.titulo || '').trim();
    const descripcionTrimeada = (valores.descripcion || '').trim();
    const ubicacionTrimeada = (valores.ubicacion || '').trim();

    const payload: any = {
      tipo: valores.tipo,
      prioridad: this.PRIORIDAD_MAP[valores.prioridad] ?? 'm',
      id_categoria: valores.id_categoria,
      titulo: tituloTrimeado,
      descripcion: descripcionTrimeada,
      id_usuario_creador: usuario.id,
      estado: 'pendiente',
      ubicacion: ubicacionTrimeada || null,
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
          this.toastService.mostrarMensaje(this.mensajeExito, false);
          setTimeout(() => this.router.navigate(['/portal-tickets/tickets']), 1500);
        } else {
          this.mostrarMensajeError(res.message || 'Error al crear el ticket.');
        }
      },
      error: () => {
        this.enviando = false;
        this.mostrarMensajeError('Error de conexión con el servidor. Inténtalo de nuevo.');
      }
    });
  }

  toggleDesplegableTipo(event: Event): void {
    event.stopPropagation();
    this.desplegableTipoAbierto = !this.desplegableTipoAbierto;
    this.desplegablePrioridadAbierto = false;
    this.desplegableCategoriaAbierto = false;
    this.desplegableEncargadoAbierto = false;
  }

  toggleDesplegablePrioridad(event: Event): void {
    event.stopPropagation();
    this.desplegablePrioridadAbierto = !this.desplegablePrioridadAbierto;
    this.desplegableTipoAbierto = false;
    this.desplegableCategoriaAbierto = false;
    this.desplegableEncargadoAbierto = false;
  }

  toggleDesplegableCategoria(event: Event): void {
    event.stopPropagation();
    this.desplegableCategoriaAbierto = !this.desplegableCategoriaAbierto;
    this.desplegableTipoAbierto = false;
    this.desplegablePrioridadAbierto = false;
    this.desplegableEncargadoAbierto = false;
  }

  toggleDesplegableEncargado(event: Event): void {
    event.stopPropagation();
    this.desplegableEncargadoAbierto = !this.desplegableEncargadoAbierto;
    this.desplegableTipoAbierto = false;
    this.desplegablePrioridadAbierto = false;
    this.desplegableCategoriaAbierto = false;
  }

  seleccionarTipo(tipo: string): void {
    this.formulario.get('tipo')?.setValue(tipo);
    this.desplegableTipoAbierto = false;
  }

  seleccionarPrioridad(prioridad: string): void {
    this.formulario.get('prioridad')?.setValue(prioridad);
    this.desplegablePrioridadAbierto = false;
  }

  get operariosFiltrados(): Usuario[] {
    const idCat = this.formulario.get('id_categoria')?.value;
    if (!idCat) {
      return [];
    }
    const idCatNum = Number(idCat);
    return this.operarios.filter(op => {
      if (!op.categorias_ids) return false;
      const ids = String(op.categorias_ids).split(',').map(Number);
      return ids.includes(idCatNum);
    });
  }

  seleccionarCategoria(id: number): void {
    this.formulario.get('id_categoria')?.setValue(id);
    this.formulario.get('id_categoria')?.markAsTouched();
    this.desplegableCategoriaAbierto = false;

    // Verificar si el operario seleccionado sigue siendo válido para la nueva categoría
    const idOperario = this.formulario.get('id_usuario_encargado')?.value;
    if (idOperario) {
      const op = this.operarios.find(o => o.id == idOperario);
      if (op) {
        const ids = op.categorias_ids ? String(op.categorias_ids).split(',').map(Number) : [];
        if (!ids.includes(Number(id))) {
          this.formulario.get('id_usuario_encargado')?.setValue('');
        }
      }
    }
  }

  seleccionarEncargado(id: number | string): void {
    this.formulario.get('id_usuario_encargado')?.setValue(id);
    this.desplegableEncargadoAbierto = false;
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

  getEncargadoEtiqueta(): string {
    const catId = this.formulario.get('id_categoria')?.value;
    if (catId && this.operariosFiltrados.length === 0) {
      return '-- No hay trabajadores disponibles --';
    }
    const id = this.formulario.get('id_usuario_encargado')?.value;
    if (!id) return '-- Sin asignar --';
    const op = this.operarios.find(o => o.id == id);
    return op ? op.nombre + ' (' + op.rol.toUpperCase() + ')' : '-- Sin asignar --';
  }

  logout(): void {
    this.authService.logout();
  }

  @HostListener('document:click', ['$event'])
  cerrarDesplegables(): void {
    this.desplegableTipoAbierto = false;
    this.desplegablePrioridadAbierto = false;
    this.desplegableCategoriaAbierto = false;
    this.desplegableEncargadoAbierto = false;
  }
}

