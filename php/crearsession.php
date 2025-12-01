<?php
session_start();

if (isset($_POST['valor'])) {
    $valor = $_POST['valor'];
    
    if ($valor == 0) {
        // Si selecciona "Selecciona un operador", mostrar todos
        unset($_SESSION['consulta']);
    } else {
        // Guardar el ID del operador seleccionado
        $_SESSION['consulta'] = $valor;
    }
    
    echo "1"; // Respuesta exitosa
} else {
    echo "0"; // Error
}
?>