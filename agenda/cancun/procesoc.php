<?php
session_start();

//Fichero de conexion con la base de datos
include_once("../locale/db.php");

$titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
$operador = filter_input(INPUT_POST, 'operador', FILTER_SANITIZE_STRING);
$servicio = filter_input(INPUT_POST, 'servicio', FILTER_SANITIZE_STRING);
$observa = filter_input(INPUT_POST, 'observa', FILTER_SANITIZE_STRING);
$color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
$inicio = filter_input(INPUT_POST, 'inicio', FILTER_SANITIZE_STRING);
$fin = filter_input(INPUT_POST, 'fin', FILTER_SANITIZE_STRING);

if(!empty($titulo) && !empty($color) && !empty($inicio) && !empty($fin)){
    //Convertir la fecha y la hora del formato
    $data = explode(" ", $inicio);
    list($date, $hora) = $data;
    $data_barra = array_reverse(explode("/", $date));
    $data_barra = implode("-", $data_barra);
    $inicio_barra = $data_barra . " " . $hora;

    $data = explode(" ", $fin);
    list($date, $hora) = $data;
    $data_barra = array_reverse(explode("/", $date));
    $data_barra = implode("-", $data_barra);
    $fin_barra = $data_barra . " " . $hora;

    $consulta_eventos = "INSERT INTO cancun (titulo, operador, servicio, color, inicio, fin, observa) VALUES ('$titulo', '$operador', '$servicio', '$color', '$inicio_barra', '$fin_barra', '$observa')";
    $resultado_eventos = mysqli_query($conexion, $consulta_eventos);

    //Comprobar si guardó en la base de datos a través de "mysqli_insert_id" el cual comprueba si existe el ID del último dato insertado
    if(mysqli_insert_id($conexion)){
        $_SESSION['mensaje'] = "<div class='alert alert-success' role='alert' style='position:absolute;left:450px'>El evento registrado con éxito<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
        header("Location: ../index.php");
    }else{
        $_SESSION['mensaje'] = "<div class='alert alert-danger' role='alert' style='position:absolute;left:450px'>Error al registrar el evento, revise que todos los datos esten correctos<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
        header("Location: ../index.php");
    }

}else{
    $_SESSION['mensaje'] = "<div class='alert alert-danger' role='alert' style='position:absolute;left:450px'>Error al registrar el evento, revise que todos los datos esten correctos <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
    header("Location: ../index.php");
}
?>