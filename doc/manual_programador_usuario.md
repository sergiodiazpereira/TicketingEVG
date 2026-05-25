<!--
Proyecto: TicketingEVG
Alumno: Joseph Joel Quispe Alvarez
Asignatura: Desarrollo de Aplicaciones Web (DAW)
Curso: 2025-2026
Descripción: Manual del programador detallado sobre el módulo de Gestión de Usuarios y Operarios.
-->

# Manual del Programador - Módulo de Gestión de Usuarios y Operarios

Este documento sirve como guía técnica y de referencia para los desarrolladores que mantengan, extiendan o auditen el módulo de **Gestión de Usuarios y Operarios** del proyecto **TicketingEVG**. 

El módulo abarca desde el diseño del esquema relacional en la base de datos hasta la interfaz del portal de administración desarrollada con Angular, pasando por la capa de lógica de negocio y controladores REST de la API en PHP.

---

## 1. Arquitectura General y Flujo de Datos

El sistema sigue una arquitectura desacoplada basada en un **Frontend de Cliente Único (SPA)** con Angular y un **Backend de API de Servicios** en PHP estructurado bajo el patrón **MVC (Modelo-Vista-Controlador)**.

```mermaid
graph TD
    subgraph Frontend (Angular)
        V[Vistas/HTML] -->|Interacciones| C[Componente: OperariosComponent]
        C -->|Validaciones/Reactivos| F[FormularioOperarioComponent]
        C -->|Llamadas HTTP| S[Servicio: UsuarioService]
    end

    subgraph Backend (PHP MVC API)
        S -->|Peticiones REST| R[Router Central: index.php]
        R -->|Reflexión y Mapeo| Ctrl[Controlador: C_Usuario]
        Ctrl -->|Instancia y Llama| Mod[Modelo: M_Usuario]
        Mod -->|Consultas SQL / Prep. Statements| DB[(Base de Datos: MySQL)]
    end
```

El flujo de datos para cualquier acción sigue la siguiente secuencia:
1. El usuario interactúa con la interfaz de administración de operarios (`operarios.component.html`).
2. El componente correspondiente gestiona el estado y delega en `usuario.service.ts` para enviar una petición HTTP (usando verbos semánticos como `GET`, `POST`, `PUT`, `DELETE`).
3. El enrutador central `index.php` recibe la petición, determina el controlador y método a llamar usando reflexión, unifica los parámetros y maneja posibles diferencias de nomenclatura.
4. El controlador (`C_Usuario.php`) valida los datos de entrada y ejecuta el método de persistencia del modelo.
5. El modelo (`M_Usuario.php`) ejecuta las sentencias SQL parametrizadas para interactuar con la base de datos de manera segura y retorna los resultados al controlador.
6. El enrutador serializa la respuesta en formato JSON de vuelta al cliente Angular.

---

## 2. Capa de Datos (SQL y Esquema de Base de Datos)

El esquema de la base de datos garantiza la integridad referencial y sigue estrictamente las normas del proyecto. Las tablas relacionadas con este módulo son:

### A. Tabla `Rol`
Define los niveles de acceso del sistema. Los roles por defecto para los operarios son `responsable`, `trabajador` y `operario`.
* **Clave Primaria (Sintética)**: `id` (`TINYINT UNSIGNED AUTO_INCREMENT`)
* **Campos**: `nombre` (`VARCHAR(50) NOT NULL`)

### B. Tabla `Usuario`
Almacena el registro principal de las cuentas de usuario y operarios del sistema.
* **Clave Primaria (Sintética)**: `id` (`SMALLINT UNSIGNED AUTO_INCREMENT`)
* **Campos**:
  - `nombre` (`VARCHAR(50) NOT NULL`): Nombre completo del usuario.
  - `correo` (`VARCHAR(100) NOT NULL`): Dirección de correo electrónico única (`UQ_correo`).
  - `activo` (`BIT NOT NULL DEFAULT 1`): Indica si la cuenta está habilitada.
  - `visitas_totales` (`SMALLINT UNSIGNED NOT NULL DEFAULT 0`): Registro acumulado de accesos del usuario.
  - `id_rol` (`TINYINT UNSIGNED NOT NULL`): Clave externa hacia `Rol`.
