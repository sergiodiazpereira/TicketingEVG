import { Component, Input } from '@angular/core';
import { CommonModule } from '@angular/common';

@Component({
  selector: 'app-icon',
  standalone: true,
  imports: [CommonModule],
  templateUrl: './icon.component.html'
})
export class IconComponent {
  @Input() name!: string;
  @Input() color: string = 'currentColor';
  @Input() size: string = '24';
  @Input() strokeWidth: string = '2';
  @Input() cssClass: string = '';
}
