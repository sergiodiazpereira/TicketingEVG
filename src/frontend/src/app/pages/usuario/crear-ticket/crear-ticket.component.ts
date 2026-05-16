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
    alta: 'a',
    urgente: 'a'
  };

  /** Mapa de categoría: valor del select → id_Categoria en la BD */
  readonly CATEGORIA_MAP: Record<string, number> = {
    software: 1,
    hardware: 2,
    redes: 3,
    mantenimiento: 4
  };

  constructor(
    private fb: FormBuilder,
    private ticketService: TicketService,
    private authService: AuthService,
    private router: Router
  ) {}

  ngOnInit(): void {
    this.formulario = this.fb.group({
      tipo: ['incidencia', Validators.required],
      prioridad: ['media', Validators.required],
      categoria: ['', Validators.required],
      ubicacion: [''],
      fecha_limite: [''],
      titulo: ['', [Validators.required, Validators.minLength(5)]],
      descripcion: ['', [Validators.required, Validators.minLength(10)]]
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
      id_Categoria: this.CATEGORIA_MAP[valores.categoria] ?? 1,
      titulo: valores.titulo,
      descripcion: valores.descripcion,
      id_Usuario_Creador: usuario.id,
      estado: 'pendiente' as const
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

