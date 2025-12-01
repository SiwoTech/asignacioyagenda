<?php


$mysqli = new mysqli("localhost","u826340212_orangedb","Cwo9982061148","u826340212_orangedb"); 
	

$asig = $_POST["elegido"];

$sql="SELECT * FROM contratos WHERE pseudocliente = '$asig' ";
$result=$mysqli ->query($sql);


$html= "<option value='0'>Seleccionar Contrato</option>";
while($valores = $result->fetch_assoc())
{
    $html.=  '<option value="'.$valores['orden'].'">'.$valores['orden'].'</option>';
}

echo $html;
?>



