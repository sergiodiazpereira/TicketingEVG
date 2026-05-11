/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio encargado de la lógica de negocio y gestión de datos de los tickets.
 */
import { Injectable } from '@angular/core';
import { Ticket } from '../models/ticket.model';

@Injectable({
  providedIn: 'root'
})
export class TicketService {
  /**
   * Almacén temporal de tickets (simula una base de datos).
   */
  private tickets: Ticket[] = [
    { 
      id: 1, 
      titulo: 'Ordenador no enciende en Aula 203', 
      descripcion: 'El equipo no da señal de vida tras pulsar el botón.', 
      fechaCreacion: new Date(), 
      tipo: 'incidencia',
      estado: 'proceso', 
      prioridad: 'alta', 
      usuarioId: 2, 
      operarioId: 4, 
      categoriaId: 1 
    },
    { 
      id: 2, 
      titulo: 'Solicitud de marcadores para pizarra', 
      descripcion: 'Se necesitan marcadores de color azul y negro.', 
      fechaCreacion: new Date(), 
      tipo: 'peticion',
      estado: 'pendiente', 
      prioridad: 'baja', 
      usuarioId: 2, 
      categoriaId: 3 
    },
    { 
      id: 3, 
      titulo: 'Proyector muestra imagen borrosa', 
      descripcion: 'No se puede enfocar correctamente la imagen.', 
      fechaCreacion: new Date(), 
      tipo: 'incidencia',
      estado: 'asignado', 
      prioridad: 'media', 
      usuarioId: 3, 
      operarioId: 4, 
      categoriaId: 1 
    },
    { 
      id: 4, 
      titulo: 'Fallo conexión Wi-Fi Sala Profesores', 
      descripcion: 'Cortes intermitentes en la red inalámbrica.', 
      fechaCreacion: new Date(), 
      tipo: 'incidencia',
      estado: 'pendiente', 
      prioridad: 'alta', 
      usuarioId: 2, 
      categoriaId: 2 
    },
    { 
      id: 5, 
      titulo: 'Instalación de software estadístico', 
      descripcion: 'Requerido para las clases de matemáticas.', 
      fechaCreacion: new Date(), 
      tipo: 'peticion',
      estado: 'asignado', 
      prioridad: 'media', 
      usuarioId: 3, 
      operarioId: 4, 
      categoriaId: 1 
    },
    { 
      id: 6, 
      titulo: 'Sustitución de bombilla fundida', 
      descripcion: 'Aula 104, fila central.', 
      fechaCreacion: new Date(), 
      tipo: 'peticion',
      estado: 'resuelto', 
      prioridad: 'baja', 
      usuarioId: 3, 
      categoriaId: 3 
    }
  ];

  constructor() { }

  /**
   * Recupera la lista completa de todos los tickets del sistema.
   * @returns Array de objetos Ticket.
   */
  getTickets(): Ticket[] {
    return [...this.tickets];
  }

  /**
   * Filtra los tickets creados por un usuario específico (ej: un profesor).
   * @param usuarioId El identificador del usuario.
   * @returns Array de tickets pertenecientes a dicho usuario.
   */
  getTicketsPorUsuario(usuarioId: number): Ticket[] {
    return this.tickets.filter(t => t.usuarioId === usuarioId);
  }

  /**
   * Filtra los tickets que han sido asignados a un operario técnico específico.
   * @param operarioId El identificador del operario.
   * @returns Array de tickets asignados.
   */
  getTicketsPorOperario(operarioId: number): Ticket[] {
    return this.tickets.filter(t => t.operarioId === operarioId);
  }

  /**
   * Crea un nuevo ticket y lo añade al almacén local.
   * Genera automáticamente el ID y la fecha de creación.
   * @param ticket Los datos del nuevo ticket (sin ID ni fecha).
   */
  addTicket(ticket: Omit<Ticket, 'id' | 'fechaCreacion'>): void {
    const nuevoTicket: Ticket = {
      ...ticket,
      id: this.tickets.length + 1,
      fechaCreacion: new Date()
    };
    this.tickets.push(nuevoTicket);
  }

  /**
   * Permite modificar el estado actual de un ticket existente.
   * @param id El identificador del ticket a modificar.
   * @param estado El nuevo estado (pendiente, asignado, proceso, resuelto).
   */
  actualizarEstado(id: number, estado: Ticket['estado']): void {
    const index = this.tickets.findIndex(t => t.id === id);
    if (index !== -1) {
      this.tickets[index].estado = estado;
    }
  }
}
