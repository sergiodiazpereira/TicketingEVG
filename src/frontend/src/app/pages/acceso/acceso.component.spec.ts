/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Pruebas unitarias para el componente de selección de entorno de Acceso.
 */
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { AccesoComponent } from './acceso.component';
import { AuthService } from '../../services/auth.service';

describe('AccesoComponent', () => {
	let component: AccesoComponent;
	let fixture: ComponentFixture<AccesoComponent>;
	let mockAuthService: any;

	beforeEach(async () => {
		mockAuthService = {
			getUsuarioActual: jasmine.createSpy('getUsuarioActual').and.returnValue({
				id: 26,
				nombre: 'Joseph Joel Quispe Alvarez',
				email: 'josephqa3131@gmail.com',
				rol: 'administrador'
			})
		};

		await TestBed.configureTestingModule({
			imports: [AccesoComponent, RouterTestingModule],
			providers: [
				{ provide: AuthService, useValue: mockAuthService }
			]
		}).compileComponents();

		fixture = TestBed.createComponent(AccesoComponent);
		component = fixture.componentInstance;
	});

	it('debería inicializarse el componente con éxito', () => {
		expect(component).toBeTruthy();
	});

	it('debería obtener el usuario autenticado actualmente al cargarse', () => {
		fixture.detectChanges();
		expect(mockAuthService.getUsuarioActual).toHaveBeenCalled();
		expect(component.usuario_actual).not.toBeNull();
		expect(component.usuario_actual?.nombre).toEqual('Joseph Joel Quispe Alvarez');
	});
});
