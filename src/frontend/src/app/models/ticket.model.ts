/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Interfaz para el modelo de Ticket.
 */
export interface Ticket {
  id: string; // Cambiado a string por el formato I200223...
  titulo: string;
  descripcion: string;
  fecha_creacion: string; // Viene como string de la API
  tipo: 'incidencia' | 'peticion';
  estado: 'pendiente' | 'asignado' | 'proceso' | 'resuelto';
  prioridad: string; // 'a', 'm', 'b'
  id_Usuario_Creador: number;
  id_Usuario_Encargado?: number;
  id_Categoria: number;
}
