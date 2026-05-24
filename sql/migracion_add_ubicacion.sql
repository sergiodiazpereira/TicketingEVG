-- ====================================================================
-- PROYECTO: TicketingEVG
-- ALUMNOS: Sergio Díaz Pereira - Joseph Joel Quispe Alvarez
-- DESCRIPCIÓN: Script de migración para añadir soporte al campo 'ubicacion'
--              en la tabla 'Ticket'. Ejecutar en DBeaver.
-- ====================================================================

USE TicketingEVG;

-- Añadir columna 'ubicacion' a la tabla 'Ticket' si no existe
ALTER TABLE Ticket ADD COLUMN ubicacion VARCHAR(100) DEFAULT NULL;
