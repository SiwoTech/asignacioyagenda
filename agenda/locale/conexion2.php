

<?php 
		function conexion(){
			$servidor="localhost";
			$usuario="u826340212_orangedb";
			$password="Cwo9982061148";
			$bd="u826340212_orangedb";

			$conexion2=mysqli_connect($servidor,$usuario,$password,$bd);

			return $conexion2;
		}


 ?>