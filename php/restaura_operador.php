<?php
require_once "conexion.php";
$conexion = conexion();
$id = $_POST['id'];

// Restaurar: cambiar status de 1 a 0
$sql = "UPDATE operadores SET status = 0 WHERE id='$id'";
echo mysqli_query($conexion, $sql) ? 1 : 0;
?>