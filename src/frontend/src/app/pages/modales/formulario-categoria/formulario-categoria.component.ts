/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Modal de formulario para crear o editar una categoría.
 */
import { Component, Input, Output, EventEmitter, OnInit } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormBuilder, FormGroup, Validators } from '@angular/forms';

@Component({
  selector: 'app-formulario-categoria',
  standalone: true,
  imports: [CommonModule, ReactiveFormsModule],
  templateUrl: './formulario-categoria.component.html',
  styleUrl: './formulario-categoria.component.css'
})
export class FormularioCategoriaComponent implements OnInit {
  @Input() categoria: any = null;
  @Output() cerrar = new EventEmitter<void>();
  @Output() guardar = new EventEmitter<any>();

  formulario!: FormGroup;

  constructor(private fb: FormBuilder) {}

  ngOnInit(): void {
    this.formulario = this.fb.group({
      nombre: [this.categoria?.nombre || '', [Validators.required, Validators.maxLength(50)]],
      descripcion: [this.categoria?.descripcion || '', [Validators.maxLength(100)]]
    });
  }

  onGuardar(): void {
    if (this.formulario.invalid) {
      this.formulario.markAllAsTouched();
      return;
    }
    const datos = this.formulario.value;
    if (this.categoria?.id)
      datos['id'] = this.categoria.id;
    this.guardar.emit(datos);
  }
}