* **Restricción de Integridad (`FK_Usuario_Rol`)**:
  - Clave externa referenciando a `Rol(id)`.
  - Acción de eliminación: `ON DELETE RESTRICT` (impide borrar roles con usuarios asignados).
  - Acción de actualización: `ON UPDATE CASCADE`.

### C. Tabla `Categoria_Usuario` (Tabla de Relación)
Entidad de relación N:M que asocia operarios con las categorías de tickets de las cuales son especialistas.
* **Clave Primaria (Sintética)**: `id` (`SMALLINT UNSIGNED AUTO_INCREMENT`)
* **Claves Externas**:
  - `id_categoria` (`TINYINT UNSIGNED NOT NULL`) $\rightarrow$ Referencia a `Categoria(id)`.
  - `id_usuario` (`SMALLINT UNSIGNED NOT NULL`) $\rightarrow$ Referencia a `Usuario(id)`.
* **Restricción de Unicidad**: `UQ_Categoria_usuario` sobre la combinación de (`id_categoria`, `id_usuario`), evitando asignaciones duplicadas.
* **Restricciones de Integridad**:
  - `FK_Categoria_Usuario_Categoria` y `FK_Categoria_Usuario_Usuario` con `ON DELETE CASCADE ON UPDATE CASCADE`. Al borrar un usuario o una categoría, se eliminan en cascada de forma automática sus asociaciones de especialidad.

### D. Relación con la Tabla `Ticket`
Los usuarios están vinculados a los tickets bajo dos roles:
* `id_usuario_creador` (`SMALLINT UNSIGNED NOT NULL`): Usuario que reporta la incidencia.
* `id_usuario_encargado` (`SMALLINT UNSIGNED DEFAULT NULL`): Operario asignado a resolverla.
* **Integridad Referencial para el Encargado**:
  - `FK_Ticket_Usuario_Encargado` utiliza `ON DELETE SET NULL ON UPDATE CASCADE`. Si un operario es eliminado del sistema, los tickets que tenía asignados no se borran; en su lugar, el campo de encargado se establece en `NULL` (quedando huérfanos para reasignación).

---

## 3. Capa de Backend (PHP)

El código del backend está diseñado para ser reutilizable, modular y seguro. Se encuentra en la carpeta `src/backend/` y utiliza la extensión `mysqli` para interactuar con la base de datos de manera eficiente.

### A. Conexión (`config/Conexion.php`)
Implementa el patrón **Singleton** para asegurar una única instancia de conexión activa por ciclo de vida de la petición PHP.
* Carga de variables de entorno mediante un archivo `.env` dinámico (soporta configuración de puerto `3307`, servidor, usuario y contraseña de forma flexible).
* Establece el juego de caracteres de forma estricta a `utf8mb4` para la compatibilidad total de codificación de caracteres.

### B. Modelo (`Models/M_Usuario.php`)
Encapsula la lógica de negocio y persistencia para los datos del usuario. Principales métodos implementados:

1. **`listar_operarios()`**:
   * Ejecuta una consulta compleja uniendo la tabla `Usuario` con `Rol` y realizando subconsultas correlacionadas.
   * Calcula de forma dinámica el número de categorías asociadas a cada operario (`num_categorias`), concatena sus nombres (`categorias_nombres` mediante `GROUP_CONCAT`) y el número de tickets asignados no resueltos (`tickets_asignados`).
   * Filtra únicamente aquellos con roles de soporte técnico (`responsable`, `trabajador`, `operario`).
2. **`get_estadisticas()`**:
   * Recopila un mapa de métricas globales del sistema como visitas totales, total de usuarios, tickets activos, acumulado por prioridades (`a`, `m`, `b`) y número de operarios disponibles (aquellos que no tienen tickets en estado `asignado` o `proceso`).
