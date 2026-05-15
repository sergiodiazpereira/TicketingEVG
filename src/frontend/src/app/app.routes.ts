import { Routes } from '@angular/router';
import { DashboardComponent } from './pages/admin/dashboard/dashboard.component';
import { CategoriasComponent } from './pages/admin/categorias/categorias.component';
import { OperariosComponent } from './pages/admin/operarios/operarios.component';
import { LoginComponent } from './pages/login/login.component';
import { AccesoComponent } from './pages/acceso/acceso.component';
import { PortalTicketsComponent } from './pages/usuario/portal-tickets/portal-tickets.component';
import { ListaTicketsComponent } from './pages/usuario/lista-tickets/lista-tickets.component';
import { CrearTicketComponent } from './pages/usuario/crear-ticket/crear-ticket.component';
import { authGuard } from './guards/auth.guard';

export const routes: Routes = [
  { path: '', redirectTo: 'login', pathMatch: 'full' },
  { path: 'login', component: LoginComponent },
  { path: 'acceso', component: AccesoComponent, canActivate: [authGuard] },
  { path: 'portal-tickets', component: PortalTicketsComponent, canActivate: [authGuard] },
  { path: 'portal-tickets/tickets', component: ListaTicketsComponent, canActivate: [authGuard] },
  { path: 'portal-tickets/crear', component: CrearTicketComponent, canActivate: [authGuard] },
  {
    path: 'admin',
    canActivate: [authGuard],
    children: [
      { path: 'dashboard', component: DashboardComponent },
      { path: 'categorias', component: CategoriasComponent },
      { path: 'operarios', component: OperariosComponent },
    ]
  },
  { path: '**', redirectTo: 'login' }
];
