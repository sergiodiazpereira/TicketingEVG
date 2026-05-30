---
trigger: always_on
---

Yo soy Joseph Joel Quispe Alvarez, para que lo pongas en las cabeceras ese es mi nombre.

Normas Generales
Todos los ficheros de código incluirán una cabecera comentada con la identificación completa del alumno, asignatura, curso y descripción del fichero.
Se evitará mezclar distintos lenguajes en un mismos fichero. Por ejemplo, se evitará incluir código CSS o JavaScript en páginas HTML y también se separará el código PHP del HTML y del SQL.
Los nombres utilizados para ficheros, variables, clases, etc, serán en castellano y descriptivos evitando el uso de acentos, ñ, diéresis y espacios. En general, se utilizarán caracteres de ASCII básico. Se admite el uso de abreviaturas habituales como num, cont, etc.
Todo el código estará adecuadamente comentado. Se buscará la legibilidad y claridad del código para reducir la necesidad de comentarios.
Todo el código estará indentado. Se utilizará tabuladores (TAB) a 8 espacios, omitiendo las llaves opcionales y líneas en blanco (identación K&R en su versión 1TBS.
Por ejemplo:

int main(int argc, char *argv[]){	//Llave en la misma línea
    		...
    		while (x == y) {
        			hacerAlgo();
        			hacerOtraCosa();

        			if (hayError)	//Sin llaves innecesarias
            			corregir();
        			else
            			continuar);
    		}
    		finalizar();
    		…
}	//Lave al mismo nivel de indentación que su función

Normas para Ficheros
La cabecera de cada fichero tendrá información para identificar el proyecto y el autor.
Los nombres de ficheros se escribirán en minúsculas.
Los ficheros se codificarán en UTF-8.
Normas para SQL
Las palabras reservadas de SQL se escribirán en mayúsculas (SELECT, FROM, CREATE TABLE…).
Los nombres de las tablas tendrán la primera letra mayúscula y serán en singular. Si el nombre se formará por varias palabras se utilizará notación C (con subrayado bajo). Por ejemplo Usuario o Usuario_Acceso.
Los nombres de los campos serán en minúsculas. Si el nombre se formara por varias palabras se utilizará notación C.
Las claves sintéticas de las tablas tendrán el nombre “id”.
Las claves externas de las tablas que hagan referencia a claves sintéticas se formarán como id_Nombre_Tabla.
Las tablas de relación se llamarán Tabla1_Tabla2, ordenando las entidades alfabéticamente: Ejemplo: Alumno_Curso
Se utilizarán restricciones de claves externas para asegurar la integridad referencial.
Normas para HTML
Las marcas de html se escribirán en minúsculas.
El código cumplirá las reglas de validación de XML (etiquetas correctamentamente cerradas).
Las etiquetas que requieran varias líneas se iniciarán y terminarán con la etiqueta sola en la línea al mismo nivel de tabulación. Por ejemplo:
<p>
Texto muy largo…
</p>
Los ficheros de estilo se cargarán en la cabecera del documento (<head>).
Los ficheros de JavaScript se cargarán en al final del cuerpo del documento (antes de </body>).

Normas para CSS
Sin especificar


Normas para JavaScript
Se utilizará notación camelCase.
Las clases se nombrarán con la primera letra mayúscula. Por ejemplo: Usuario, UsuarioAdministracion.
Las variables y objetos se nombrarán con minúsculas. Por ejemplo: contador, fechaApertura.
Las constantes se escribirán íntegramente en mayúsculas y en notación C. Por ejemplo: FICHERO, FICHERO_USUARIOS

Normas para PHP
Se utilizará notación C (con subrayado bajo).
Las clases se nombrarán con la primera letra mayúscula. Por ejemplo: Usuario, Usuario_Administracion.
Las variables y objetos se nombrarán con minúsculas. Por ejemplo: $contador, $fecha_apertura.
Las constantes se escribirán íntegramente en mayúsculas. Por ejemplo: FICHERO, FICHERO_USUARIOS

### Apuntes Técnicos y Decisiones de Arquitectura
*   **Integración SSO (Intranet):**
    *   **Interceptor POST/GET:** La Intranet envía el JWT mediante una petición POST (o parámetros GET) a la ruta raíz. Dado que Angular es una SPA, pierde estos datos al cargar. Se implementó `sso_catch.php` para atrapar estas peticiones antes de cargar el frontend y realizar una redirección Hash (`/#/sso-callback?token=...`) que Angular pueda leer.
    *   **Validación Local en Frontend:** Debido a problemas con el historial y la caché del navegador que reenviaban tokens antiguos caducados, Angular (`sso-callback.component.ts`) decodifica el token localmente para revisar su fecha de expiración (`exp`). Si está caducado, el usuario es devuelto a la Intranet de forma transparente antes de molestar al backend.
    *   **Validación Backend (`C_Auth.php`):** La validación de la firma del token se hace de forma robusta con Firebase JWT. El `id` y el `rol` mapeado del JWT de la Intranet gobiernan los permisos de acceso en TicketingEVG.
    *   **Gestión de Entorno (`.env`):** Las claves JWT de la intranet se guardan en `.env`. Se ha abandonado el uso de `parse_ini_file()` en `Conexion.php` en favor de un parseo manual, evitando errores fatales con comentarios (`#`) introducidos en PHP 8.

