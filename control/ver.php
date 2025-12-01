<?php
	// incluimos el fichero de conexión

	require_once('dbcon.php');
	
	// extraemos la informacion de la tabla clientes..
	
	echo "<table class='table table-hover table-striped'>
			<thead>
				<tr>
					<th>Id</th>
					<th>Cliente</th>
					<th>Servicio</th>
					<th>Cantidad</th>
					<th>Fecha Inicio</th>
					<th>Fecha Fin</th>
					<th>Realizados</th>
					<th>Pendientes</th>
					<th>Editar</th>						
					<th>Eliminar</th>						
				</tr>
			</thead>";
	$cuenta=0;
	$resultcli=mysqli_query($con, "SELECT * FROM controlh");
	 while($vercli=mysqli_fetch_row($resultcli)){
		$clientectrl=$vercli[1];
		$srvctrl=trim($vercli[2]);
		$cantctrl=$vercli[3];
		$fechainictrl=$vercli[4];
		$fechafinctrl=$vercli[5];	
	
		$resultord=mysqli_query($con, "SELECT * FROM ordenes WHERE fechaarriv BETWEEN '$fechainictrl' AND '$fechafinctrl' ");
		while ($verord = mysqli_fetch_row($resultord)) {
			$srv=substr($verord[5],0,3);								
			$cliente=$verord[4];
			$fecha=$verord[7];
			
			if ($cliente==$clientectrl){
				if ($srvctrl==$srv){					
					$cuenta=$cuenta+1;				
				}
			}	
		}
		$restan=$cantctrl-$cuenta;
	 	
		if ($resultcli->num_rows  > 0) {
			if ($restan==0){
			$editar="none";			
			}else{
			$editar="block";
			}	
			$output = "";			
				$output .= "<tbody>
					<tr>
						<td>{$vercli[0]}</td>
						<td>{$vercli[1]}</td>
						<td>{$vercli[2]}</td>
						<td>{$vercli[3]}</td>
						<td>{$vercli[4]}</td>
						<td>{$vercli[5]}</td>
						<td>{$cuenta}</td>
						<td>{$restan}</td>									
						<td><button class='btn btn-success btn-sm editar-btn' data-id='{$vercli[0]}' data-toggle='modal' data-target='#exampleModal' style='display:".$editar."'>Editar</button></td>
						<td><button class='btn btn-danger btn-sm borrar-btn' data-id='{$vercli[0]}' style='display:".$editar."'>Borrar</button></td>
					</tr>
				</tbody>";
			echo $output;
			$output .="</table>";
				
		}else{
			echo "<h5>Ningún registro fue encontrado</h5>";
		}
		$cuenta=0;
		
	}	
		
?>
