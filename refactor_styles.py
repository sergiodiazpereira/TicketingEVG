import os
import re

def extract_styles_to_css(html_path, css_path, prefix):
    with open(html_path, 'r', encoding='utf-8') as f:
        html_content = f.read()

    style_pattern = re.compile(r'style="([^"]+)"')
    styles_found = []
    
    def replacer(match):
        style_content = match.group(1).strip()
        if not style_content:
            return ''
        class_name = f"{prefix}-{len(styles_found) + 1}"
        styles_found.append(f".{class_name} {{\n    {style_content.replace(';', ';\n   ').strip()}\n}}")
        # Check if the tag already has a class attribute
        # We handle this crudely by adding a new class attribute, if it has one it will have two class="" which is invalid.
        # Actually, let's just do a string replacement on the whole tag later.
        return f'class="{class_name}"'

    # This regex is safer: it replaces style="..." with a new class. If there's an existing class, it might create a second class="...", which angular handles fine but isn't strictly valid HTML.
    # Let's do a better replacement:
    def tag_replacer(match):
        tag = match.group(0)
        style_match = re.search(r'style="([^"]+)"', tag)
        if not style_match:
            return tag
            
        style_content = style_match.group(1).strip()
        class_name = f"{prefix}-{len(styles_found) + 1}"
        styles_found.append(f".{class_name} {{\n    {style_content}\n}}")
        
        tag_without_style = tag[:style_match.start()] + tag[style_match.end():]
        class_match = re.search(r'class="([^"]+)"', tag_without_style)
        
        if class_match:
            # Append to existing class
            new_classes = f'class="{class_match.group(1)} {class_name}"'
            new_tag = tag_without_style[:class_match.start()] + new_classes + tag_without_style[class_match.end():]
        else:
            # Add new class
            insert_pos = tag_without_style.find(' ')
            if insert_pos == -1: insert_pos = tag_without_style.find('>')
            new_tag = tag_without_style[:insert_pos] + f' class="{class_name}"' + tag_without_style[insert_pos:]
            
        return new_tag

    new_html = re.sub(r'<[^>]+style="[^"]+"[^>]*>', tag_replacer, html_content)
    
    if styles_found:
        with open(html_path, 'w', encoding='utf-8') as f:
            f.write(new_html)
            
        with open(css_path, 'a', encoding='utf-8') as f:
            f.write("\n/* Extracted Inline Styles */\n")
            f.write("\n\n".join(styles_found))
            f.write("\n")
        print(f"Extracted {len(styles_found)} styles from {html_path}")

base_dir = 'D:/DAW/ProyectoTicketing/TicketingEVG/src/frontend/src/app/pages'
extract_styles_to_css(
    f"{base_dir}/modales/modal-ticket/modal-ticket.component.html", 
    f"{base_dir}/modales/modal-ticket/modal-ticket.component.css", 
    "extracted-mt"
)
extract_styles_to_css(
    f"{base_dir}/admin/categorias/categorias.component.html", 
    f"{base_dir}/admin/categorias/categorias.component.css", 
    "extracted-cat"
)
extract_styles_to_css(
    f"{base_dir}/admin/operarios/operarios.component.html", 
    f"{base_dir}/admin/operarios/operarios.component.css", 
    "extracted-op"
)
extract_styles_to_css(
    f"{base_dir}/usuario/crear-ticket/crear-ticket.component.html", 
    f"{base_dir}/usuario/crear-ticket/crear-ticket.component.css", 
    "extracted-ct"
)
extract_styles_to_css(
    f"{base_dir}/modales/formulario-categoria/formulario-categoria.component.html", 
    f"{base_dir}/modales/formulario-categoria/formulario-categoria.component.css", 
    "extracted-fc"
)