*   **Permisos y Ciclo de Vida de los Tickets:**
    *   **Usuarios Solicitantes (Rol null / 'profesor'):** 
        *   Pueden crear tickets.
        *   Pueden editar sus propios tickets únicamente si no han entrado en estado `proceso`, `resuelto` o `no aplica` (es decir, en `pendiente` o `asignado`).
        *   Pueden cancelar sus tickets (pasarlos a estado `no aplica`) siempre y cuando no estén en `proceso`. Requiere confirmación modal en el frontend.
        *   Tienen terminantemente prohibido marcar tickets como `resuelto`.
    *   **Usuarios Técnicos (Operario, Responsable, Administrador):**
        *   Pueden editar cualquier ticket en curso.
        *   Pueden cambiar el estado de los tickets, incluyendo marcarlos como `resuelto` o cancelarlos pasándolos a `no aplica`.

*   **Mapeo de Nombres Reales y Desasignación:**
    *   **Backend (`M_Ticket.php`):** Se mapean los IDs del encargado (`id_usuario_encargado`) y del creador (`id_usuario_creador`) a sus nombres reales en texto plano (`encargado_nombre`, `creador_nombre`) extrayéndolos desde `M_Intranet::listar_personal()`, evitando así mostrar cadenas como "TRABAJADOR 31" en la interfaz.
    *   **Desasignación Completa:** Se modificaron los métodos del backend y frontend para permitir pasar el valor `0` o `null` en la asignación de técnicos, devolviendo el ticket al estado de `pendiente` de forma automática.

*   **Edición Dinámica In-Place y Controladores:**
    *   **Dualidad del Modal:** Se creó el Modo Lectura / Modo Edición (Opción A) en el modal unificado de tickets, permitiendo la edición reactiva de Título, Descripción, Ubicación, Categoría y Prioridad bajo la validación del getter `puedeEditar`.
    *   **Compatibilidad de Roles ('admin' vs 'administrador'):** Se corrigió la discrepancia de roles en `C_Ticket::asignar()` admitiendo de manera transparente ambos alias para evitar el bloqueo del backend al reasignar técnicos.

*   **Hilo Cronológico de Comentarios y Notas Técnicas:**
    *   **Persistencia:** Implementada la tabla `Comentario` asociada a `Ticket` y `Usuario` mediante claves externas en cascada.
    *   **Comunicación Interna y Diseño Ergonométrico:** Diseñado un feed de comentarios con envío dinámico reactivo integrado. El panel de comentarios se posiciona de forma **adyacente (lado a lado)** al modal de detalles principales, evitando la saturación vertical del modal principal y asegurando que ninguna información técnica quede fuera del visor de pantalla. En resoluciones pequeñas, el layout se apila responsivamente de forma vertical de manera elegante.

*   **Superposición de Modales y Captura de Eventos (Event Bubbling):**
    *   **Overflow Clipping:** Los modales colocados dentro de contenedores transformados (`transform: scale(...)`) con animaciones de apertura sufren recortes debido al contexto de apilamiento CSS. Se extrajeron los modales personalizados de confirmación (`<app-confirmacion-eliminar>`) como hermanos externos del contenedor animado.
    *   **Propagación de Clics:** Se implementó `(click)="$event.stopPropagation()"` en los límites de los modales internos para evitar que el evento click suba por el DOM y active el cierre accidental del modal padre administrado por `@HostListener('click')`.

*   **Plan de Mejoras de Seguridad, Funcionalidad y Auditoría (Roadmap):**
    *   **Historial de Auditoría (`Ticket_Historial`):** Diseñado el esquema relacional y de auditoría para la nueva tabla `Ticket_Historial` que registrará los hitos de ciclo de vida del ticket (creación, cambio de estado, cambio de prioridad, asignación y desasignación de técnico). Su feed visual se acoplará dinámicamente como línea de tiempo en el frontend.
    *   **Adjuntos en Comentarios (Subida de Archivos):** Estructurada la adición de las columnas `archivo_nombre` y `archivo_ruta` en la tabla `Comentario` para adjuntar capturas de pantalla o documentos técnicos. Los archivos se guardarán de manera segura en `uploads/comentarios/` y su descarga pasará por el validador `C_Adjunto.php` para verificar privilegios antes de servir.
    *   **Sesiones Blindadas con Cookies HttpOnly**: Planificada la migración de JWT de LocalStorage a cookies de servidor `HttpOnly`, `Secure` y `SameSite=Strict` emitidas en `sso_catch.php`. Angular consumirá la sesión automáticamente mediante `{ withCredentials: true }`, mitigando cualquier vulnerabilidad de scripts maliciosos (XSS).
    *   **Notificaciones por Correo Electrónico (PHPMailer)**: Integración de la biblioteca PHPMailer con credenciales SMTP seguras cargadas desde el fichero `.env`. Automatizará avisos de creación de tickets, reasignaciones y resolución al instante.