<?php 
	require_once "conexion.php";
	$conexion=conexion();
	$id=$_POST['id'];
	$a=$_POST['afiliado'];
	$n=$_POST['nombre'];
	$c=$_POST['codigo'];
	$t=$_POST['telefono'];
	$d=$_POST['departamento'];
	$ce=$_POST['centro'];
	$s=$_POST['asignacion'];
	$o=$_POST['contrato'];
	$g=$_POST['categoria'];
	$e=$_POST['equipo'];

	$sql="UPDATE operadores set afiliado='$a', 
								nombre='$n', 
								codeoper='$c',
								telefono='$t',
								departamento='$d',
								asignacion='$s',
								contrato='$o',
								categoria='$g',
								team='$e',
								centro='$ce'
				where id='$id'";
	echo $result=mysqli_query($conexion,$sql);

 ?>