3. **`crear($datos)`** y **`actualizar($id, $datos)`**:
   * Traducen el rol textual a su correspondiente clave referencial `id_rol` mediante un ayudante interno `obtener_id_rol()`.
   * Hacen uso de **Sentencias Preparadas (`Prepared Statements`)** para mitigar cualquier riesgo de inyección SQL.
4. **`eliminar($id)`**:
   * Implementa una validación lógica crítica antes de borrar: ejecuta una subconsulta para verificar si el operario cuenta con tickets activos (`estado != 'resuelto'`). Si el conteo es mayor a `0`, cancela la operación de forma segura y devuelve un mensaje informativo detallado.
   * Si no tiene tickets activos, borra en primer lugar sus relaciones de especialidad de la tabla `Categoria_Usuario` y finalmente elimina el registro en la tabla `Usuario`.
5. **`asignar_categorias($id_usuario, $ids_categorias)`**:
   * Maneja de manera limpia la actualización de especialidades. Primero ejecuta un borrado global para el `id_usuario` dado y, a continuación, recorre el array de categorías seleccionadas insertándolas de forma individual.

### C. Controlador (`Controllers/C_Usuario.php`)
Actúa como intermediario directo entre la API y el modelo.
* **`guardar($datos)`**:
  - Valida la presencia de campos obligatorios (`nombre`, `correo`, `rol`).
  - Determina si la acción es una creación o una actualización según la presencia o ausencia de la propiedad `id`.
  - Coordina la transacción lógica: guarda los datos base del operario y actualiza su matriz de categorías especialistas.

### D. Enrutador Dinámico (`index.php`)
Una sola puerta de entrada recibe todas las peticiones desde el frontend.
* Implementa la unificación de datos (`$_GET`, `$_POST` y el cuerpo crudo de la petición `php://input` decodificado).
* Utiliza **Reflexión (`ReflectionMethod`)** para mapear automáticamente los parámetros dinámicos esperados por el controlador.
* **Capa de Compatibilidad / Traducción de Parámetros**:
  - Incorpora una lógica de auto-adaptación bidireccional para resolver diferencias de nomenclatura entre el frontend Angular (que suele usar notaciones como `usuario_id`) y la base de datos o firmas en PHP (que emplean `id_usuario`).
  ```php
  // Convierte 'id_usuario' a 'usuario_id' si Angular envió la variante alternativa
  $variante1 = str_replace('id_', '', $nombre) . '_id';
  if (isset($datos[$variante1])) {
      $valor = $datos[$variante1];
  }
  ```

---

## 4. Capa de Frontend (Angular)

La interfaz gráfica del módulo de gestión de usuarios está construida de manera moderna, utilizando componentes de grano fino y formularios reactivos.

### A. Definición de Interfaz (`models/usuario.model.ts`)
Establece el tipo estricto TypeScript para garantizar el correcto uso de los datos del operario en toda la aplicación cliente:
```typescript
export interface Usuario {
  id: number;
  nombre: string;
  email?: string;
  correo?: string; // Mapeado con la base de datos
  password?: string;
  rol: string;
  num_categorias?: number;
  tickets_asignados?: number;
  categorias_nombres?: string[];
}
```

### B. Servicio (`services/usuario.service.ts`)
Encapsula la comunicación HTTP. Expone llamadas limpias mapeadas a los métodos y acciones del enrutador PHP:
* `getOperarios()` $\rightarrow$ Envía una petición `GET` a `?entidad=usuario&accion=listar_operarios`.
* `crearUsuario(datos)` $\rightarrow$ Envía un `POST` a `?entidad=usuario&accion=guardar`.
* `actualizarUsuario(id, datos)` $\rightarrow$ Envía un `PUT` inyectando el `id` en la payload a `?entidad=usuario&accion=guardar`.
* `eliminarUsuario(id)` $\rightarrow$ Envía un `DELETE` con el parámetro query `&id=${id}` a `?entidad=usuario&accion=borrar`.

