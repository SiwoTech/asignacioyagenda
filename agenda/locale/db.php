<?php
$hostname = "localhost";
$usuariodb = "u826340212_agenda";
$contrasenadb = "Cwo9982061148";
$dbname = "u826340212_agenda";
	
// Generar conexion con el servidor MySQl
$conexion = mysqli_connect($hostname, $usuariodb, $contrasenadb, $dbname);

$DB_HOST="localhost";
$DB_USERNAME="u826340212_orangedb";
$DB_PASSWORD="Cwo9982061148";
$DB_NAME="u826340212_orangedb";

$conexion2 = mysqli_connect($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);