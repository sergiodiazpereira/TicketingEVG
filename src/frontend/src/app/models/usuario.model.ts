/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Interfaz para el modelo de Usuario.
 */
export interface Usuario {
  id: number;
  nombre: string;
  email: string;
  password?: string;
  rol: 'admin' | 'profesor' | 'operario';
}
