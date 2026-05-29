/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Pruebas unitarias para el componente callback SSO de la Intranet.
 */
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { Router, ActivatedRoute } from '@angular/router';
import { of, throwError } from 'rxjs';
import { SsoCallbackComponent } from './sso-callback.component';
import { AuthService } from '../../services/auth.service';

describe('SsoCallbackComponent', () => {
	let component: SsoCallbackComponent;
	let fixture: ComponentFixture<SsoCallbackComponent>;
	let mockRouter: any;
	let mockActivatedRoute: any;
	let mockAuthService: any;

	beforeEach(async () => {
		mockRouter = {
			navigate: jasmine.createSpy('navigate')
		};

		mockActivatedRoute = {
			queryParams: of({ token: 'JWT_PRUEBA_INTRANET' })
		};

		mockAuthService = {
			loginConSSO: jasmine.createSpy('loginConSSO').and.returnValue(of({
				status: 'success',
				usuario: { id: 26, nombre: 'Joseph', rol: 'trabajador' }
			}))
		};

		await TestBed.configureTestingModule({
			imports: [SsoCallbackComponent],
			providers: [
				{ provide: Router, useValue: mockRouter },
				{ provide: ActivatedRoute, useValue: mockActivatedRoute },
				{ provide: AuthService, useValue: mockAuthService }
			]
		}).compileComponents();

		fixture = TestBed.createComponent(SsoCallbackComponent);
		component = fixture.componentInstance;
	});

	it('debería inicializarse el componente con éxito', () => {
		expect(component).toBeTruthy();
	});

	it('debería procesar el token y redirigir a /portal-tickets si el usuario es trabajador', () => {
		fixture.detectChanges();
		expect(mockAuthService.loginConSSO).toHaveBeenCalledWith('JWT_PRUEBA_INTRANET');
		expect(mockRouter.navigate).toHaveBeenCalledWith(['/portal-tickets']);
	});

	it('debería procesar el token y redirigir a /acceso si el usuario es administrador', () => {
		mockAuthService.loginConSSO.and.returnValue(of({
			status: 'success',
			usuario: { id: 26, nombre: 'Joseph', rol: 'administrador' }
		}));

		fixture.detectChanges();
		expect(mockRouter.navigate).toHaveBeenCalledWith(['/acceso']);
	});

	it('debería redirigir a /login con query param de error si falla el servicio SSO', () => {
		mockAuthService.loginConSSO.and.returnValue(throwError(() => new Error('SSO fallido')));

		fixture.detectChanges();
		expect(mockRouter.navigate).toHaveBeenCalledWith(['/login'], { queryParams: { error: 'sso_failed' } });
	});

	it('debería redirigir directamente a /login si se invoca el callback sin token en query params', () => {
		mockActivatedRoute.queryParams = of({});
		
		fixture = TestBed.createComponent(SsoCallbackComponent);
		component = fixture.componentInstance;
		fixture.detectChanges();

		expect(mockRouter.navigate).toHaveBeenCalledWith(['/login']);
		expect(mockAuthService.loginConSSO).not.toHaveBeenCalled();
	});
});
