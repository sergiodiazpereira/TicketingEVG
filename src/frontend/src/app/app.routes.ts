import { Routes } from '@angular/router';
import { DashboardComponent } from './pages/admin/dashboard/dashboard.component';
import { CategoriasComponent } from './pages/admin/categorias/categorias.component';
import { OperariosComponent } from './pages/admin/operarios/operarios.component';

export const routes: Routes = [
  { path: '', redirectTo: 'admin/dashboard', pathMatch: 'full' },
  {
    path: 'admin',
    children: [
      { path: 'dashboard', component: DashboardComponent },
      { path: 'categorias', component: CategoriasComponent },
      { path: 'operarios', component: OperariosComponent },
    ]
  },
  { path: '**', redirectTo: 'admin/dashboard' } // Ruta comodín por si se pierde
];
