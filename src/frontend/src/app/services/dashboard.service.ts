import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../enviroments/environment';
import { Estadisticas } from '../models/estadisticas.model';

/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Servicio para gestionar los datos específicos del Dashboard.
 */
@Injectable({
  providedIn: 'root'
})
export class DashboardService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  /**
   * Obtiene todas las estadísticas globales para las tarjetas del dashboard.
   */
  getEstadisticas(): Observable<Estadisticas> {
    return this.http.get<Estadisticas>(`${this.apiUrl}?entidad=dashboard&accion=obtener_datos`);
  }
}
