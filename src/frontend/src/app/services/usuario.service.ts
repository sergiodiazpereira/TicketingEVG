import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from '../../enviroments/environment';
import { Usuario } from '../models/usuario.model';

@Injectable({
  providedIn: 'root'
})
export class UsuarioService {
  private apiUrl = environment.apiUrl;

  constructor(private http: HttpClient) { }

  getOperarios(): Observable<Usuario[]> {
    return this.http.get<Usuario[]>(`${this.apiUrl}?entidad=usuario&accion=listar_operarios`);
  }
}
