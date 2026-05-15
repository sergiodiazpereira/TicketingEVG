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
  operarioExpandido: number | null = null;

  constructor(
    private authService: AuthService, 
    private usuarioService: UsuarioService
  ) {
    this.usuario_actual = this.authService.getUsuarioActual();
  }

  ngOnInit(): void {
    this.usuarioService.getOperarios().subscribe({
      next: (data) => {
        // Asegurar que data sea un array para evitar errores en @for
        this.operarios = Array.isArray(data) && data.length > 0 ? data : [
          { id: 1, nombre: 'Juan Pérez', email: 'juan@ticketing.com', rol: 'responsable', num_categorias: 3, tickets_asignados: 12, categorias_nombres: ['Soporte Nivel 1', 'Soporte Nivel 2', 'Redes y Sistemas'] },
          { id: 2, nombre: 'Ana Gómez', email: 'ana@ticketing.com', rol: 'trabajador', num_categorias: 1, tickets_asignados: 5, categorias_nombres: ['Mantenimiento General'] }
        ] as any;
      },
      error: (err) => {
        console.error('Error al cargar operarios', err);
        this.operarios = [
          { id: 1, nombre: 'Juan Pérez', email: 'juan@ticketing.com', rol: 'responsable', num_categorias: 3, tickets_asignados: 12, categorias_nombres: ['Soporte Nivel 1', 'Soporte Nivel 2', 'Redes y Sistemas'] },
          { id: 2, nombre: 'Ana Gómez', email: 'ana@ticketing.com', rol: 'trabajador', num_categorias: 1, tickets_asignados: 5, categorias_nombres: ['Mantenimiento General'] }
        ] as any;
      }
    });
  }

  toggleCategorias(id: number) {
    if (this.operarioExpandido === id) {
      this.operarioExpandido = null;
    } else {
      this.operarioExpandido = id;
    }
  }

  abrirModalFormulario(operario?: any) {
    this.operarioAEditar = operario || null;
    this.mostrarModalFormulario = true;
  }

  cerrarModalFormulario() {
    this.mostrarModalFormulario = false;
    this.operarioAEditar = null;
  }

  guardarOperario() {
    console.log('Operario guardado (Simulación)', this.operarioAEditar);
    this.cerrarModalFormulario();
  }

  abrirModalEliminar() {
    this.mostrarModalEliminar = true;
  }

  cerrarModal() {
    this.mostrarModalEliminar = false;
  }

  confirmarEliminar() {
    console.log('Operario eliminado (Simulación)');
    this.cerrarModal();
  }
  
trackByOperarioId(index: number, operario: any): number {
  return operario.id;
}
}
