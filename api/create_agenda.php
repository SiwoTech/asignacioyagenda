<?php
/**
 * API Endpoint: create_agenda.php
 * 
 * Recibe datos de agenda vía POST (JSON o form-data) y crea una entrada en la tabla agendas.
 * También puede insertar en las tablas de calendario (cancun/playa) según el centro de trabajo.
 * 
 * Campos requeridos: operator_id, operator_name, date, start_time, subject
 * Campos opcionales: end_time, notes, location, centro, color
 */

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Solo permitir POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método no permitido. Use POST.']);
    exit;
}

// Obtener datos del request (soporta JSON y form-data)
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';

if (strpos($contentType, 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'JSON inválido.']);
        exit;
    }
} else {
    $input = $_POST;
}

// Validar campos requeridos
$requiredFields = ['operator_id', 'operator_name', 'date', 'start_time', 'subject'];
$missingFields = [];

foreach ($requiredFields as $field) {
    if (!isset($input[$field]) || trim($input[$field]) === '') {
        $missingFields[] = $field;
    }
}

if (!empty($missingFields)) {
    http_response_code(400);
    echo json_encode([
        'success' => false, 
        'error' => 'Campos requeridos faltantes: ' . implode(', ', $missingFields)
    ]);
    exit;
}

// Sanitizar y obtener datos
$operator_id = intval($input['operator_id']);
$operator_name = trim($input['operator_name']);
$date = trim($input['date']);
$start_time = trim($input['start_time']);
$end_time = isset($input['end_time']) && trim($input['end_time']) !== '' ? trim($input['end_time']) : $start_time;
$subject = trim($input['subject']);
$notes = isset($input['notes']) ? trim($input['notes']) : '';
$location = isset($input['location']) ? trim($input['location']) : '';
$centro = isset($input['centro']) && trim($input['centro']) !== '' ? trim($input['centro']) : 'Cancun';
$color = isset($input['color']) && trim($input['color']) !== '' ? trim($input['color']) : '#E55B26';

// Validar formato de fecha (YYYY-MM-DD)
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Formato de fecha inválido. Use YYYY-MM-DD.']);
    exit;
}

// Validar formato de hora (HH:MM o HH:MM:SS)
if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $start_time)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Formato de hora de inicio inválido. Use HH:MM o HH:MM:SS.']);
    exit;
}

if (!preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $end_time)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Formato de hora de fin inválido. Use HH:MM o HH:MM:SS.']);
    exit;
}

// Validar que operator_id sea positivo
if ($operator_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'ID de operador inválido.']);
    exit;
}

// Conexión a la base de datos
// Usamos las credenciales del archivo de conexión existente
$hostname = "localhost";
$username_db = "u826340212_orangedb";
$password_db = "Cwo9982061148";
$database = "u826340212_orangedb";

// Conexión principal (operadores)
$conexion = new mysqli($hostname, $username_db, $password_db, $database);

if ($conexion->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
    exit;
}

$conexion->set_charset('utf8mb4');

// Iniciar transacción
$conexion->begin_transaction();

try {
    // Verificar que el operador existe
    $stmt = $conexion->prepare("SELECT id, nombre, centro FROM operadores WHERE id = ? AND status = 0");
    $stmt->bind_param("i", $operator_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        throw new Exception('Operador no encontrado o inactivo.');
    }
    
    $operador = $result->fetch_assoc();
    $stmt->close();
    
    // Usar el centro del operador si no se especificó
    if ($centro === 'Cancun' && !empty($operador['centro'])) {
        $centro = $operador['centro'];
    }
    
    // Crear la tabla agendas si no existe
    $createTableSQL = "CREATE TABLE IF NOT EXISTS agendas (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $conexion->query($createTableSQL);
    
    // Insertar en la tabla agendas
    $stmt = $conexion->prepare(
        "INSERT INTO agendas (operator_id, operator_name, date, start_time, end_time, subject, notes, location, centro, color) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    $stmt->bind_param(
        "isssssssss",
        $operator_id,
        $operator_name,
        $date,
        $start_time,
        $end_time,
        $subject,
        $notes,
        $location,
        $centro,
        $color
    );
    
    if (!$stmt->execute()) {
        throw new Exception('Error al insertar la agenda: ' . $stmt->error);
    }
    
    $agenda_id = $conexion->insert_id;
    $stmt->close();
    
    // También insertar en la tabla de calendario correspondiente (cancun o playa)
    // Conexión a la base de datos de agenda
    $hostname_agenda = "localhost";
    $username_agenda = "u826340212_agenda";
    $password_agenda = "Cwo9982061148";
    $database_agenda = "u826340212_agenda";
    
    $conexion_agenda = new mysqli($hostname_agenda, $username_agenda, $password_agenda, $database_agenda);
    
    if (!$conexion_agenda->connect_error) {
        $conexion_agenda->set_charset('utf8mb4');
        
        // Determinar la tabla según el centro
        $tabla_calendario = strtolower($centro) === 'playa' ? 'playa' : 'cancun';
        
        // Formatear fechas para el calendario
        $inicio = $date . ' ' . $start_time;
        $fin = $date . ' ' . $end_time;
        
        // Insertar en la tabla de calendario
        $stmt_cal = $conexion_agenda->prepare(
            "INSERT INTO $tabla_calendario (titulo, operador, servicio, color, inicio, fin, observa) 
             VALUES (?, ?, ?, ?, ?, ?, ?)"
        );
        
        $titulo = $location ?: $subject;
        $stmt_cal->bind_param(
            "sssssss",
            $titulo,
            $operator_name,
            $subject,
            $color,
            $inicio,
            $fin,
            $notes
        );
        
        $stmt_cal->execute();
        $stmt_cal->close();
        $conexion_agenda->close();
    }
    
    // Confirmar transacción
    $conexion->commit();
    
    // Respuesta exitosa
    echo json_encode([
        'success' => true,
        'message' => 'Agenda creada exitosamente.',
        'agenda' => [
            'id' => $agenda_id,
            'operator_id' => $operator_id,
            'operator_name' => $operator_name,
            'date' => $date,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'subject' => $subject,
            'notes' => $notes,
            'location' => $location,
            'centro' => $centro,
            'color' => $color
        ]
    ]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conexion->rollback();
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

$conexion->close();
?>
