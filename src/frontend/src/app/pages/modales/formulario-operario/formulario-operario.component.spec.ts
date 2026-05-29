/**
 * Proyecto: TicketingEVG
 * Alumno: Joseph Joel Quispe Alvarez
 * Asignatura: DAW
 * Curso: 2025-2026
 * Descripción: Pruebas unitarias para el modal Formulario de Operarios con inyección de servicios.
 */
import { ComponentFixture, TestBed } from '@angular/core/testing';
import { HttpClientTestingModule } from '@angular/common/http/testing';
import { ReactiveFormsModule } from '@angular/forms';
import { of } from 'rxjs';
import { FormularioOperarioComponent } from './formulario-operario.component';
import { CategoriasService } from '../../../services/categorias.service';
import { UsuarioService } from '../../../services/usuario.service';

describe('FormularioOperarioComponent', () => {
	let component: FormularioOperarioComponent;
	let fixture: ComponentFixture<FormularioOperarioComponent>;
	let mockCategoriasService: any;
	let mockUsuarioService: any;

	beforeEach(async () => {
		mockCategoriasService = {
			obtenerCategorias: jasmine.createSpy('obtenerCategorias').and.returnValue(of([
				{ id: 1, nombre: 'Software', descripcion: 'Soporte de Software' },
				{ id: 2, nombre: 'Redes', descripcion: 'Soporte de Redes' }
			]))
		};

		mockUsuarioService = {
			getPersonalIntranet: jasmine.createSpy('getPersonalIntranet').and.returnValue(of([
				{ id: 1, nombre: 'Sergio Díaz', correo: 'sergio@evg.es' },
				{ id: 26, nombre: 'Joseph Quispe', correo: 'joseph@evg.es' }
			]))
		};

		await TestBed.configureTestingModule({
			imports: [FormularioOperarioComponent, HttpClientTestingModule, ReactiveFormsModule],
			providers: [
				{ provide: CategoriasService, useValue: mockCategoriasService },
				{ provide: UsuarioService, useValue: mockUsuarioService }
			]
		}).compileComponents();

		fixture = TestBed.createComponent(FormularioOperarioComponent);
		component = fixture.componentInstance;
	});

	it('debería inicializarse el componente con éxito', () => {
		fixture.detectChanges();
		expect(component).toBeTruthy();
	});

	it('debería inicializar el formulario con valores vacíos en modo creación y cargar personal', () => {
		fixture.detectChanges();
		expect(mockUsuarioService.getPersonalIntranet).toHaveBeenCalled();
		expect(component.personalIntranet.length).toEqual(2);
		expect(component.formulario.get('id')?.value).toEqual('');
		expect(component.formulario.get('rol')?.value).toEqual('trabajador');
	});

	it('debería inicializar el formulario con datos del operario en modo edición y no cargar personal', () => {
		component.operario = {
			id: 26,
			nombre: 'Joseph Joel Quispe Alvarez',
			email: 'josephqa3131@gmail.com',
			rol: 'responsable',
			categorias_nombres: ['Software']
		};

		fixture.detectChanges();
		expect(mockUsuarioService.getPersonalIntranet).not.toHaveBeenCalled();
		expect(component.formulario.get('id')?.value).toEqual(26);
		// El nombre y correo se deshabilitan en el formulario
		expect(component.formulario.get('nombre')?.disabled).toBeTrue();
		expect(component.formulario.get('correo')?.disabled).toBeTrue();
		expect(component.formulario.get('rol')?.value).toEqual('responsable');
	});

	it('debería alternar selecciones de categorías', () => {
		fixture.detectChanges();
		expect(component.estaSeleccionada(1)).toBeFalse();

		component.toggleCategoria(1);
		expect(component.estaSeleccionada(1)).toBeTrue();

		component.toggleCategoria(1);
		expect(component.estaSeleccionada(1)).toBeFalse();
	});

	it('debería emitir los datos del formulario al guardar si es válido', () => {
		fixture.detectChanges();
		spyOn(component.guardar, 'emit');

		component.formulario.patchValue({
			id: 26,
			rol: 'responsable'
		});
		component.toggleCategoria(1);

		component.onGuardar();
		expect(component.guardar.emit).toHaveBeenCalledWith(jasmine.objectContaining({
			id: 26,
			rol: 'responsable',
			categorias: [1]
		}));
	});
});
