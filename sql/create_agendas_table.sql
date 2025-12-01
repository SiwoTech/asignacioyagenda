-- Migración SQL para crear la tabla de agendas
-- Esta tabla almacena las agendas vinculadas a operadores

CREATE TABLE IF NOT EXISTS agendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    operator_id INT NOT NULL,
    operator_name VARCHAR(255) NOT NULL,
    date DATE NOT NULL,
    start_time TIME NOT NULL,
    end_time TIME NOT NULL,
    subject VARCHAR(255) NOT NULL,
    notes TEXT,
    location VARCHAR(255),
    centro VARCHAR(50) DEFAULT 'Cancun',
    color VARCHAR(20) DEFAULT '#E55B26',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_operator_id (operator_id),
    INDEX idx_date (date),
    INDEX idx_centro (centro)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Nota: El campo operator_id hace referencia a la columna 'id' de la tabla 'operadores'
-- Para agregar una restricción de llave foránea, descomente la siguiente línea:
-- ALTER TABLE agendas ADD CONSTRAINT fk_agendas_operador FOREIGN KEY (operator_id) REFERENCES operadores(id) ON DELETE CASCADE;
