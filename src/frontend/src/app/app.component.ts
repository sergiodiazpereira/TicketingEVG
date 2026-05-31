import { Component } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { ToastComponent } from './components/toast/toast.component';
import { IconComponent } from './components/icon/icon.component';

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, ToastComponent, IconComponent],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  title = 'frontend';
}
