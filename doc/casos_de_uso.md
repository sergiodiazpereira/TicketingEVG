<!--
Proyecto: TicketingEVG
Alumno: Joseph Joel Quispe Alvarez
Asignatura: Desarrollo de Aplicaciones Web (DAW)
Curso: 2025-2026
Descripción: Documento con la especificación detallada de los Casos de Uso (CU) del sistema TicketingEVG.
-->

# Especificación de Casos de Uso - TicketingEVG

Este documento contiene la especificación formal de los **Casos de Uso (CU)** del sistema **TicketingEVG**. Está estructurado para servir como referencia técnica y facilitar la generación de documentación del proyecto.

---

## 1. Módulo de Autenticación e Integración SSO

### CU 1 - Login único vía SSO Intranet
* **Actores**: Cualquier usuario (Profesor, Alumno, Trabajador, Responsable, Administrador).
* **Flujo Principal**:
  1. El actor accede a la URL principal del Sistema o es redirigido desde el portal corporativo.
  2. El Sistema intercepta la petición entrante mediante `sso_catch.php`.
  3. El Sistema extrae y comprueba el token JWT remitido por la Intranet.
  4. Después de comprobar:
     * **Si es válido**: El Sistema redirige a la ruta callback del frontend (`/#/sso-callback?token=...`).
     * **Si es erróneo o inexistente**: El Sistema redirige directamente a la página de login corporativo de la Intranet.
  5. El Frontend Angular decodifica localmente el JWT y comprueba su fecha de expiración (`exp`).
  6. Después de comprobar:
     * **Si está vigente**: El Frontend inicia sesión localmente, guarda el token en `LocalStorage`, consulta el perfil del usuario e inicia el portal correspondiente según su rol.
     * **Si está caducado**: El Frontend muestra un mensaje de sesión inválida y devuelve al usuario de manera segura a la Intranet.

### CU 2 - Cierre de Sesión Seguro (Logout)
* **Actores**: Cualquier usuario.
* **Flujo Principal**:
  1. El actor pulsa el botón o enlace "Cerrar sesión" en la cabecera o barra lateral.
  2. El Sistema despliega una modal premium de confirmación preguntando *"¿Seguro que quieres cerrar sesión?"*.
  3. El actor selecciona una opción:
     * **Si pulsa Cancelar**: La modal se cierra y la sesión permanece activa.
     * **Si pulsa Confirmar**: El Sistema destruye el token de sesión (`LocalStorage`), borra el estado de autenticación y redirige a la URL de acceso del SSO.

---

## 2. Módulo de Solicitud y Gestión de Tickets (Profesores / Creadores)

### CU 3 - Crear Incidencia o Petición
* **Actores**: Profesor, Alumno, Trabajador (actuando como solicitante).
* **Flujo Principal**:
  1. El actor accede a la opción "Crear ticket" en el portal de usuario.
  2. El Sistema le muestra el formulario interactivo de creación de tickets.
  3. El actor selecciona el tipo de ticket (Incidencia o Petición) y rellena los campos: Asunto/Título, Descripción del problema, Categoría técnica y Ubicación (campo Opcional).
  4. El actor pulsa el botón "Enviar ticket".
  5. El Sistema ejecuta validaciones del lado del cliente en caliente (longitud mínima y presencia de espacios en blanco).
  6. Después de comprobar:
     * **Si cumple requisitos**: El Sistema envía la petición al backend. El backend genera el ID dinámico secuencial (e.g. `I2002230101` para Incidencias), inserta el registro en estado 'pendiente' y devuelve confirmación. El Sistema redirige al actor a su listado de tickets.
     * **Si no cumple**: El Sistema cancela el envío y muestra mensajes de validación detallados directamente debajo de cada input defectuoso.

### CU 4 - Editar Información Base de un Ticket
* **Actores**: Profesor (solo tickets creados por él), Responsable, Administrador.
* **Flujo Principal**:
  1. El actor hace clic sobre un ticket en su listado para abrir sus detalles.
  2. El actor pulsa el botón "Editar Detalles" (habilitado solo si se cumple el getter `puedeEditar`).
  3. El Sistema habilita la edición reactiva de los campos: Asunto, Descripción, Categoría, Ubicación y Prioridad.
  4. El actor modifica los datos y pulsa "Guardar Cambios".
  5. El Sistema valida los datos del lado del cliente y realiza la petición al backend.
  6. El backend procesa el cambio mediante `C_Ticket::actualizar` validando privilegios y guarda en base de datos.
  7. El Sistema muestra un mensaje de éxito flotante y refresca la vista del modal.

