import { Routes } from '@angular/router';
import { DashboardComponent } from './pages/admin/dashboard/dashboard.component';
import { CategoriasComponent } from './pages/admin/categorias/categorias.component';
import { OperariosComponent } from './pages/admin/operarios/operarios.component';
import { LoginComponent } from './pages/login/login.component';
import { AccesoComponent } from './pages/acceso/acceso.component';
import { PortalTicketsComponent } from './pages/portal-tickets/portal-tickets.component';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'acceso', component: AccesoComponent },
  { path: 'portal-tickets', component: PortalTicketsComponent },
  {
    path: 'admin',
    children: [
      { path: 'dashboard', component: DashboardComponent },
      { path: 'categorias', component: CategoriasComponent },
      { path: 'operarios', component: OperariosComponent },
    ]
  },
  { path: '**', redirectTo: 'login' }
];
