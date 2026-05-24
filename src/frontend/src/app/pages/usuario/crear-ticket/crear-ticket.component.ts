/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Componente para crear un nuevo ticket de soporte.
 */
import { Component, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { RouterModule, Router } from '@angular/router';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { TicketService } from '../../../services/ticket.service';
import { AuthService } from '../../../services/auth.service';
import { CategoriasService } from '../../../services/categorias.service';
import { Categoria } from '../../../models/categoria.model';

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

  /** Mapa de prioridad: valor del select → código de la BD */
  readonly PRIORIDAD_MAP: Record<string, string> = {
    baja: 'b',
    media: 'm',
    alta: 'a'
  };

  /** Categorías cargadas dinámicamente desde la BD */
  categorias: Categoria[] = [];
  cargandoCategorias = true;

  constructor(
    private fb: FormBuilder,
    private ticketService: TicketService,
    private authService: AuthService,
    private categoriasService: CategoriasService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.formulario = this.fb.group({
      tipo: ['incidencia', Validators.required],
      prioridad: ['media', Validators.required],
      id_categoria: ['', Validators.required],
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
    const payload = {
      tipo: valores.tipo,
      prioridad: this.PRIORIDAD_MAP[valores.prioridad] ?? 'm',
      id_categoria: valores.id_categoria,
      titulo: valores.titulo,
      descripcion: valores.descripcion,
      id_usuario_creador: usuario.id,
      estado: 'pendiente' as const,
      ubicacion: valores.ubicacion || null,
      fecha_prevista: valores.fecha_limite || null
    };

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
}

