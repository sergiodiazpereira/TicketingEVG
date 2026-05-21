/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Interfaz para el modelo de Categoria.
 */
export interface Categoria {
  id: number;
  nombre: string;
  descripcion?: string;
  operarios?: number;
  tickets?: number;
}
