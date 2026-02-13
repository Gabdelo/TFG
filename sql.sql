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
ALTER TABLE usuarios
ADD COLUMN foto_perfil VARCHAR(255) DEFAULT NULL;
CREATE TABLE usuario_seguidores (
    id_usuario INT NOT NULL,         -- El usuario que está siendo seguido
    id_seguidor INT NOT NULL,        -- El usuario que sigue
    fecha_seguimiento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_usuario, id_seguidor),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_seguidor) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);
---------------------------------------------------------
-- INSERTS ----------------------------------------------------------------------------------------
---------------------------------------------------------
INSERT INTO usuarios (nombre, email, password, ciudad, modalidad, tipo_usuario, disponibilidad, tipo_intercambio) VALUES
('Ana López', 'ana.lopez@email.com', 'pass123', 'Madrid', 'Online', 'buscar', 'Lunes a Viernes 9-17', 'Clases / mentoría'),
('Carlos Pérez', 'carlos.perez@email.com', 'pass123', 'Barcelona', 'Presencial', 'ofrecer', 'Fines de semana', 'Proyecto'),
('Lucía Gómez', 'lucia.gomez@email.com', 'pass123', 'Valencia', 'Ambas', 'ambos', 'Martes y Jueves', 'Puntual'),
('David Martínez', 'david.martinez@email.com', 'pass123', 'Sevilla', 'Online', 'buscar', 'Flexible', 'Clases / mentoría'),
('Marta Fernández', 'marta.fernandez@email.com', 'pass123', 'Bilbao', 'Presencial', 'ofrecer', 'Lunes y Miércoles', 'Proyecto'),
('Javier Ruiz', 'javier.ruiz@email.com', 'pass123', 'Zaragoza', 'Ambas', 'ambos', 'Fines de semana', 'Puntual'),
('Sofía Sánchez', 'sofia.sanchez@email.com', 'pass123', 'Málaga', 'Online', 'buscar', 'Flexible', 'Clases / mentoría'),
('Miguel Torres', 'miguel.torres@email.com', 'pass123', 'Granada', 'Presencial', 'ofrecer', 'Martes y Jueves', 'Proyecto'),
('Laura Ramírez', 'laura.ramirez@email.com', 'pass123', 'Salamanca', 'Ambas', 'ambos', 'Lunes a Viernes', 'Puntual'),
('Diego Moreno', 'diego.moreno@email.com', 'pass123', 'Alicante', 'Online', 'buscar', 'Fines de semana', 'Clases / mentoría'),
('Elena Jiménez', 'elena.jimenez@email.com', 'pass123', 'Valladolid', 'Presencial', 'ofrecer', 'Flexible', 'Proyecto'),
('Andrés Castillo', 'andres.castillo@email.com', 'pass123', 'Santander', 'Ambas', 'ambos', 'Martes y Jueves', 'Puntual'),
('Isabel Ortiz', 'isabel.ortiz@email.com', 'pass123', 'Toledo', 'Online', 'buscar', 'Lunes a Viernes', 'Clases / mentoría'),
('Fernando Herrera', 'fernando.herrera@email.com', 'pass123', 'Córdoba', 'Presencial', 'ofrecer', 'Flexible', 'Proyecto'),
('Patricia Molina', 'patricia.molina@email.com', 'pass123', 'Murcia', 'Ambas', 'ambos', 'Fines de semana', 'Puntual'),
('Raúl Navarro', 'raul.navarro@email.com', 'pass123', 'Pamplona', 'Online', 'buscar', 'Martes y Jueves', 'Clases / mentoría'),
('Verónica Rubio', 'veronica.rubio@email.com', 'pass123', 'Vigo', 'Presencial', 'ofrecer', 'Lunes a Viernes', 'Proyecto'),
('Sergio Méndez', 'sergio.mendez@email.com', 'pass123', 'Girona', 'Ambas', 'ambos', 'Flexible', 'Puntual'),
('Natalia Campos', 'natalia.campos@email.com', 'pass123', 'Oviedo', 'Online', 'buscar', 'Fines de semana', 'Clases / mentoría'),
('Antonio Vega', 'antonio.vega@email.com', 'pass123', 'Huelva', 'Presencial', 'ofrecer', 'Martes y Jueves', 'Proyecto');

-- Usuarios que ofrecen habilidades
INSERT INTO usuario_ofrece (id_usuario, id_habilidad, id_nivel) VALUES
(2, 1, 3),  -- Carlos Pérez ofrece Programación Avanzado
(2, 6, 2),  -- Carlos Pérez ofrece Marketing Intermedio
(5, 2, 2),  -- Marta Fernández ofrece Diseño gráfico Intermedio
(5, 4, 1),  -- Marta Fernández ofrece Pintura Básico
(6, 3, 2),  -- Javier Ruiz ofrece Mecánica Intermedio
(6, 5, 3),  -- Javier Ruiz ofrece Reparación Avanzado
(8, 1, 2),  -- Miguel Torres ofrece Programación Intermedio
(8, 6, 1),  -- Miguel Torres ofrece Marketing Básico
(11, 2, 3), -- Elena Jiménez ofrece Diseño gráfico Avanzado
(11, 5, 2), -- Elena Jiménez ofrece Reparación Intermedio
(14, 1, 3), -- Fernando Herrera ofrece Programación Avanzado
(14, 3, 2), -- Fernando Herrera ofrece Mecánica Intermedio
(17, 4, 3), -- Verónica Rubio ofrece Pintura Avanzado
(17, 6, 2), -- Verónica Rubio ofrece Marketing Intermedio
(20, 3, 1), -- Antonio Vega ofrece Mecánica Básico
(20, 5, 2); -- Antonio Vega ofrece Reparación Intermedio