### CU 5 - Cancelar Ticket (Solicitante)
* **Actores**: Profesor (solo tickets creados por él).
* **Flujo Principal**:
  1. El actor abre el modal de detalles de su ticket activo.
  2. El actor pulsa el botón "Cancelar ticket" (visible únicamente si el ticket se encuentra en estado 'pendiente' o 'asignado' y no ha entrado en proceso de resolución).
  3. El Sistema despliega una modal de confirmación preguntando si desea cancelar la incidencia.
  4. El actor selecciona una opción:
     * **Si pulsa Cancelar**: La modal se cierra y el ticket permanece activo.
     * **Si pulsa Confirmar**: El Sistema envía la solicitud al backend, cambia el estado del ticket a 'no aplica', y devuelve al usuario a su listado de tickets.

---

## 3. Módulo de Operaciones Técnicas y Ciclo de Vida (Técnicos)

### CU 6 - Iniciar Proceso de Resolución
* **Actores**: Trabajador (Técnico Asignado), Responsable, Administrador.
* **Flujo Principal**:
  1. El actor abre el modal de detalles de un ticket que tiene asignado y que está en estado 'pendiente' o 'asignado'.
  2. El actor pulsa el botón "Iniciar Proceso".
  3. El Sistema realiza la petición HTTP al backend (`cambiar_estado`).
  4. El Sistema actualiza el estado del ticket a 'proceso' e inserta el cambio en la base de datos.
  5. El Sistema refresca el modal del ticket en caliente actualizando los badges de estado.

### CU 7 - Marcar Ticket como Resuelto
* **Actores**: Trabajador (Técnico Asignado), Responsable, Administrador.
* **Flujo Principal**:
  1. El actor abre el modal de detalles de un ticket que tiene asignado en estado 'proceso'.
  2. El actor pulsa el botón "Marcar Resuelto".
  3. El Sistema solicita confirmación (opcional) y envía la petición al backend.
  4. El backend cambia el estado del ticket a 'resuelto' bloqueando futuras ediciones.
  5. El Sistema muestra el mensaje "Ticket resuelto" y actualiza la tabla de tickets del usuario.

### CU 8 - Añadir Comentario o Nota Técnica
* **Actores**: Cualquier usuario con acceso al modal de detalles del ticket.
* **Flujo Principal**:
  1. El actor abre un ticket y visualiza el panel lateral adyacente de "Comentarios y Notas".
  2. El actor escribe un texto en la caja inferior de comentarios.
  3. El actor pulsa "Enviar" (o pulsa la tecla `Enter`).
  4. El Sistema valida que la nota no contenga solo espacios en blanco.
  5. Después de comprobar:
     * **Si tiene texto**: Envía el comentario al servidor. El backend lo registra en la tabla `Comentario` asociándolo al ticket y usuario. El frontend lo renderiza de inmediato al final del visor cronológico.
     * **Si está vacío**: El botón de envío se bloquea y se evita realizar la llamada HTTP.

---

## 4. Módulo de Asignaciones e Integridad (Supervisores y Admins)

### CU 9 - Asignar / Reasignar Operario Técnico
* **Actores**: Responsable, Administrador.
* **Flujo Principal**:
  1. El actor abre el modal de detalles de un ticket en estado no cerrado.
  2. El Sistema carga y despliega la lista de técnicos disponibles (filtrados automáticamente en base a las categorías especialistas del operario).
  3. El actor selecciona un operario de la lista desplegable.
  4. El Sistema realiza la petición de asignación al backend (`C_Ticket::asignar`).
  5. El backend registra la asignación del técnico en la tabla `Ticket` y cambia el estado del ticket a 'asignado' de forma automática si estaba 'pendiente'.
  6. El Sistema actualiza la interfaz mostrando la tarjeta con el nombre completo real del técnico.

### CU 10 - Desasignar Operario Técnico
* **Actores**: Responsable, Administrador.
* **Flujo Principal**:
  1. El actor abre un ticket asignado en modo lectura.
  2. El actor pulsa el botón de aspa ("X") ubicado en la tarjeta del Técnico Asignado.
  3. El Sistema solicita confirmación mediante un modal premium.
  4. El actor selecciona una opción:
     * **Si pulsa Cancelar**: Se cierra el diálogo y la asignación se mantiene intacta.
     * **Si pulsa Confirmar**: El Sistema envía la orden de desasignación al backend. El backend elimina al técnico asignado (`id_usuario_encargado = NULL`) y devuelve el ticket de forma automática al estado de 'pendiente'.

