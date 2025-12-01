<!DOCTYPE html>
<?php
require_once('dbcon.php');
?>
<html lang="en">
<head>
  <title>Control de Operaciones por Hotel</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/bootstrap.min.css">
  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/bootstrap.min.js"></script>
</head>
<body>
	
	<div class="container">
	<center><img src="images/LOGO.png" id="logohead" alt="" width="150" height="40"></center>
		<br>
		<div class="card">
			<div class="card-header">
				Control de Operaciones
			
			</div>
			<div class="card-body" style="width:1080px;height:200px;">
 
				<form class="form-horizontal" id="clienteForm">
					<div class="form-group row">
						<label class="control-label col-sm-5" for="nombre">Cliente:</label>
						<label class="control-label col-sm-5" for="servicio">Servicio:</label>
						<label class="control-label col-sm-2" for="cantidad">Cantidad:</label>
						<div class="col-sm-10">          
							<select name="cbxCHotel" size="1" id="cbxCHotel" onchange="ShowSelected2();" style="display:block;width:350px;height:28px;z-index:10;">
								<option value="Seleccione">Seleccione:</option>
								<?php 
									$result2 = mysqli_query($con,"SELECT * FROM codhotel ORDER BY hotel");
									while ($r2 = mysqli_fetch_array($result2)){
										echo '<option value="'.$r2[1].'"> '.$r2[2].'</option>';
									}
								?>
							</select>
							<select name="cbxServicio" size="1" id="cbxServicio" onchange="ShowSelected();" style="position:absolute;left:472px;top:0px;width:350px;height:28px;z-index:14;">
								<option value="0">Seleccione:</option>
								<?php 				
									$result = mysqli_query($con,"SELECT * FROM baseprecios ORDER BY descripcion");
									while ($r = mysqli_fetch_array($result)){
										echo '<option value="'.$r[1].'">'.$r[2].'</option>';
									}	 
								?>
							</select>
							
							<script type"text/javascript">
								function ShowSelected2(){
									var codigo=document.getElementById('cbxCHotel').value;
									document.getElementById('cliente').value = codigo;
								}                       
							</script>						
							<input type="text" class="form-control" id="cliente" name="cliente" value="" spellcheck="false" style="position:absolute;left:15px;top:28px;width:200px;height:28px;z-index:14;"disabled>													
							
							<script type"text/javascript">
								function ShowSelected(){
									var clave=document.getElementById('cbxServicio').value;
									document.getElementById('servicio').value = clave;
								}
							</script>    			
							<input type="text" class="form-control" id="servicio" name="servicio" value="" spellcheck="false" style="position:absolute;left:472px;top:28px;width:200px;height:28px;z-index:14;" disabled>
										    
							<input type="number" id="cantidad" placeholder="Cantidad de Servicios a Realizar" name="cantidad" style="position:absolute;left:840px;top:0px;width:245px;height:28px;z-index:15;">
						</div>
					
					
						<div class="form-group row">
							<label  for="fechaini" style="position:absolute;left:15px;width:150px;top:180px;height:28px;z-index:15;">Fecha Inicio:</label>
							<label  for="fechafin" style="position:absolute;left:195px;width:150px;top:180px;height:28px;z-index:15;">Fecha Final:</label>
						        
									<input type="date" class="form-control" id="fechaini" name="fechaini" style="position:absolute;left:15px;top:210px;width:150px;height:28px;z-index:15;">
									<input type="date" class="form-control" id="fechafin" name="fechafin" style="position:absolute;left:195px;top:210px;width:150px;height:28px;z-index:15;">
							
						</div>
								
    	
						<div class="form-group row">        
							<div style="position:absolute;left:750px;top:200px;width:150px;height:28px;z-index:15;">
								<button type="submit" class="btn btn-primary" id="registro">Registrar cliente</button>
							</div>
						</div>
					</div>	
				</form>
			</div>
		</div>
		<div id="tableData">
  		</div>

		<div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel">
			<div class="modal-dialog" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h2 class="modal-title" id="exampleModalLabel"><b>Editar cliente</b></h2>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div id="editarForm">                
					</div>
				</div>
			</div>
		</div> 
	</div>
	<script type="text/javascript" src="assets/js/main.js"></script>
</body>
</html>