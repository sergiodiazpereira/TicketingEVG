/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modal de formulario para crear o editar un operario.
 */
import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';
import { CategoriasService } from '../../../services/categorias.service';
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

  constructor(private fb: FormBuilder, private categoriasService: CategoriasService) {}

  ngOnInit(): void {
    this.formulario = this.fb.group({
      nombre: [this.operario?.nombre || '', [Validators.required, Validators.minLength(3)]],
      correo: [this.operario?.email || '', [Validators.required, Validators.email]],
      rol: [this.operario?.rol || 'trabajador', Validators.required],
      categorias: [[]]
    });

    // Cargar categorías desde la BD y luego preseleccionar las del operario
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
    const datos = this.formulario.value;
    if (this.operario?.id)
      datos['id'] = this.operario.id;
    this.guardar.emit(datos);
  }
}
