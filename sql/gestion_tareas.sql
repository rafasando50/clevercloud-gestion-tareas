-- =========================================
--  Archivo: gestion_tareas.sql
--  Proyecto: Gestión de Tareas (PHP)
-- =========================================

-- 1) Crear base de datos (si no existe)
CREATE DATABASE IF NOT EXISTS gestion_tareas
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_general_ci;

USE gestion_tareas;

-- 2) Crear tabla de tareas
DROP TABLE IF EXISTS tareas;

CREATE TABLE tareas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(255) NOT NULL,
  descripcion TEXT NULL,
  fecha_inicio DATE NULL,
  fecha_fin DATE NULL,
  estado ENUM('pendiente', 'en_curso', 'terminada') NOT NULL DEFAULT 'pendiente',
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  actualizado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Insertar datos de ejemplo para que coincidan con tu dashboard

INSERT INTO tareas (id, titulo, descripcion, fecha_inicio, fecha_fin, estado)
VALUES
  -- Pendientes (los que ves en la primera columna del dashboard)
  (1, 'Realizar módulo "Nueva tarea"',
      'Formulario para crear nuevas tareas en la aplicación.',
      '2025-10-28', '2025-11-02', 'pendiente'),

  (2, 'Realizar módulo "Página principal"',
      'Diseño del panel principal y organización de secciones.',
      '2025-10-20', '2025-10-30', 'pendiente'),

  (3, 'Conectar la base de datos',
      'Configuración de conexión MySQL y pruebas básicas.',
      '2025-10-25', '2025-11-05', 'pendiente'),

  -- En curso (segunda columna del dashboard)
  (4, 'Levantamiento de requisitos',
      'Reunir requisitos del usuario y documentar funcionalidades.',
      '2025-10-18', '2025-10-25', 'en_curso'),

  (5, 'Realizar prototipado',
      'Maquetar pantallas principales antes de implementar.',
      '2025-10-26', '2025-11-03', 'en_curso'),

  -- Terminadas (tercera columna del dashboard)
  (6, 'Onboarding',
      'Explicar al usuario cómo usar la aplicación de gestión de tareas.',
      '2025-10-10', '2025-10-15', 'terminada'),

  (7, 'Desafíos iniciales',
      'Registrar y resolver problemas encontrados en la primera versión.',
      '2025-10-15', '2025-10-20', 'terminada');

-- Opcional: ajustar el AUTO_INCREMENT para que las nuevas tareas sigan después de la 7
ALTER TABLE tareas AUTO_INCREMENT = 8;
