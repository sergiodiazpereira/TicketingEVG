import os
import re

import_icon = "import { IconComponent } from '../../../components/icon/icon.component';\n"
import_icon_short = "import { IconComponent } from '../../components/icon/icon.component';\n"

for root, _, files in os.walk('D:/DAW/ProyectoTicketing/TicketingEVG/src/frontend/src/app'):
    for f in files:
        if f.endswith('.component.ts') and 'icon.component' not in f and 'toast.component' not in f:
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8') as file:
                content = file.read()
            
            # Count depths to get correct path
            depth = path.count('\\') - 'D:\\DAW\\ProyectoTicketing\\TicketingEVG\\src\\frontend\\src\\app'.count('\\')
            icon_path = '../' * (depth - 1) + 'components/icon/icon.component' if depth > 1 else './components/icon/icon.component'
            
            import_statement = f"import {{ IconComponent }} from '{icon_path}';\n"
            
            if 'IconComponent' not in content:
                # Add import after the last import
                last_import_idx = content.rfind('import ')
                if last_import_idx != -1:
                    end_of_line = content.find('\n', last_import_idx)
                    content = content[:end_of_line+1] + import_statement + content[end_of_line+1:]
                
                # Add to imports array
                content = re.sub(r'imports:\s*\[(.*?)\]', lambda m: f"imports: [{m.group(1)}{', IconComponent' if m.group(1).strip() else 'IconComponent'}]", content)
                
                with open(path, 'w', encoding='utf-8') as file:
                    file.write(content)
                print(f'Added IconComponent import to {path}')
