/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Pruebas unitarias para el Dashboard de Administración.
 */
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { HttpClientTestingModule } from '@angular/common/http/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { of, throwError } from 'rxjs';
import { DashboardComponent } from './dashboard.component';
import { AuthService } from '../../../services/auth.service';
import { DashboardService } from '../../../services/dashboard.service';
import { TicketService } from '../../../services/ticket.service';

describe('DashboardComponent', () => {
	let component: DashboardComponent;
	let fixture: ComponentFixture<DashboardComponent>;
	let mockAuthService: any;
	let mockDashboardService: any;
	let mockTicketService: any;

	beforeEach(async () => {
		mockAuthService = {
			getUsuarioActual: jasmine.createSpy('getUsuarioActual').and.returnValue({
				id: 26,
				nombre: 'Joseph Joel Quispe Alvarez',
				rol: 'administrador'
			})
		};

		mockDashboardService = {
			getEstadisticas: jasmine.createSpy('getEstadisticas').and.returnValue(of({
				total_visitas: 120,
				total_usuarios: 15,
				total_categorias: 4,
				tickets_activos: 5,
				operarios_disponibles: 3,
				tickets_resueltos: 5,
				total_tickets: 10,
				prioridad_alta: 1,
				prioridad_media: 2,
				prioridad_baja: 2
			}))
		};

		mockTicketService = {
			getTickets: jasmine.createSpy('getTickets').and.returnValue(of([
				{ id: 'I2302260101', titulo: 'Problema Red', estado: 'pendiente', prioridad: 'a', fecha_creacion: '2026-02-23' },
				{ id: 'PS2302260102', titulo: 'Instalar Office', estado: 'resuelto', prioridad: 'b', fecha_creacion: '2026-02-22' }
			]))
		};

		await TestBed.configureTestingModule({
			imports: [DashboardComponent, HttpClientTestingModule, RouterTestingModule],
			providers: [
				{ provide: AuthService, useValue: mockAuthService },
				{ provide: DashboardService, useValue: mockDashboardService },
				{ provide: TicketService, useValue: mockTicketService }
			]
		}).compileComponents();

		fixture = TestBed.createComponent(DashboardComponent);
		component = fixture.componentInstance;
	});

	it('debería inicializarse el componente con éxito', () => {
		fixture.detectChanges();
		expect(component).toBeTruthy();
		expect(component.usuario_actual?.nombre).toEqual('Joseph Joel Quispe Alvarez');
	});

	it('debería cargar estadísticas y tickets recientes al inicializarse', () => {
		fixture.detectChanges();
		expect(mockDashboardService.getEstadisticas).toHaveBeenCalled();
		expect(mockTicketService.getTickets).toHaveBeenCalled();
		expect(component.ticketsRecientes.length).toEqual(2);
		expect(component.estadisticas?.total_tickets).toEqual(10);
	});

	it('debería computar correctamente el porcentaje de tickets resueltos', () => {
		fixture.detectChanges();
		// Resueltos: 5 de 10 totales = 50%
		expect(component.porcentajeResueltos).toEqual(50);
	});

	it('debería gestionar la apertura y cierre del modal de tickets', () => {
		fixture.detectChanges();
		const ticketPrueba = { id: 'I2302260101', titulo: 'Problema Red' };
		
		component.abrirModalTicket(ticketPrueba);
		expect(component.mostrarModalTicket).toBeTrue();
		expect(component.ticketSeleccionado).toEqual(ticketPrueba);

		component.cerrarModalTicket();
		expect(component.mostrarModalTicket).toBeFalse();
		expect(component.ticketSeleccionado).toBeNull();
	});
});
