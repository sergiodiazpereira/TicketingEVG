import os
import re

toast_html_pattern = re.compile(r'<!-- Toast Alerta Premium.*?<div class="toast-alerta-progreso"></div>\s*</div>', re.DOTALL)
toast_html_pattern2 = re.compile(r'<div \*ngIf="mensajeFeedback" class="toast-alerta-contenedor".*?<div class="toast-alerta-progreso"></div>\s*</div>', re.DOTALL)
toast_html_pattern3 = re.compile(r'<div \*ngIf="mensajeError" class="toast-alerta-contenedor toast-error".*?<div class="toast-alerta-progreso"></div>\s*</div>', re.DOTALL)
toast_html_pattern4 = re.compile(r'<div \*ngIf="mensajeExito" class="toast-alerta-contenedor toast-exito".*?<div class="toast-alerta-progreso"></div>\s*</div>', re.DOTALL)

for root, _, files in os.walk('D:/DAW/ProyectoTicketing/TicketingEVG/src/frontend/src/app'):
    for f in files:
        if f.endswith('.html'):
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8') as file:
                content = file.read()
            new_content = re.sub(toast_html_pattern, '', content)
            new_content = re.sub(toast_html_pattern2, '', new_content)
            new_content = re.sub(toast_html_pattern3, '', new_content)
            new_content = re.sub(toast_html_pattern4, '', new_content)
            if new_content != content:
                with open(path, 'w', encoding='utf-8') as file:
                    file.write(new_content)
                print(f'Cleaned HTML toasts in {path}')
