<?php
session_start();
include_once("../locale/db.php");

$id = filter_input(INPUT_POST, 'id', FILTER_SANITIZE_NUMBER_INT);
$titulo = filter_input(INPUT_POST, 'titulo', FILTER_SANITIZE_STRING);
$operador = filter_input(INPUT_POST, 'operador', FILTER_SANITIZE_STRING);
$servicio = filter_input(INPUT_POST, 'servicio', FILTER_SANITIZE_STRING);
$observa = filter_input(INPUT_POST, 'observa', FILTER_SANITIZE_STRING);
$color = filter_input(INPUT_POST, 'color', FILTER_SANITIZE_STRING);
$inicio = filter_input(INPUT_POST, 'inicio', FILTER_SANITIZE_STRING);
$fin = filter_input(INPUT_POST, 'fin', FILTER_SANITIZE_STRING);

if(!empty($id) && !empty($titulo) && !empty($color) && !empty($inicio) && !empty($fin)){
    // Convertir fecha y hora al formato correcto
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
    
    $consulta_eventos = "UPDATE playa SET titulo='$titulo', operador='$operador', servicio='$servicio', color='$color', inicio='$inicio_barra', fin='$fin_barra', observa='$observa' WHERE id='$id'";
    $resultado_eventos = mysqli_query($conexion, $consulta_eventos);
    
    if($resultado_eventos){
        $_SESSION['mensaje'] = "<div class='alert alert-success' role='alert' style='position:absolute;left:450px'>Evento modificado con éxito<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
        header("Location: ../index.php");
    }else{
        $_SESSION['mensaje'] = "<div class='alert alert-danger' role='alert' style='position:absolute;left:450px'>Error al modificar el evento <button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
        header("Location: ../index.php");
    }
}else{
    $_SESSION['mensaje'] = "<div class='alert alert-danger' role='alert' style='position:absolute;left:450px'>Error: datos incompletos<button type='button' class='close' data-dismiss='alert' aria-label='Close'><span aria-hidden='true'>&times;</span></button></div>";
    header("Location: ../index.php");
}
?>