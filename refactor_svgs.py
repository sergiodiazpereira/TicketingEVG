import os
import re

svg_pattern = re.compile(r'<svg[^>]*>.*?</svg>', re.DOTALL)

def svg_replacer(match):
    svg_content = match.group(0).lower()
    if 'polyline points="20 6 9 17 4 12"' in svg_content or 'fa-circle-check' in svg_content:
        return '<app-icon name="save" size="18"></app-icon>'
    elif 'polyline points="22 4 12 14.01 9 11.01"' in svg_content:
        return '<app-icon name="resolve" size="18"></app-icon>'
    elif 'circle cx="12" cy="12" r="10"' in svg_content and 'polyline points="12 6 12 12 16 14"' in svg_content:
        return '<app-icon name="process" size="18"></app-icon>'
    elif 'circle cx="12" cy="12" r="10"' in svg_content and 'line x1="15" y1="9" x2="9" y2="15"' in svg_content:
        return '<app-icon name="cancel" size="18"></app-icon>'
    elif 'path d="m21 15a2 2 0 0 1-2 2h7l-4 4v5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"' in svg_content:
        return '<app-icon name="chat" size="18"></app-icon>'
    elif 'path d="m20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0l2 12v2h10l8.59 8.59a2 2 0 0 1 0 2.82z"' in svg_content:
        return '<app-icon name="tag" size="14" cssClass="label-icon"></app-icon>'
    elif 'path d="m21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"' in svg_content:
        return '<app-icon name="info" size="18" cssClass="info-icon" color="#eab308"></app-icon>'
    elif 'path d="m20 21v-2a4 4 0 0 0-4-4h8a4 4 0 0 0-4 4v2"' in svg_content:
        return '<app-icon name="user" size="22"></app-icon>'
    elif 'path d="m11 4h4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"' in svg_content:
        return '<app-icon name="edit" size="14"></app-icon>'
    elif 'line x1="22" y1="2" x2="11" y2="13"' in svg_content:
        return '<app-icon name="send" size="14"></app-icon>'
    else:
        return '<app-icon name="info" size="18"></app-icon>' # Default fallback

for root, _, files in os.walk('D:/DAW/ProyectoTicketing/TicketingEVG/src/frontend/src/app'):
    for f in files:
        if f.endswith('.html') and 'icon.component' not in f:
            path = os.path.join(root, f)
            with open(path, 'r', encoding='utf-8') as file:
                content = file.read()
            new_content = re.sub(svg_pattern, svg_replacer, content)
            if new_content != content:
                with open(path, 'w', encoding='utf-8') as file:
                    file.write(new_content)
                print(f'Replaced SVGs in {path}')
