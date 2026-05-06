import { Component } from '@angular/core';
import { RouterLink } from '@angular/router';
import { FooterComponent } from '../../../shared/layout/footer/footer.component';
import { SidebarComponent } from '../../../shared/layout/sidebar/sidebar.component';

@Component({
  selector: 'app-operarios',
  imports: [RouterLink, FooterComponent, SidebarComponent],
  templateUrl: './operarios.component.html',
  styleUrl: './operarios.component.css'
})
export class OperariosComponent { }
