/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Pruebas unitarias para la Consola de Operarios.
 */
import { ComponentFixture, TestBed, fakeAsync, tick } from '@angular/core/testing';
import { HttpClientTestingModule } from '@angular/common/http/testing';
import { RouterTestingModule } from '@angular/router/testing';
import { of, throwError } from 'rxjs';
import { OperariosComponent } from './operarios.component';
import { AuthService } from '../../../services/auth.service';
import { UsuarioService } from '../../../services/usuario.service';

describe('OperariosComponent', () => {
	let component: OperariosComponent;
	let fixture: ComponentFixture<OperariosComponent>;
	let mockAuthService: any;
	let mockUsuarioService: any;

	beforeEach(async () => {
		mockAuthService = {
			getUsuarioActual: jasmine.createSpy('getUsuarioActual').and.returnValue({
				id: 26,
				nombre: 'Joseph Joel Quispe Alvarez',
				rol: 'administrador'
			})
		};

		mockUsuarioService = {
			getOperarios: jasmine.createSpy('getOperarios').and.returnValue(of([
				{ id: 26, nombre: 'Joseph Joel Quispe Alvarez', email: 'josephqa3131@gmail.com', rol: 'responsable', categorias_nombres: 'Software, Redes' },
				{ id: 101, nombre: 'María Reyes', email: 'mcreyes@evg.es', rol: 'trabajador', categorias_nombres: 'Mantenimiento' }
			])),
			crearUsuario: jasmine.createSpy('crearUsuario').and.returnValue(of({ status: 'success' })),
			actualizarUsuario: jasmine.createSpy('actualizarUsuario').and.returnValue(of({ status: 'success' })),
			eliminarUsuario: jasmine.createSpy('eliminarUsuario').and.returnValue(of({ status: 'success' }))
		};

		await TestBed.configureTestingModule({
			imports: [OperariosComponent, HttpClientTestingModule, RouterTestingModule],
			providers: [
				{ provide: AuthService, useValue: mockAuthService },
				{ provide: UsuarioService, useValue: mockUsuarioService }
			]
		}).compileComponents();

		fixture = TestBed.createComponent(OperariosComponent);
		component = fixture.componentInstance;
	});

	it('debería inicializarse el componente con éxito', () => {
		fixture.detectChanges();
		expect(component).toBeTruthy();
	});

	it('debería cargar la lista de operarios al inicializarse mapeando categorías', () => {
		fixture.detectChanges();
		expect(mockUsuarioService.getOperarios).toHaveBeenCalled();
		expect(component.operarios.length).toEqual(2);
		expect((component.operarios[0] as any).categorias_nombres).toEqual(['Software', 'Redes']);
	});

	it('debería alternar la expansión de categorías por operario', () => {
		fixture.detectChanges();
		expect(component.operarioExpandido).toBeNull();
		
		component.toggleCategorias(26);
		expect(component.operarioExpandido).toEqual(26);

		component.toggleCategorias(26);
		expect(component.operarioExpandido).toBeNull();
	});

	it('debería gestionar apertura y cierre de modal de formularios', () => {
		fixture.detectChanges();
		component.abrirModalFormulario({ id: 101, nombre: 'María Reyes' });
		expect(component.mostrarModalFormulario).toBeTrue();
		expect(component.operarioAEditar).not.toBeNull();

		component.cerrarModalFormulario();
		expect(component.mostrarModalFormulario).toBeFalse();
		expect(component.operarioAEditar).toBeNull();
	});

	it('debería crear un operario llamando al servicio correspondiente y recargando la lista', () => {
		fixture.detectChanges();
		const nuevoOperario = { id: 102, rol: 'trabajador', categorias: [1, 2] };

		component.guardarOperario(nuevoOperario);
		expect(mockUsuarioService.crearUsuario).toHaveBeenCalledWith(nuevoOperario);
		expect(component.mensajeFeedback).toEqual('Operario creado correctamente.');
		expect(component.mostrarModalFormulario).toBeFalse();
	});

	it('debería actualizar un operario existente llamando al servicio', () => {
		fixture.detectChanges();
		const editOperario = { id: 101, rol: 'responsable', categorias: [1] };

		component.guardarOperario(editOperario);
		expect(mockUsuarioService.actualizarUsuario).toHaveBeenCalledWith(101, editOperario);
		expect(component.mensajeFeedback).toEqual('Operario actualizado correctamente.');
	});

	it('debería gestionar eliminación de operarios', () => {
		fixture.detectChanges();
		const opEliminar = { id: 101, nombre: 'María Reyes' };

		component.abrirModalEliminar(opEliminar);
		expect(component.mostrarModalEliminar).toBeTrue();
		expect(component.operarioAEliminarId).toEqual(101);

		component.confirmarEliminar();
		expect(mockUsuarioService.eliminarUsuario).toHaveBeenCalledWith(101);
		expect(component.mensajeFeedback).toEqual('Operario eliminado correctamente.');
		expect(component.mostrarModalEliminar).toBeFalse();
	});

	it('debería mostrar mensaje de feedback temporal', fakeAsync(() => {
		fixture.detectChanges();
		(component as any).mostrarMensaje('Prueba Feedback', false);
		expect(component.mensajeFeedback).toEqual('Prueba Feedback');
		expect(component.esMensajeError).toBeFalse();

		tick(4000); // Avanzar temporizador 4 segundos
		expect(component.mensajeFeedback).toBeNull();
	}));
});