### C. Componentes de Interfaz

1. **`OperariosComponent` (`pages/admin/operarios/`)**:
   * Componente principal de visualización. Muestra el listado de operarios registrados con sus respectivas estadísticas (número de categorías y tickets activos).
   * Incorpora micro-animaciones interactivas (despliegue mediante acordeón con `toggleCategorias()` para examinar visualmente el desglose de categorías en las que se especializa el operario).
   * Administra la apertura y cierre de las modales y procesa sus eventos de salida (`guardar`, `cerrar`, `confirmar`).
   * Posee un sistema integrado de mensajes temporales (`mensajeFeedback`) con estilos diferenciados para alertas de éxito o error que se disuelven de forma automática tras 4 segundos.

2. **`FormularioOperarioComponent` (`pages/modales/formulario-operario/`)**:
   * Componente modal secundario que reutiliza una misma interfaz para la creación y edición de operarios.
   * Emplea **Formularios Reactivos (`ReactiveFormsModule`)** con validaciones estrictas (`Validators.required`, `Validators.email`, longitud mínima).
   * Al inicializarse en modo edición (`ngOnInit`), se conecta con `categorias.service.ts` para obtener todas las categorías de la base de datos, y preselecciona automáticamente aquellas a las que el usuario ya pertenece mapeándolas por nombre.
   * Ofrece un selector interactivo amigable para marcar y desmarcar especialidades mediante `toggleCategoria(idCategoria)`.

3. **`ConfirmacionEliminarComponent` (`pages/modales/confirmacion-eliminar/`)**:
   * Modal de confirmación genérica para evitar eliminaciones accidentales de operarios. Llama al evento padre solo ante la aprobación consciente del administrador.

---

## 5. Tabla de Endpoints de la API (Gestión de Operarios)

| Método HTTP | Parámetro `entidad` | Parámetro `accion` | Parámetros Adicionales | Payload / Body | Descripción |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **GET** | `usuario` | `listar_operarios` | Ninguno | Vacío | Retorna el listado completo de operarios y sus estadísticas de asignación en formato JSON. |
| **GET** | `usuario` | `get_estadisticas` | Ninguno | Vacío | Obtiene métricas generales del sistema. |
| **POST** | `usuario` | `guardar` | Ninguno | `{ nombre, correo, rol, categorias: [id1, id2...] }` | Crea un nuevo operario y le asigna sus especialidades iniciales. |
| **PUT** | `usuario` | `guardar` | Ninguno | `{ id, nombre, correo, rol, categorias: [id1, id2...] }` | Actualiza un operario existente y refresca su matriz de categorías. |
| **DELETE** | `usuario` | `borrar` | `&id={ID}` | Vacío | Elimina el operario del sistema si no posee incidencias en proceso abiertas. |

---

## 6. Buenas Prácticas y Pautas de Mantenimiento

* **Identificación**: Al crear nuevos archivos de código o lógica del módulo de usuarios, asegúrese de agregar el encabezado estipulado identificando al autor **Joseph Joel Quispe Alvarez**, la asignatura, curso y descripción funcional.
* **Integridad del Negocio**: Cualquier cambio en la lógica de eliminación del usuario debe pasar siempre por la verificación previa de tickets activos. Nunca elimine el operario directamente de la base de datos sin comprobar si tiene tickets asignados en curso, ya que causará fallas lógicas o inconsistencias en la trazabilidad del soporte técnico.
* **Separación de Capas**: No introduzca sentencias SQL directamente en los controladores ni imprima bloques de código HTML dentro del modelo. Mantenga la pureza arquitectónica y respete el flujo del patrón MVC unificado en el router `index.php`.
* **Codificación**: Mantenga los archivos codificados estrictamente bajo UTF-8 sin caracteres con tilde, diéresis o `ñ` en los nombres de variables y métodos de programación para garantizar la plena portabilidad en servidores Unix/Windows.
