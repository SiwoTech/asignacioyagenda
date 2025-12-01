<?php 
	session_start();
	require_once "../php/conexion.php";
	$conexion=conexion();
    $level=$_SESSION['level'];
    $afil=$_SESSION['afiliado'];
 ?>
<div class="row">
	<div class="col-sm-12">
	<h2>Gestión de Operadores</h2><?php echo "afiliado".$afil?>
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
				<td>Editar</td>
				<?php if ($level==10){ ?>
				<td>Eliminar</td>
				<?php }?>
			</tr>
			<?php 
				if(isset($_SESSION['consulta'])){
					if($_SESSION['consulta'] > 0){
						$idp=$_SESSION['consulta'];
						$sql="SELECT * from operadores where id='$idp' AND status=0 AND afiliado='$afil'"; // ← Cambié aquí
					}else{
						$sql="SELECT * from operadores WHERE status=0 AND afiliado='$afil'"; // ← Cambié aquí
					}
				}else{
				    if ($level<10){
					$sql="SELECT * from operadores WHERE status=0 AND afiliado='$afil'"; // ← Cambié aquí
				    }else{
				    $sql="SELECT * from operadores WHERE status=0";   
				    }
				}
				$result=mysqli_query($conexion,$sql);
				while($ver=mysqli_fetch_row($result)){ 
					$datos=$ver[0]."||".
						   $ver[1]."||".
						   $ver[2]."||".
						   $ver[3]."||".
						   $ver[4]."||".
						   $ver[5]."||".
						   $ver[6]."||".
						   $ver[7]."||".
						   $ver[8]."||".
						   $ver[9]."||".
						   $ver[10]."||".
						   $ver[11];
			 ?>
			<tr>
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
						class="btn btn-sm rounded-circle shadow-sm" 
						style="background-color: #E55B26; color: #fff;" 
						data-bs-toggle="modal" 
						data-bs-target="#modalEdicion" 
						onclick="agregaform('<?php echo $datos ?>')" 
						title="Editar operador">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-pencil" viewBox="0 0 16 16">
						  <path d="M12.146.854a.5.5 0 0 1 .708 0l2.292 2.292a.5.5 0 0 1 0 .708l-9.193 9.193a.5.5 0 0 1-.168.11l-4 1.5a.5.5 0 0 1-.65-.65l1.5-4a.5.5 0 0 1 .11-.168l9.193-9.193zM11.207 2.5 2 11.707V13h1.293L13.5 4.793l-2.293-2.293zm1.086-1.086a1.5 1.5 0 0 1 2.121 2.121l-.646.647-2.121-2.121.646-.647z"/>
						</svg>
					</button>
				</td>
				<?php if ($level==10){ ?>
				<td>
					<button 
						class="btn btn-sm btn-danger rounded-circle shadow-sm" 
						onclick="preguntarSiNo('<?php echo $ver[0] ?>')" 
						title="Eliminar operador">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16">
						  <path d="M5.5 5.5A.5.5 0 0 1 6 5h4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-.5.5H6a.5.5 0 0 1-.5-.5v-7zm1.5.5v6h2V6h-2z"/>
						  <path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1-1V1h-11v1a1 1 0 0 1-1 1H1v1h14V3h-.5zM5.5 1v1h5V1h-5zm7 1v1h-12V2h12z"/>
						</svg>
					</button>
				</td>
				<?php }?>
			</tr>
			<?php
		}
			 ?>
		</table>
	</div>
</div>