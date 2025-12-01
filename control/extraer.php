<?php
	// incluimos fichero de conexión
	require_once('dbcon.php');

	if (isset($_POST['editarId'])) {
		$editarId = $_POST['editarId'];
	}
	// extraer tabla clientes..
	
	$sql = "SELECT * FROM controlh WHERE id = {$editarId}";
	$query = $con->query($sql);
	if ($query->num_rows > 0) {
		$output = "";
		while ($row = $query->fetch_assoc()) {
	    $output .= "<form>
                      <div class='modal-body'>
                      	<input type='hidden' class='form-control' id='editarId' value='{$row['id']}'>
                        <div class='form-group'>
						<label class='control-label' for='cliente'>Cliente:</label>
                            <input type='text' class='form-control' id='editarcliente' value='{$row['cliente']}'>
                        </div>
                        <div class='form-group'>
						<label class='control-label' for='servicio'>Servicio:</label>
                            <input type='text' class='form-control' id='editarservicio' value='{$row['servicio']}'>
                        </div>
						<div class='form-group'>
						<label class='control-label' for='cantidad'>Cantidad:</label>
                            <input type='text' class='form-control' id='editarcantidad' value='{$row['cantidad']}'>
                        </div>
                        <div class='form-group'>
						<label class='control-label' for='fechaini'>Fecha Inicio:</label>
                            <input type='text' class='form-control' id='editarfini' value='{$row['finicio']}'>
                        </div>
						<div class='form-group'>
						<label class='control-label' for='fechafin'>Fecha Inicio:</label>
                            <input type='text' class='form-control' id='editarffin' value='{$row['ffin']}'>
                        </div>
					  
					  
					  </div>
                      <div class='modal-footer'>
                        <button type='button' class='btn btn-secondary' data-dismiss='modal'>Cerrar</button>
                        <button type='button' class='btn btn-info' id='editarSubmit'>Guardar cambios</button>
                      </div>
                  </form>";         	
	    }
	    $output .="</table>";
	}
	echo $output;

?>
