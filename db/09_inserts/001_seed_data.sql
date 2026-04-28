INSERT INTO entrenadores (nombre, especialidad, email, telefono, activo) VALUES
('Laura Martinez', 'Funcional y HIIT', 'laura@gimnasio.com', '3001112233', TRUE),
('Carlos Rojas', 'Musculacion', 'carlos@gimnasio.com', '3002223344', TRUE),
('Andrea Gomez', 'Yoga y movilidad', 'andrea@gimnasio.com', '3003334455', TRUE)
ON CONFLICT (email) DO NOTHING;

INSERT INTO planes (nombre, descripcion, precio_mensual, duracion_dias, activo) VALUES
('Basico', 'Acceso a zona de maquinas en horario diurno', 60000, 30, TRUE),
('Premium', 'Acceso total, clases grupales y evaluacion mensual', 95000, 30, TRUE),
('Estudiante', 'Plan con descuento para estudiantes', 45000, 30, TRUE)
ON CONFLICT (nombre) DO NOTHING;

INSERT INTO miembros (plan_id, nombre, email, telefono, fecha_inscripcion, estado) VALUES
(1, 'Juan Perez', 'juan.perez@mail.com', '3101112233', CURRENT_DATE, 'ACTIVO'),
(2, 'Maria Lopez', 'maria.lopez@mail.com', '3102223344', CURRENT_DATE, 'ACTIVO'),
(3, 'Sofia Torres', 'sofia.torres@mail.com', '3103334455', CURRENT_DATE, 'ACTIVO'),
(2, 'Diego Ramirez', 'diego.ramirez@mail.com', '3104445566', CURRENT_DATE, 'SUSPENDIDO')
ON CONFLICT (email) DO NOTHING;

INSERT INTO clases (entrenador_id, nombre, descripcion, cupo_maximo, horario, activo) VALUES
(1, 'HIIT Express', 'Clase intensa de cardio y fuerza de 45 minutos', 20, CURRENT_TIMESTAMP + INTERVAL '1 day', TRUE),
(2, 'Fuerza Total', 'Entrenamiento guiado de musculacion', 15, CURRENT_TIMESTAMP + INTERVAL '2 days', TRUE),
(3, 'Yoga Flow', 'Movilidad, respiracion y flexibilidad', 18, CURRENT_TIMESTAMP + INTERVAL '3 days', TRUE),
(1, 'Core Training', 'Trabajo de abdomen, estabilidad y postura', 16, CURRENT_TIMESTAMP + INTERVAL '4 days', TRUE);

INSERT INTO reservas (miembro_id, clase_id, estado) VALUES
(1, 1, 'RESERVADA'),
(2, 2, 'RESERVADA'),
(3, 3, 'ASISTIO'),
(4, 4, 'CANCELADA')
ON CONFLICT (miembro_id, clase_id) DO NOTHING;
