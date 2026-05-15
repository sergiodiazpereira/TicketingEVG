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
  email?: string;
  correo?: string; // Según BBDD
  password?: string;
  rol: string;
  num_categorias?: number;
  tickets_asignados?: number;
  categorias_nombres?: string[];
}
