# Reporte de Inconsistencias y Buenas Prácticas - Proyecto TicketingEVG

Este documento presenta un análisis exhaustivo de las inconsistencias técnicas, estructurales y de codificación identificadas en el proyecto **TicketingEVG**. Abarca desde la duplicación de código y archivos hasta la discrepancia en la nomenclatura de base de datos y hojas de estilo, cruzando los hallazgos con las normas estipuladas en el [Manual del programador](Manual%20del%20programador.txt).

---

## 1. Duplicidad de Archivos y Redundancia Estructural

Se ha detectado una gran redundancia en la estructura de directorios del proyecto, con carpetas completas que duplican el código fuente:

*   **Mockups vs Vistas Reales**: La carpeta `diseno/mockups/` contiene copias casi exactas de todos los archivos PHP y HTML que se encuentran en `src/vistas/`. Esto duplica el esfuerzo de mantenimiento, ya que cualquier cambio funcional o visual en el portal obliga a actualizar ambas carpetas de forma manual.
*   **Duplicidad de Estilos Base**: Los archivos `estiloAdmin.css` y `estiloUsuario.css` en `src/css` se encuentran duplicados de forma idéntica en `src/frontend/src/styles/`. 

---

## 2. Inconsistencias y Fragmentación en las Hojas de Estilo (CSS)

La integración de estilos presenta problemas de uniformidad y una alta fragmentación:

### A. Diferencias en la Paleta de Colores y Nombres de Variables
El archivo `admin.css` (importado globalmente por Angular en `styles.css`) define una paleta y nombres de variables en `:root` que chocan con los de `estiloAdmin.css`:

| Propiedad / Propósito | Variable en `admin.css` | Variable en `estiloAdmin.css` |
| :--- | :--- | :--- |
| **Color Azul Oscuro** | `--azul-oscuro: #003366` | `--azul-oscuro: #014179` |
| **Color Azul Base** | `--azul-btn: #006ea4` | `--azul: #006ea4` |
| **Fondo Gris** | `--fondo-gris: #f4f7fa` | `--gris-fondo: #f4f7fa` |
| **Borde** | `--borde-suave: #eef2f7` | `--borde: #e4e7ef` |

> [!WARNING]
> La inversión del orden de palabras (`--fondo-gris` vs `--gris-fondo`) y la diferencia en el tono azul principal (`#003366` vs `#014179`) pueden generar un aspecto visual incoherente si se mezclan componentes o vistas que utilicen una u otra hoja de estilo.

### B. Uso de Nesting CSS y Clases Personales (`.joseph`, `.sergio`, `.manuel`)
Para evitar conflictos de estilos al fusionar el trabajo de los tres alumnos, se encapsularon las reglas bajo selectores específicos en el mismo archivo:
*   `estiloAdmin.css` y `estiloUsuario.css` contienen bloques enteros envueltos en `.joseph { ... }`, `.manuel { ... }` y `.sergio { ... }`.
*   Esto requiere inyectar la clase en la etiqueta principal del documento de cada vista (ej. `<html class="joseph" lang="es">` en `dashboard.php` o `<html class="sergio" lang="es">` en `acceso.php`).

**Impacto en la Migración Angular**:
*   En Angular, las hojas de estilo de los componentes ya están encapsuladas por defecto (Emulated View Encapsulation).
*   Para migrar las pantallas, se ha tenido que duplicar/extraer fragmentos de código CSS, eliminando o adaptando las clases personales `.sergio` o `.manuel` en archivos como `portal-tickets.component.css` y `formulario-operario.component.css`.
*   Esto provoca que el código CSS base del proyecto y el del frontend Angular se bifurquen por completo, haciendo imposible propagar mejoras de diseño automáticamente.

---

## 3. Discrepancias en el Esquema de Base de Datos (SQL)

El archivo de base de datos `creacion_bd.sql` presenta desviaciones directas respecto al *Manual del Programador*:

> [!IMPORTANT]
> **Regla de Nomenclatura del Manual**: *"Los nombres de los campos serán en minúsculas. Si el nombre se formara por varias palabras se utilizará notación C."*

