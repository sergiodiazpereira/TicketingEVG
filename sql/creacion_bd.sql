/*
 * Alumno: Sergio Díaz Pereira
 * Asignatura: Desarrollo de Aplicaciones Web
 * Curso: 2025-2026
 * Descripción: Script de creación de la base de datos para TicketingEVG.
 */

DROP SCHEMA IF EXISTS TicketingEVG;
CREATE SCHEMA TicketingEVG;
USE TicketingEVG;

--
-- Estructura de tabla para la tabla `Rol`
--

CREATE TABLE Rol (
	id TINYINT UNSIGNED AUTO_INCREMENT,
	nombre VARCHAR(50) NOT NULL,
	CONSTRAINT PK_Rol PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `Categoria`
--

CREATE TABLE Categoria (
	id TINYINT UNSIGNED AUTO_INCREMENT,
	nombre VARCHAR(100) NOT NULL,
	CONSTRAINT PK_Categoria PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `Usuario`
--

CREATE TABLE Usuario (
	id SMALLINT UNSIGNED AUTO_INCREMENT,
	nombre VARCHAR(150) NOT NULL,
	correo VARCHAR(150) NOT NULL,
	activo BIT NOT NULL DEFAULT 1,
	visitas_totales SMALLINT UNSIGNED NOT NULL DEFAULT 0,
	id_Rol TINYINT UNSIGNED NOT NULL,
	CONSTRAINT PK_Usuario PRIMARY KEY (id),
	CONSTRAINT UQ_correo UNIQUE (correo),
	CONSTRAINT FK_Usuario_Rol FOREIGN KEY (id_Rol) REFERENCES Rol (id) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `Categoria_Usuario`
--

CREATE TABLE Categoria_Usuario (
	id SMALLINT UNSIGNED AUTO_INCREMENT,
	id_Categoria TINYINT UNSIGNED NOT NULL,
	id_Usuario SMALLINT UNSIGNED NOT NULL,
	CONSTRAINT PK_Categoria_Usuario PRIMARY KEY (id),
	CONSTRAINT UQ_Categoria_usuario UNIQUE (id_Categoria, id_Usuario),
	CONSTRAINT FK_Categoria_Usuario_Categoria FOREIGN KEY (id_Categoria) REFERENCES Categoria (id) ON DELETE CASCADE ON UPDATE CASCADE,
	CONSTRAINT FK_Categoria_Usuario_Usuario FOREIGN KEY (id_Usuario) REFERENCES Usuario (id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Estructura de tabla para la tabla `Ticket`
--

CREATE TABLE Ticket (
	id CHAR(20) NOT NULL,
	id_Categoria TINYINT UNSIGNED NOT NULL,
	titulo VARCHAR(50) NOT NULL,
	descripcion VARCHAR(500) NOT NULL,
	prioridad CHAR(1) NOT NULL,
	id_Usuario_Creador SMALLINT UNSIGNED NOT NULL,
	estado ENUM('pendiente', 'asignado', 'proceso', 'resuelto') NOT NULL DEFAULT 'pendiente',
	id_Usuario_Encargado SMALLINT UNSIGNED DEFAULT NULL,
	fecha_creacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
	fecha_prevista DATETIME DEFAULT NULL,
	CONSTRAINT PK_Ticket PRIMARY KEY (id),
	CONSTRAINT CHECK_prioridad CHECK (prioridad IN ('a', 'm', 'b')),
	CONSTRAINT FK_Ticket_Categoria FOREIGN KEY (id_Categoria) REFERENCES Categoria (id) ON DELETE RESTRICT ON UPDATE CASCADE,
	CONSTRAINT FK_Ticket_Usuario_Creador FOREIGN KEY (id_Usuario_Creador) REFERENCES Usuario (id) ON DELETE RESTRICT ON UPDATE CASCADE,
	CONSTRAINT FK_Ticket_Usuario_Encargado FOREIGN KEY (id_Usuario_Encargado) REFERENCES Usuario (id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;