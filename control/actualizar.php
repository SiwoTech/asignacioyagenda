<?php
	// incluimos la conexión
	include "dbcon.php";

	// Variables para editar la tabla por id
	$id = $_POST['id'];
	$cliente = strip_tags(trim($_POST["cliente"]));
    $cliente = str_replace(array("\r","\n"),array(" "," "), $cliente);
    $servicio = filter_var(trim($_POST["servicio"]), FILTER_SANITIZE_STRING);
    $cantidad = filter_var(trim($_POST["cantidad"]));
    $fini = $_POST['fechainicial'];
	$ffin = $_POST['fechafinal'];
	$srv=substr($servicio,0,3);
	
	// SQL para actualizar un registro	
	$query = "UPDATE controlh SET cliente='{$cliente}',servicio='{$servicio}',cantidad='{$cantidad}', finicio='{$fini}' , ffin='{$ffin}' WHERE id='{$id}'";
	if ($con->query($query)) {
		echo 1;
	}else{
		echo 0;
	}
?>