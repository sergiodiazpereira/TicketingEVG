/*
 * Alumno: Sergio Díaz Pereira
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * Descripción: Inserción de datos para la base de datos TicketingEVG.
 */

USE TicketingEVG;

-- Inserción de datos en `Rol`
INSERT INTO Rol (nombre) VALUES 
('Administrador'),
('Responsable'),
('Trabajador'),
('Profesor');

-- Inserción de datos en `Categoria`
INSERT INTO Categoria (nombre) VALUES 
('Hardware'),
('Software'),
('Redes'),
('Mantenimiento');

-- Inserción de datos en `Usuario`
INSERT INTO Usuario (nombre, correo, activo, visitas_totales, id_Rol) VALUES 
('Julio', 'julioadmin@fundacionloyola.es', 1, 10, 1),
('UsuarioR', 'uresponsable@fundacionloyola.es', 1, 5, 2),
('UsuarioT', 'uprofesor@fundacionloyola.es', 1, 2, 3),
('Alberto Domínguez', 'albertodominguez@fundacionloyola.es', 1, 8, 4);

-- Inserción de datos en `Categoria_Usuario`
INSERT INTO Categoria_Usuario (id_Categoria, id_Usuario) VALUES 
(1, 1), 
(1, 3), 
(2, 1), 
(2, 4),
(3, 1);

-- Inserción de datos en `Ticket`
INSERT INTO Ticket (id, id_Categoria, titulo, descripcion, prioridad, id_Usuario_Creador, id_Usuario_Encargado, fecha_creacion) VALUES 
('PS2002230501', 2, 'Instalar Office', 'Se requiere licencia para el equipo de secretaría.', 'b', 1, 4, '2026-02-22 09:15:00'),
('PS2002230502', 2, 'Actualización Windows', 'Varios equipos del aula 2 están pidiendo reinicio.', 'm', 1, 4, '2026-02-22 10:00:00'),
('I2002230101', 1, 'Monitor parpadea', 'El monitor del puesto 5 no deja de parpadear.', 'a', 4, 1, '2026-02-23 08:30:00'),
('PS2002230503', 3, 'no hay conexión WiFi', 'La zona del gimnasio no tiene señal desde ayer.', 'a', 2, 1, '2026-02-23 09:45:00'),
('PS2002230504', 4, 'Limpieza de filtros', 'Se solicita mantenimiento preventivo de los proyectores.', 'b', 3, 2, '2026-02-24 12:00:00'),
('I2002230102', 1, 'Teclado roto', 'Faltan teclas en el teclado del aula de música.', 'b', 4, 3, '2026-02-24 13:20:00'),
('I2002230103', 2, 'Fallo Java', 'No funciona el entorno de desarrollo en el aula 1.', 'm', 3, 1, '2026-02-25 10:15:00');