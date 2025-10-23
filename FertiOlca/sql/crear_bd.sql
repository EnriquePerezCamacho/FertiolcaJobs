-- ===================================================
-- BASE DE DATOS: fertiolca_db
-- ===================================================

CREATE DATABASE IF NOT EXISTS fertiolca_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE fertiolca_db;

-- ---------------------------------------------------
-- TABLA: usuarios
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  correo VARCHAR(150) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  rol ENUM('admin', 'jefe') NOT NULL DEFAULT 'jefe',
  activo TINYINT(1) NOT NULL DEFAULT 1,
  ultima_conexion DATETIME NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- TABLA: cuadrillas
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS cuadrillas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  id_jefe INT NOT NULL,
  ubicacion VARCHAR(150),
  fecha_actualizacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (id_jefe) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- TABLA: trabajadores
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS trabajadores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  id_cuadrilla INT NOT NULL,
  FOREIGN KEY (id_cuadrilla) REFERENCES cuadrillas(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- TABLA: categorias_maquinas
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS categorias_maquinas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL
) ENGINE=InnoDB;

-- ---------------------------------------------------
-- TABLA: maquinarias
-- ---------------------------------------------------
CREATE TABLE IF NOT EXISTS maquinarias (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  id_categoria INT NOT NULL,
  estado ENUM('disponible', 'ocupada') NOT NULL DEFAULT 'disponible',
  id_jefe_ocupando INT NULL,
  FOREIGN KEY (id_categoria) REFERENCES categorias_maquinas(id) ON DELETE CASCADE,
  FOREIGN KEY (id_jefe_ocupando) REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ===================================================
-- DATOS DE EJEMPLO
-- ===================================================

-- Usuario administrador (correo: admin@fertiolca.com / pass: Admin1234)
INSERT INTO usuarios (nombre, correo, contrasena, rol)
VALUES ('Administrador', 'admin@fertiolca.com', '$2y$10$O7B6V5C0UIlQZTGcJcmrP.SJZx2S8STKDnchuxut.TULMy6xX/5QW', 'admin');

-- Usuario jefe (correo: jefe@fertiolca.com / pass: Jefe1234)
INSERT INTO usuarios (nombre, correo, contrasena, rol)
VALUES ('Carlos Ruiz', 'jefe@fertiolca.com', '$2y$10$tpBjEJAnNR7pE36JjFy3ZeWcG6HnSWvQkkgK/fuMnlRLu4H/lv8bG', 'jefe');

-- Categorías de maquinaria
INSERT INTO categorias_maquinas (nombre) VALUES
('Motosierras'),
('Desbrozadoras'),
('Tractores');

-- Maquinarias
INSERT INTO maquinarias (nombre, id_categoria) VALUES
('Motosierra 1', 1),
('Motosierra 2', 1),
('Desbrozadora 1', 2),
('Tractor 1', 3);

-- Cuadrilla ejemplo
INSERT INTO cuadrillas (nombre, id_jefe, ubicacion) VALUES
('Cuadrilla Norte', 2, 'Campo 3');

-- Trabajadores
INSERT INTO trabajadores (nombre, id_cuadrilla) VALUES
('Juan Pérez', 1),
('Luis Gómez', 1),
('Andrés Díaz', 1);
