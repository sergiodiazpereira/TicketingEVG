/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Pruebas unitarias para el componente de Login con redirección automática SSO.
 */
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { Router } from '@angular/router';
import { ActivatedRoute } from '@angular/router';
import { of } from 'rxjs';
import { LoginComponent } from './login.component';

describe('LoginComponent', () => {
	let component: LoginComponent;
	let fixture: ComponentFixture<LoginComponent>;
	let mockRouter: any;
	let mockActivatedRoute: any;

	beforeEach(async () => {
		mockRouter = {
			navigate: jasmine.createSpy('navigate')
		};

		mockActivatedRoute = {
			queryParams: of({})
		};

		await TestBed.configureTestingModule({
			imports: [LoginComponent],
			providers: [
				{ provide: Router, useValue: mockRouter },
				{ provide: ActivatedRoute, useValue: mockActivatedRoute }
			]
		}).compileComponents();

		fixture = TestBed.createComponent(LoginComponent);
		component = fixture.componentInstance;
	});

	it('debería inicializarse el componente con éxito', () => {
		expect(component).toBeTruthy();
	});

	it('debería redirigir a sso-callback si recibe un token en los query params', () => {
		mockActivatedRoute.queryParams = of({ token: 'test-token' });
		fixture = TestBed.createComponent(LoginComponent);
		component = fixture.componentInstance;
		fixture.detectChanges();
		
		expect(mockRouter.navigate).toHaveBeenCalledWith(
			['/sso-callback'],
			jasmine.objectContaining({
				queryParams: jasmine.objectContaining({
					token: 'test-token'
				})
			})
		);
	});

	it('debería mostrar un mensaje de error si el query parameter error es sso_failed', () => {
		mockActivatedRoute.queryParams = of({ error: 'sso_failed' });
		
		// Re-inicializamos para aplicar los nuevos query params mockeados
		fixture = TestBed.createComponent(LoginComponent);
		component = fixture.componentInstance;
		fixture.detectChanges();

		expect(component.error).toEqual('No se pudo validar tu sesión con la Intranet Escolar. Por favor, inténtalo de nuevo.');
		expect(mockRouter.navigate).not.toHaveBeenCalled();
	});
});
