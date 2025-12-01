<?php 
	require_once "../php/conexion.php";
	$conexion = conexion();

	// SOLO operadores activos (status = 0)
	$sql = "SELECT id, nombre, afiliado, codeoper 
	        FROM operadores 
	        WHERE status = 0 
	        ORDER BY nombre";
	$result = mysqli_query($conexion, $sql);
?>

<br><br>
<div class="row">
	<div class="col-sm-8"></div>
	<div class="col-sm-4">
		<label>Buscador de Operadores</label>
		<div class="input-group">
			<select id="buscadorvivo" class="form-control input-sm">
				<option value="0">Selecciona un operador</option>
				<?php while($ver = mysqli_fetch_row($result)): ?>
					<option value="<?php echo $ver[0] ?>">
						<?php echo $ver[1] . " (" . $ver[2] . " - " . $ver[3] . ")" ?>
					</option>
				<?php endwhile; ?>
			</select>
			<button class="btn btn-outline-secondary btn-sm" type="button" onclick="limpiarBusqueda()" title="Limpiar búsqueda">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-x" viewBox="0 0 16 16">
					<path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
				</svg>
			</button>
		</div>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		$('#buscadorvivo').select2({
			placeholder: "Buscar operador...",
			allowClear: true
		});

		$('#buscadorvivo').change(function(){
			var valorSeleccionado = $('#buscadorvivo').val();
			
			$.ajax({
				type: "POST",
				data: 'valor=' + valorSeleccionado,
				url: 'php/crearsession.php',
				success: function(r){
					$('#tabla').load('componentes/tabla.php');
				},
				error: function() {
					console.log('Error al filtrar operadores');
				}
			});
		});
	});

	function limpiarBusqueda() {
		$('#buscadorvivo').val(0).trigger('change');
	}
</script>