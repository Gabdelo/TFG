.-- =========================
-- CREAR BASE DE DATOS
-- =========================
CREATE DATABASE IF NOT EXISTS nexum_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE nexum_db;

-- =========================
-- TABLA USUARIOS
-- =========================
CREATE TABLE usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    ciudad VARCHAR(50),
    modalidad ENUM('Presencial','Online','Ambas'),
    tipo_usuario ENUM('ofrecer','buscar','ambos') NOT NULL,
    disponibilidad VARCHAR(100),
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- =========================
-- TABLA HABILIDADES
-- =========================
CREATE TABLE habilidades (
    id_habilidad INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

-- =========================
-- TABLA NIVELES
-- =========================
CREATE TABLE niveles (
    id_nivel INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(20) NOT NULL UNIQUE
);

-- =========================
-- USUARIO OFRECE HABILIDADES
-- =========================
CREATE TABLE usuario_ofrece (
    id_usuario INT NOT NULL,
    id_habilidad INT NOT NULL,
    id_nivel INT NOT NULL,
    PRIMARY KEY (id_usuario, id_habilidad),
    FOREIGN KEY (id_usuario) 
        REFERENCES usuarios(id_usuario) 
        ON DELETE CASCADE,
    FOREIGN KEY (id_habilidad) 
        REFERENCES habilidades(id_habilidad),
    FOREIGN KEY (id_nivel) 
        REFERENCES niveles(id_nivel)
);

-- =========================
-- USUARIO BUSCA HABILIDADES
-- =========================
CREATE TABLE usuario_busca (
    id_usuario INT NOT NULL,
    id_habilidad INT NOT NULL,
    PRIMARY KEY (id_usuario, id_habilidad),
    FOREIGN KEY (id_usuario) 
        REFERENCES usuarios(id_usuario) 
        ON DELETE CASCADE,
    FOREIGN KEY (id_habilidad) 
        REFERENCES habilidades(id_habilidad)
);

-- =========================
-- DATOS INICIALES
-- =========================
INSERT INTO habilidades (nombre) VALUES
('Programación'),
('Diseño gráfico'),
('Mecánica'),
('Pintura'),
('Reparación'),
('Marketing');

INSERT INTO niveles (nombre) VALUES
('Básico'),
('Intermedio'),
('Avanzado');

ALTER TABLE usuarios
ADD tipo_intercambio ENUM('Puntual','Proyecto','Clases / mentoría');

CREATE TABLE proyectos (
    id_proyecto INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descripcion TEXT NOT NULL,
    tipo_proyecto ENUM('Personal','Académico','Profesional','Colaborativo'),
    rol VARCHAR(50),
    modalidad ENUM('Online','Presencial','Mixto'),
    fecha_inicio DATE,
    fecha_fin DATE,
    enlace VARCHAR(255),
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);
CREATE TABLE proyecto_habilidad (
    id_proyecto INT,
    id_habilidad INT,
    PRIMARY KEY (id_proyecto, id_habilidad),
    FOREIGN KEY (id_proyecto) REFERENCES proyectos(id_proyecto) ON DELETE CASCADE,
    FOREIGN KEY (id_habilidad) REFERENCES habilidades(id_habilidad)
);