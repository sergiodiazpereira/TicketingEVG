import os
import re

app_dir = 'D:/DAW/ProyectoTicketing/TicketingEVG/src/frontend/src/app'

for root, _, files in os.walk(app_dir):
    for f in files:
        if f.endswith('.component.ts') and 'icon.component' not in f and 'toast.component' not in f:
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8') as file:
                content = file.read()
            
            # Calculate relative path from current file to components/icon/icon.component
            # path is something like D:/.../src/app/pages/acceso/acceso.component.ts
            # root is D:/.../src/app/pages/acceso
            
            rel_path = os.path.relpath(os.path.join(app_dir, 'components', 'icon', 'icon.component'), root)
            rel_path = rel_path.replace('\\', '/')
            if not rel_path.startswith('.'):
                rel_path = './' + rel_path
                
            # Replace the wrong import
            new_content = re.sub(r"import \{ IconComponent \} from '.*?';", f"import {{ IconComponent }} from '{rel_path}';", content)
            
            if new_content != content:
                with open(path, 'w', encoding='utf-8') as file:
                    file.write(new_content)
                print(f'Fixed import in {path}')
