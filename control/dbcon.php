<?php
	
	// Creamos las variables de conexión
	$servername = "localhost";
	$username = "u826340212_orangedb";
	$password = "Cwo9982061148";
	$database = "u826340212_orangedb";
	// Creamos la conexion con MySQL
	$con = new mysqli($servername, $username, $password, $database);
	// Revisamos la conexión
	if ($con->connect_error) {
	  	die("Conexión fallida: " . $con->connect_error);
	}
	
?>