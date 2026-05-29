/*
 * Alumnos: Sergio Díaz Pereira - Joseph Joel Quispe Alvarez
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * Descripción: Script de inserción masiva de datos para pruebas adaptado a SSO e ID Intranet.
 */

-- USE TicketingEVG;


-- Inserción de datos en `Rol`
INSERT INTO Rol (id, nombre) VALUES 
(1, 'Administrador'),
(2, 'Responsable'),
(3, 'Trabajador');

-- Inserción de datos en `Categoria`
INSERT INTO Categoria (nombre, descripcion) VALUES 
('Software', 'Incidencias y peticiones relacionadas con programas y SO'),
('Redes', 'Problemas de conectividad y equipos de red'),
('Mantenimiento', 'Mantenimiento preventivo y correctivo de hardware'),
('Otros', 'Cualquier otro tipo de solicitud no clasificada');

-- Inserción de datos en `Usuario` (id, visitas_totales, id_rol)
-- IDs alineados con el personal simulado de la Intranet:
-- id 1 = Sergio Díaz Pereira (Trabajador)
-- id 2 = Julio Alberto Domínguez (Administrador)
-- id 26 = Joseph Joel Quispe Alvarez (Solicitante normal, id_rol = NULL)
-- id 101 = María del Carmen Reyes (Responsable)
INSERT INTO Usuario (id, visitas_totales, id_rol) VALUES 
(1, 8, 3),   -- Sergio (Trabajador)
(2, 10, 1),  -- Julio (Administrador)
(26, 2, NULL), -- Joseph (Profesor / Solicitante normal)
(101, 5, 2);  -- María (Responsable)

-- Inserción de datos en `Categoria_Usuario` (Asignación de operarios a categorías)
INSERT INTO Categoria_Usuario (id_categoria, id_usuario) VALUES 
(1, 1), -- Sergio a Software
(2, 1), -- Sergio a Redes
(3, 101); -- María a Mantenimiento

-- Inserción de datos en `Ticket`
-- Creadores y encargados apuntando a los IDs consistentes de Usuario:
INSERT INTO Ticket (id, id_categoria, titulo, descripcion, prioridad, id_usuario_creador, estado, id_usuario_encargado, fecha_creacion, ubicacion) VALUES 
('PS2002230501', 2, 'Instalar Office', 'Se requiere licencia para el equipo de secretaría.', 'b', 26, 'proceso', 1, '2026-02-22 09:15:00', 'secretaria'),
('PS2002230502', 2, 'Actualización Windows', 'Varios equipos del aula 2 están pidiendo reinicio.', 'm', 26, 'asignado', 1, '2026-02-22 10:00:00', 'aula102'),
('I2002230101', 1, 'Monitor parpadea', 'El monitor del puesto 5 no deja de parpadear.', 'a', 26, 'proceso', 1, '2026-02-23 08:30:00', 'aula201'),
('PS2002230503', 3, 'no hay conexión WiFi', 'La zona del gimnasio no tiene señal desde ayer.', 'a', 101, 'pendiente', 1, '2026-02-23 09:45:00', 'otros'),
('PS2002230504', 4, 'Limpieza de filtros', 'Se solicita mantenimiento preventivo de los proyectores.', 'b', 26, 'resuelto', 101, '2026-02-24 12:00:00', 'salon_actos'),
('I2002230102', 1, 'Teclado roto', 'Faltan teclas en el teclado del aula de música.', 'b', 26, 'pendiente', 1, '2026-02-24 13:20:00', 'aula202'),
('I2002230103', 2, 'Fallo Java', 'No funciona el entorno de desarrollo en el aula 1.', 'm', 26, 'asignado', 1, '2026-02-25 10:15:00', 'aula101');