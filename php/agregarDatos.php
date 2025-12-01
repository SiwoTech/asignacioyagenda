<?php
require_once "conexion.php";
$conexion = conexion();

$afiliado = $_POST['afiliado'];
$nombre = $_POST['nombre'];
$codeoper = $_POST['codigo']; // ← Cambié aquí: recibimos 'codigo' pero lo guardamos en 'codeoper'
$telefono = $_POST['telefono'];
$departamento = $_POST['departamento'];
$centro = $_POST['centro'];
$asignacion = $_POST['asignacion'];
$contrato = $_POST['contrato'];
$categoria = $_POST['categoria'];
$team = $_POST['equipo']; // equipo → team

$sql = "INSERT INTO operadores (afiliado, nombre, codeoper, telefono, departamento, asignacion, contrato, categoria, team, centro, status) 
        VALUES ('$afiliado', '$nombre', '$codeoper', '$telefono', '$departamento', '$asignacion', '$contrato', '$categoria', '$team', '$centro', 0)";

echo mysqli_query($conexion, $sql) ? 1 : 0;
?>