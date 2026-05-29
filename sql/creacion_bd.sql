/*
 * Alumnos: Sergio Díaz Pereira - Joseph Joel Quispe Alvarez
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * Descripción: Script de creación de la base de datos para TicketingEVG.
 */

-- DROP SCHEMA IF EXISTS TicketingEVG;
-- CREATE SCHEMA TicketingEVG;
-- USE TicketingEVG;


--
-- Estructura de tabla para la tabla `Rol`
--

CREATE TABLE Rol (
	id TINYINT UNSIGNED AUTO_INCREMENT,
	nombre VARCHAR(50) NOT NULL,
	CONSTRAINT PK_Rol PRIMARY KEY (id),
	CONSTRAINT UQ_nombre UNIQUE (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `Categoria`
--

CREATE TABLE Categoria (
	id TINYINT UNSIGNED AUTO_INCREMENT,
	nombre VARCHAR(50) NOT NULL,
	descripcion VARCHAR(100) DEFAULT NULL,
	CONSTRAINT PK_Categoria PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `Usuario`
--

CREATE TABLE Usuario (
	id SMALLINT UNSIGNED,
	visitas_totales SMALLINT UNSIGNED NOT NULL DEFAULT 0,
	id_rol TINYINT UNSIGNED DEFAULT NULL,
	CONSTRAINT PK_Usuario PRIMARY KEY (id),
	CONSTRAINT FK_Usuario_Rol FOREIGN KEY (id_rol) REFERENCES Rol (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `Categoria_Usuario`
--

CREATE TABLE Categoria_Usuario (
	id SMALLINT UNSIGNED AUTO_INCREMENT,
	id_categoria TINYINT UNSIGNED NOT NULL,
	id_usuario SMALLINT UNSIGNED NOT NULL,
	CONSTRAINT PK_Categoria_Usuario PRIMARY KEY (id),
	CONSTRAINT UQ_Categoria_usuario UNIQUE (id_categoria, id_usuario),
	CONSTRAINT FK_Categoria_Usuario_Categoria FOREIGN KEY (id_categoria) REFERENCES Categoria (id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT FK_Categoria_Usuario_Usuario FOREIGN KEY (id_usuario) REFERENCES Usuario (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `Ticket`
--

CREATE TABLE Ticket (
	id CHAR(20) NOT NULL,
	id_categoria TINYINT UNSIGNED NOT NULL,
	titulo VARCHAR(50) NOT NULL,
	descripcion VARCHAR(500) NOT NULL,
	prioridad CHAR(1) NOT NULL,
	id_usuario_creador SMALLINT UNSIGNED NOT NULL,
	estado ENUM('pendiente', 'asignado', 'proceso', 'resuelto', 'no aplica') NOT NULL DEFAULT 'pendiente',
	id_usuario_encargado SMALLINT UNSIGNED DEFAULT NULL,
	fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	fecha_prevista DATETIME DEFAULT NULL,
	ubicacion VARCHAR(100) DEFAULT NULL,
	CONSTRAINT PK_Ticket PRIMARY KEY (id),
	CONSTRAINT CHECK_prioridad CHECK (prioridad IN ('a', 'm', 'b')),
	CONSTRAINT FK_Ticket_Categoria FOREIGN KEY (id_categoria) REFERENCES Categoria (id) ON DELETE RESTRICT ON UPDATE CASCADE,
	CONSTRAINT FK_Ticket_Usuario_Creador FOREIGN KEY (id_usuario_creador) REFERENCES Usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
	CONSTRAINT FK_Ticket_Usuario_Encargado FOREIGN KEY (id_usuario_encargado) REFERENCES Usuario (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;