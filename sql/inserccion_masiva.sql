/*
 * Alumnos: Sergio Díaz Pereira - Joseph Joel Quispe Alvarez
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * Descripción: Script de inserción masiva de datos para pruebas.
 */

-- USE TicketingEVG;


-- Inserción de datos en `Rol`
INSERT INTO Rol (nombre) VALUES
('Administrador'), 
('Responsable'),
('Trabajador');

-- Inserción de datos en `Categoria`
INSERT INTO Categoria (nombre, descripcion) VALUES 
('Software', 'Incidencias y peticiones relacionadas con programas y SO'),
('Redes', 'Problemas de conectividad y equipos de red'),
('Mantenimiento', 'Mantenimiento preventivo y correctivo de hardware'),
('Otros', 'Cualquier otro tipo de solicitud no clasificada');