<?php
	// incluimos la conexión a MySQL

	include_once('dbcon.php');

	// variables para insertar datos a mysqli
	$cliente = strip_tags(trim($_POST["cliente"]));
    $cliente = str_replace(array("\r","\n"),array(" "," "), $cliente);
    $servicio = filter_var(trim($_POST["servicio"]), FILTER_SANITIZE_STRING);
    $cantidad = filter_var(trim($_POST["cantidad"]));
    $fini = $_POST['fechainicial'];
	$ffin = $_POST['fechafinal'];
	$srv=substr($servicio,0,3);
    
    $query = "INSERT INTO controlh (cliente, servicio, cantidad, finicio, ffin) 
	VALUES('$cliente', '$srv', '$cantidad', '$fini', '$ffin')";
    
	if ($con->query($query)) {  
        return true;
    }else{
        return false;
    }

?>