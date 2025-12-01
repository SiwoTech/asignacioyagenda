<?php 
	session_start();
	require_once "../php/conexion.php";
	$conexion=conexion();
    $level=$_SESSION['level'];
    $afil=$_SESSION['afiliado'];
 ?>
<div class="row">
	<div class="col-sm-12">
	<h2 class="text-danger">Operadores Eliminados</h2>
		<table class="table table-hover table-condensed table-bordered align-middle">
			<tr>
				<td>ID</td>
				<td>Afiliado</td>
				<td>Nombre</td>
				<td>Código</td>
				<td>Telefono</td>
				<td>Departamento</td>
				<td>Asignación</td>
				<td>Contrato</td>
				<td>Categoria</td>
				<td>Equipo</td>
				<td>Centro</td>
				<td>Restaurar</td>
			</tr>
			<?php 
				// Solo mostrar operadores eliminados (status = 1)
				if ($level<10){
				    $sql = "SELECT * FROM operadores WHERE status = 1 AND afiliado='$afil'";
				}else{
				    $sql = "SELECT * FROM operadores WHERE status = 1";
				}
				
				$result=mysqli_query($conexion,$sql);
				while($ver=mysqli_fetch_row($result)){ 
			 ?>
			<tr class="table-danger">
				<td><?php echo $ver[0] ?></td>
				<td><?php echo $ver[1] ?></td>
				<td><?php echo $ver[2] ?></td>
				<td><?php echo $ver[3] ?></td>
				<td><?php echo $ver[4] ?></td>
				<td><?php echo $ver[5] ?></td>
				<td><?php echo $ver[6] ?></td>
				<td><?php echo $ver[7] ?></td>
				<td><?php echo $ver[8] ?></td>
				<td><?php echo $ver[9] ?></td>
				<td><?php echo $ver[10] ?></td>
				<td>
					<button 
						class="btn btn-sm btn-success rounded-circle shadow-sm" 
						onclick="restaurarOperador('<?php echo $ver[0] ?>')" 
						title="Restaurar operador">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-arrow-clockwise" viewBox="0 0 16 16">
						  <path fill-rule="evenodd" d="M8 3a5 5 0 1 1-4.546 2.914.5.5 0 0 0-.908-.417A6 6 0 1 0 8 2v1z"/>
						  <path d="M8 4.466V.534a.25.25 0 0 0-.41-.192L5.23 2.308a.25.25 0 0 0 0 .384l2.36 1.966A.25.25 0 0 0 8 4.466z"/>
						</svg>
					</button>
				</td>
			</tr>
			<?php
		}
			 ?>
		</table>
	</div>
</div>