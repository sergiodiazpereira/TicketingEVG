/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Interfaz para las Estadisticas del Dashboard.
 */
export interface Estadisticas {
  total_visitas: number;
  total_usuarios: number;
  total_categorias: number;
  tickets_activos: number;
  operarios_disponibles: number;
  tickets_resueltos: number;
  total_tickets: number;
  prioridad_alta: number;
  prioridad_media: number;
  prioridad_baja: number;
}
