/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modal de formulario para crear o editar un operario conectando con Intranet.
 */
import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CategoriasService } from '../../../services/categorias.service';
import { UsuarioService } from '../../../services/usuario.service';
import { Categoria } from '../../../models/categoria.model';

@Component({
  selector: 'app-formulario-operario',
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './formulario-operario.component.html',
  styleUrl: './formulario-operario.component.css'
})
export class FormularioOperarioComponent implements OnInit {
  /** Operario a editar. Si es null, el formulario está en modo creación. */
  @Input() operario: any = null;
  @Output() cerrar = new EventEmitter<void>();
  /** Emite el objeto de datos del formulario al componente padre. */
  @Output() guardar = new EventEmitter<any>();

  formulario!: FormGroup;

  /** Categorías cargadas desde la API. */
  categorias: Categoria[] = [];
  cargandoCategorias = true;

  /** Miembros del personal de la intranet disponibles para registrar en Ticketing. */
  personalIntranet: any[] = [];
  cargandoPersonal = false;

  constructor(
    private fb: FormBuilder,
    private categoriasService: CategoriasService,
    private usuarioService: UsuarioService
  ) {}

  ngOnInit(): void {
    // Si estamos editando, el ID ya viene establecido y el nombre/correo son de solo lectura
    this.formulario = this.fb.group({
      id: [this.operario?.id || '', Validators.required],
      nombre: [{ value: this.operario?.nombre || '', disabled: true }],
      correo: [{ value: this.operario?.email || '', disabled: true }],
      rol: [this.operario?.rol || 'trabajador', Validators.required],
      categorias: [[]]
    });

    // Cargar categorías desde la BD y preseleccionar las del operario si es edición
    this.categoriasService.obtenerCategorias().subscribe({
      next: (data: any) => {
        this.categorias = data;
        this.cargandoCategorias = false;

        // Si es modo edición, preseleccionar las categorías del operario por nombre
        if (this.operario?.categorias_nombres) {
          const idsSeleccionados = this.categorias
            .filter(c => this.operario.categorias_nombres.includes(c.nombre))
            .map(c => c.id);
          this.formulario.patchValue({ categorias: idsSeleccionados });
        }
      },
      error: () => {
        this.cargandoCategorias = false;
      }
    });

    // Si es modo creación, cargar el personal disponible de la Intranet
    if (!this.operario) {
      this.cargandoPersonal = true;
      this.usuarioService.getPersonalIntranet().subscribe({
        next: (data: any) => {
          this.personalIntranet = Array.isArray(data) ? data : [];
          this.cargandoPersonal = false;
        },
        error: () => {
          this.cargandoPersonal = false;
        }
      });
    }
  }

  /** Devuelve true si una categoría está seleccionada en el formulario. */
  estaSeleccionada(idCategoria: number): boolean {
    return (this.formulario.get('categorias')?.value ?? []).includes(idCategoria);
  }

  /** Añade o quita una categoría del array de seleccionadas. */
  toggleCategoria(idCategoria: number): void {
    const actuales: number[] = this.formulario.get('categorias')?.value ?? [];
    const nuevas = actuales.includes(idCategoria)
      ? actuales.filter(id => id !== idCategoria)
      : [...actuales, idCategoria];
    this.formulario.patchValue({ categorias: nuevas });
  }

  /** Emite los datos del formulario si es válido. */
  onGuardar(): void {
    if (this.formulario.invalid) {
      this.formulario.markAllAsTouched();
      return;
    }
    
    // Obtenemos los valores incluyendo los campos deshabilitados si procede
    const datos = this.formulario.getRawValue();
    this.guardar.emit(datos);
  }
}