### Desviaciones identificadas:
1.  **Uso de Mayúsculas (CamelCase)**: Se han creado campos con letras mayúsculas en lugar de minúsculas puras separadas por guion bajo:
    *   `id_Rol` (en tabla `Usuario`) $\rightarrow$ Debería ser `id_rol` o `id_Usuario` (ya que la tabla es `Rol`).
    *   `id_Categoria` (en tablas `Categoria_Usuario` y `Ticket`) $\rightarrow$ Debería ser `id_categoria`.
    *   `id_Usuario` (en tabla `Categoria_Usuario`) $\rightarrow$ Debería ser `id_usuario`.
    *   `id_Usuario_Creador` y `id_Usuario_Encargado` (en tabla `Ticket`) $\rightarrow$ Debería ser `id_usuario_creador` y `id_usuario_encargado`.
2.  **Palabras reservadas secundarias en minúscula**:
    *   En consultas internas como `M_Ticket.php` se utiliza `as` en minúscula (`COUNT(*) as total`) cuando la regla exige escribir las palabras reservadas de SQL íntegramente en mayúsculas (`AS`).

---

## 4. Acoplamiento Arquitectónico (Traducción en el Backend)

Como consecuencia directa de las inconsistencias en los nombres de variables y claves externas entre el Frontend (Angular) y el Backend (Base de Datos / PHP), el enrutador central de la API ha tenido que implementar una lógica de "traducción" ad-hoc.

En `src/backend/index.php` se puede observar el siguiente bloque de código:

```php
// 2. Si el parámetro en PHP es 'id_usuario', buscar 'usuario_id' en la petición de Angular
else {
    $variante1 = str_replace('id_', '', $nombre) . '_id';
    if (isset($datos[$variante1])) {
        $valor = $datos[$variante1];
    } 
    // 3. Si el parámetro en PHP es 'usuario_id', buscar 'id_usuario' en la petición
    else {
        $variante2 = 'id_' . str_replace('_id', '', $nombre);
        if (isset($datos[$variante2])) {
            $valor = $datos[$variante2];
        }
    }
}
```

> [!NOTE]
> Este "parche" de código en el enrutador es el síntoma de una inconsistencia de diseño de API: el frontend envía campos formateados de una manera (ej. `usuario_id`), pero la base de datos y los parámetros del controlador PHP esperan otro formato (ej. `id_usuario`). Estandarizar la nomenclatura eliminaría la necesidad de este procesamiento extra en cada petición.

---

## 5. Formato de Código e Indentación

El estilo de codificación en múltiples archivos no se ajusta rigurosamente a las normas generales del manual:

*   **Indentación**: La norma exige: *"Se utilizará tabuladores (TAB) a 8 espacios, omitiendo las llaves opcionales y líneas en blanco."*
    *   La mayoría de los ficheros de TypeScript (`.ts`) y hojas de estilo de Angular (`.component.css`) utilizan **2 o 4 espacios** para la indentación en lugar de tabuladores físicos de 8 espacios.
    *   El archivo de enrutador `index.php` y los controladores utilizan espacios en lugar de tabuladores.
*   **Cabeceras de Archivos**:
    *   Algunos archivos generados recientemente carecen de cabeceras de autor completas (ej. `admin.css` no cuenta con el formato estándar completo de Asignatura, Curso y Alumno).

---

## 6. Recomendaciones de Consolidación

Para unificar y asegurar la calidad del proyecto a largo plazo, se sugieren las siguientes acciones:

1.  **Eliminar la carpeta `mockups`**: Una vez validadas las interfaces, se debe mantener una única fuente de verdad en `src/vistas` para evitar discrepancias y código fantasma.
2.  **Consolidar las Hojas de Estilo en Angular**: Reemplazar la importación global de `admin.css` por un sistema de variables CSS centralizado en `styles.css` que use una única paleta armonizada.
3.  **Refactorizar los nombres de columnas de BD**: Aplicar la notación C pura en minúsculas en `creacion_bd.sql` (`id_rol`, `id_usuario_creador`, etc.) y corregir las referencias en los modelos PHP. Esto permitirá eliminar la lógica de traducción temporal de `index.php`.
4.  **Configurar Reglas de Linter (ESLint / Prettier)**: Establecer una configuración de editor (`.editorconfig`) que fuerce el uso de Tabuladores (TAB) para cumplir de manera automatizada con las directrices de entrega del curso.
