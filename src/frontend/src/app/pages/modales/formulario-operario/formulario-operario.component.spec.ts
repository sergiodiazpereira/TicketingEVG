import { ComponentFixture, TestBed } from '@angular/core/testing';

import { FormularioOperarioComponent } from './formulario-operario.component';

describe('FormularioOperarioComponent', () => {
  let component: FormularioOperarioComponent;
  let fixture: ComponentFixture<FormularioOperarioComponent>;

  beforeEach(async () => {
    await TestBed.configureTestingModule({
      imports: [FormularioOperarioComponent]
    })
    .compileComponents();

    fixture = TestBed.createComponent(FormularioOperarioComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