---

## 5. Módulo de Administración y Gestión de Personal (Admins)

### CU 11 - Registrar Nuevo Operario Técnico
* **Actores**: Administrador.
* **Flujo Principal**:
  1. El actor entra a la sección "Gestión de Operarios".
  2. El actor pulsa el botón "Añadir Operario".
  3. El Sistema abre un modal de formulario y lista dinámicamente el personal disponible en la Intranet institucional.
  4. El actor busca y selecciona el usuario a dar de alta.
  5. El actor selecciona su rango en la aplicación (Trabajador o Responsable) y marca las categorías en las que se especializa.
  6. El actor pulsa "Crear Operario".
  7. El Sistema comprueba que no existan duplicados y registra el nuevo operario en la tabla local `Usuario` junto con sus categorías en `Categoria_Usuario`.
  8. El Sistema muestra un mensaje de éxito y actualiza la lista de operarios en pantalla.

### CU 12 - Modificar Operario
* **Actores**: Administrador.
* **Flujo Principal**:
  1. El actor localiza al operario en el listado y pulsa el botón "Editar" (icono de lápiz).
  2. El Sistema abre el modal precargando los datos y especialidades actuales del técnico.
  3. El actor cambia el rol y añade o quita categorías.
  4. El actor pulsa "Guardar Cambios".
  5. Después de guardar:
     * El Sistema actualiza el rol en la base de datos.
     * Si se le remueve una categoría: El backend ejecuta la desasignación de todos los tickets activos que el técnico tuviera en esa área técnica restableciéndolos a 'pendiente' para preservar el flujo de trabajo.

### CU 13 - Eliminar Operario del Sistema
* **Actores**: Administrador.
* **Flujo Principal**:
  1. El actor pulsa "Eliminar" en el operario de la lista y confirma en la modal.
  2. El Sistema comprueba si el técnico tiene tickets activos asignados sin resolver.
  3. Después de comprobar:
     * **Si tiene tickets activos**: Muestra una alerta de bloqueo impidiendo la eliminación para evitar inconsistencias.
     * **Si no tiene**: Borra de forma segura sus registros en `Categoria_Usuario` y `Usuario`, marcando sus antiguos tickets cerrados como desasignados en cascada (`SET NULL`).

### CU 14 - Operaciones en Lote (Selección Múltiple)
* **Actores**: Administrador.
* **Flujo Principal**:
  1. El actor selecciona varios operarios o categorías marcando sus casillas de verificación.
  2. El Sistema muestra un botón flotante con el conteo de elementos seleccionados.
  3. El actor pulsa el botón y aprueba la modal de confirmación.
  4. El Sistema ejecuta peticiones concurrentes en paralelo usando el operador de RxJS `forkJoin`.
  5. El Sistema procesa y reúne todos los resultados, informando al administrador de forma pormenorizada cuántos elementos se eliminaron correctamente y cuántos fueron bloqueados por dependencias.

---

## 6. Módulo de Consultas y Búsquedas

### CU 15 - Filtrado Interactivo de Tickets
* **Actores**: Profesor, Trabajador, Responsable, Administrador.
* **Flujo Principal**:
  1. El actor introduce palabras clave en la barra de búsqueda o hace clic en los chips de filtrado personalizado (Tipo, Estado, Prioridad, Creador).
  2. El Sistema intercepta la interacción y filtra la lista de tickets localmente de forma reactiva (sin recargar la página).
  3. La lista muestra en pantalla únicamente los registros que coinciden simultáneamente con todos los criterios de búsqueda activos.

### CU 16 - Restricción Visual del Trabajador Creador
* **Actores**: Trabajador (actuando como creador del ticket, no asignado a sí mismo).
* **Flujo Principal**:
  1. El actor abre el modal detallado de un ticket que él mismo creó pero del cual no es el técnico responsable.
  2. El Sistema evalúa la expresión `debeVerComoProfesor` a `true`.
  3. El Sistema oculta del modal los botones técnicos de resolución y proceso y las opciones de reasignación.
  4. El Sistema muestra el modal en modo lectura idéntico al de un Profesor, permitiéndole editar el ticket o cancelarlo únicamente si el estado de este sigue en 'pendiente' o 'asignado'.