-- Usuarios que buscan habilidades
INSERT INTO usuario_busca (id_usuario, id_habilidad) VALUES
(1, 1),  -- Ana López busca Programación
(1, 2),  -- Ana López busca Diseño gráfico
(3, 5),  -- Lucía Gómez busca Reparación
(3, 3),  -- Lucía Gómez busca Mecánica
(4, 1),  -- David Martínez busca Programación
(4, 6),  -- David Martínez busca Marketing
(7, 2),  -- Sofía Sánchez busca Diseño gráfico
(7, 4),  -- Sofía Sánchez busca Pintura
(9, 5),  -- Laura Ramírez busca Reparación
(9, 1),  -- Laura Ramírez busca Programación
(10, 3), -- Diego Moreno busca Mecánica
(10, 6), -- Diego Moreno busca Marketing
(12, 4), -- Andrés Castillo busca Pintura
(12, 2), -- Andrés Castillo busca Diseño gráfico
(13, 1), -- Isabel Ortiz busca Programación
(13, 5), -- Isabel Ortiz busca Reparación
(15, 3), -- Patricia Molina busca Mecánica
(15, 6), -- Patricia Molina busca Marketing
(16, 2), -- Raúl Navarro busca Diseño gráfico
(16, 4); -- Raúl Navarro busca Pintura

INSERT INTO proyectos (id_usuario, titulo, descripcion, tipo_proyecto, rol, modalidad, fecha_inicio, fecha_fin, enlace) VALUES
(2, 'Desarrollo Web Ecommerce', 'Proyecto de creación de tienda online completa.', 'Profesional', 'Desarrollador', 'Online', '2026-01-05', '2026-03-20', 'http://github.com/cperez/ecommerce'),
(5, 'Cartel Publicitario', 'Diseño de campaña gráfica para empresa local.', 'Profesional', 'Diseñadora', 'Presencial', '2026-02-01', '2026-02-28', NULL),
(6, 'Reparación de Motores', 'Mantenimiento y reparación de motores mecánicos.', 'Personal', 'Técnico Mecánico', 'Mixto', '2026-01-10', '2026-02-15', NULL),
(8, 'Aplicación de Marketing', 'App para gestionar campañas de marketing digital.', 'Colaborativo', 'Desarrollador', 'Online', '2026-03-01', '2026-05-01', 'http://github.com/mtorres/marketingapp'),
(11, 'Rediseño de Marca', 'Rediseño integral de identidad visual de empresa.', 'Profesional', 'Diseñadora', 'Presencial', '2026-01-15', '2026-02-28', NULL),
(14, 'Software de Control', 'Aplicación para controlar procesos mecánicos.', 'Académico', 'Desarrollador', 'Online', '2026-02-05', '2026-04-05', 'http://github.com/fherrera/controlapp'),
(17, 'Taller de Pintura', 'Clases y proyecto de mural colectivo.', 'Personal', 'Instructor', 'Mixto', '2026-03-10', '2026-03-25', NULL),
(20, 'Restauración de Vehículos', 'Proyecto de restauración y mantenimiento de autos clásicos.', 'Personal', 'Mecánico', 'Presencial', '2026-01-20', '2026-03-15', NULL);

-- Vinculando habilidades de los usuarios a los proyectos
INSERT INTO proyecto_habilidad (id_proyecto, id_habilidad) VALUES
(1, 1),  -- Desarrollo Web Ecommerce → Programación
(1, 6),  -- Marketing
(2, 2),  -- Cartel Publicitario → Diseño gráfico
(2, 4),  -- Pintura
(3, 3),  -- Reparación de Motores → Mecánica
(3, 5),  -- Reparación
(4, 1),  -- Aplicación de Marketing → Programación
(4, 6),  -- Marketing
(5, 2),  -- Rediseño de Marca → Diseño gráfico
(5, 5),  -- Reparación (tal vez de elementos gráficos físicos)
(6, 1),  -- Software de Control → Programación
(6, 3),  -- Mecánica
(7, 4),  -- Taller de Pintura → Pintura
(7, 6),  -- Marketing
(8, 3),  -- Restauración de Vehículos → Mecánica
(8, 5);  -- Reparación

INSERT INTO usuario_seguidores (id_usuario, id_seguidor) VALUES
(2, 1),  -- Usuario 1 sigue a Usuario 2
(3, 1),  -- Usuario 1 sigue a Usuario 3
(3, 2);  -- Usuario 2 sigue a Usuario 3

