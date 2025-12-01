<?php 
	require_once "conexion.php";
	$conexion=conexion();
	$id=$_POST['id'];

	// Eliminación lógica: cambiar status de 0 a 1 en lugar de DELETE
	$sql="UPDATE operadores SET status = 1 WHERE id='$id'";
	echo mysqli_query($conexion,$sql) ? 1 : 0;
?>