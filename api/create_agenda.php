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

// Validar centro de trabajo contra whitelist
$centrosPermitidos = ['Cancun', 'Playa', 'Xcaret'];
if (!in_array($centro, $centrosPermitidos)) {
    $centro = 'Cancun'; // Valor por defecto si no es válido
}

// Conexión a la base de datos usando la función existente
require_once __DIR__ . '/../php/conexion.php';
$conexion = conexion();

if (!$conexion) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Error de conexión a la base de datos.']);
    exit;
}

mysqli_set_charset($conexion, 'utf8mb4');

// Iniciar transacción
mysqli_begin_transaction($conexion);

try {
    // Verificar que el operador existe
    $stmt = mysqli_prepare($conexion, "SELECT id, nombre, centro FROM operadores WHERE id = ? AND status = 0");
    mysqli_stmt_bind_param($stmt, "i", $operator_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if (mysqli_num_rows($result) === 0) {
        throw new Exception('Operador no encontrado o inactivo.');
    }
    
    $operador = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    // Usar el centro del operador si no se especificó y es válido
    if (!empty($operador['centro']) && in_array($operador['centro'], $centrosPermitidos)) {
        $centro = $operador['centro'];
    }
    
    // Insertar en la tabla agendas
    // Nota: La tabla debe existir previamente. Ejecutar sql/create_agendas_table.sql si no existe.
    $stmt = mysqli_prepare($conexion, 
        "INSERT INTO agendas (operator_id, operator_name, date, start_time, end_time, subject, notes, location, centro, color) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    
    mysqli_stmt_bind_param($stmt, 
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
    
    if (!mysqli_stmt_execute($stmt)) {
        throw new Exception('Error al insertar la agenda: ' . mysqli_stmt_error($stmt));
    }
    
    $agenda_id = mysqli_insert_id($conexion);
    mysqli_stmt_close($stmt);
    
    // También insertar en la tabla de calendario correspondiente (cancun o playa)
    // Conexión separada a la base de datos de agenda
    $conexion_agenda = mysqli_connect("localhost", "u826340212_agenda", "Cwo9982061148", "u826340212_agenda");
    
    if ($conexion_agenda && !mysqli_connect_error()) {
        mysqli_set_charset($conexion_agenda, 'utf8mb4');
        
        // Determinar la tabla según el centro (whitelist segura)
        $tabla_calendario = (strtolower($centro) === 'playa') ? 'playa' : 'cancun';
        
        // Formatear fechas para el calendario
        $inicio = $date . ' ' . $start_time;
        $fin = $date . ' ' . $end_time;
        
        // Usar consulta con nombre de tabla fijo para evitar inyección SQL
        $titulo = $location ?: $subject;
        
        if ($tabla_calendario === 'playa') {
            $stmt_cal = mysqli_prepare($conexion_agenda,
                "INSERT INTO playa (titulo, operador, servicio, color, inicio, fin, observa) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
        } else {
            $stmt_cal = mysqli_prepare($conexion_agenda,
                "INSERT INTO cancun (titulo, operador, servicio, color, inicio, fin, observa) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)"
            );
        }
        
        if ($stmt_cal) {
            mysqli_stmt_bind_param($stmt_cal, 
                "sssssss",
                $titulo,
                $operator_name,
                $subject,
                $color,
                $inicio,
                $fin,
                $notes
            );
            
            mysqli_stmt_execute($stmt_cal);
            mysqli_stmt_close($stmt_cal);
        }
        
        mysqli_close($conexion_agenda);
    }
    
    // Confirmar transacción
    mysqli_commit($conexion);
    
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
    mysqli_rollback($conexion);
    
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

mysqli_close($conexion);
?>
