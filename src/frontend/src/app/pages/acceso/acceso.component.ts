/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Controlador para la pantalla de selección de entorno de trabajo (Consola Admin vs Portal Tickets).
 */
import { Component, OnInit } from '@angular/core';
import { RouterLink } from '@angular/router';
import { AuthService } from '../../services/auth.service';
import { Usuario } from '../../models/usuario.model';

@Component({
  selector: 'app-acceso',
  standalone: true,
  imports: [RouterLink],
  templateUrl: './acceso.component.html',
  styleUrl: './acceso.component.css'
})
export class AccesoComponent implements OnInit {
  /** Almacena la información del usuario logueado para personalizar la bienvenida */
  usuario_actual: Usuario | null = null;

  /**
   * Inyecta el servicio de autenticación para acceder a los datos del usuario.
   * @param authService Servicio que gestiona el estado de la sesión.
   */
  constructor(private authService: AuthService) { }

  /**
   * Método de ciclo de vida que se ejecuta al cargar el componente.
   * Recupera el usuario autenticado actualmente.
   */
  ngOnInit(): void {
    this.usuario_actual = this.authService.getUsuarioActual();
  }
}
