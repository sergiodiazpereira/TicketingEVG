/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Interfaz para el modelo de Ticket.
 */
export interface Ticket {
  id: number;
  titulo: string;
  descripcion: string;
  fechaCreacion: Date;
  tipo: 'incidencia' | 'peticion';
  estado: 'pendiente' | 'asignado' | 'proceso' | 'resuelto';
  prioridad: 'baja' | 'media' | 'alta';
  usuarioId: number; // ID del profesor/alumno que lo crea
  operarioId?: number; // ID del operario asignado
  categoriaId: number;
}
