

function agregardatos(afiliado,nombre,codigo,telefono,departamento,asignacion,contrato,categoria,equipo){

	cadena="afiliado=" + afiliado +
			"&nombre=" + nombre + 
			"&codigo=" + codigo +
			"&telefono=" + telefono +
			"&departamento=" + departamento +
			"&asignacion=" + asignacion +
			"&contrato=" + contrato +
			"&categoria=" + categoria +
			"&equipo=" + equipo;
	


	$.ajax({
		type:"POST",
		url:"php/agregarDatos.php",
		data:cadena,
		success:function(r){
			if(r==1){
				$('#tabla').load('componentes/tabla.php');
				 $('#buscador').load('componentes/buscador.php');
				alertify.success("agregado con exito :)");
			}else{
				alertify.error("Fallo al guardar :(");
			}
		}
	});

}

function agregaform(datos){

	d=datos.split('||');

	$('#idpersona').val(d[0]);
	$('#afiliadou').val(d[1]);
	$('#nombreu').val(d[2]);
	$('#codigou').val(d[3]);
	$('#telefonou').val(d[4]);
	$('#departamentou').val(d[5]);
	$('#asignacionu').val(d[6]);
	$('#contratou').val(d[7]);
	$('#categoriau').val(d[8]);	
	$('#equipou').val(d[9]);
	
}

function actualizaDatos(){

	id=$('#idpersona').val();
	afiliado=$('#afiliadou').val();
	nombre=$('#nombreu').val();
	codigo=$('#codigou').val();
	telefono=$('#telefonou').val();
	departamento=$('#departamentou').val();
	asignacion=$('#asignacionu').val();
	contrato=$('#contratou').val();
	categoria=$('#categoriau').val();
	equipo=$('#equipou').val();



	cadena="id=" + id +
	"&afiliado=" + afiliado +
	"&nombre=" + nombre + 
	"&codigo=" + codigo +
	"&telefono=" + telefono +
	"&departamento=" + departamento +
	"&asignacion=" + asignacion +
	"&contrato=" + contrato +
	"&categoria=" + categoria +
	"&equipo=" + equipo;



	$.ajax({
		type:"POST",
		url:"php/actualizaDatos.php",
		data:cadena,
		success:function(r){
			
			if(r==1){
				$('#tabla').load('componentes/tabla.php');
				alertify.success("Actualizado con exito :)");
			}else{
				alertify.error("Fallo el servidor :(");
			}
		}
	});

}

function preguntarSiNo(id){
	alertify.confirm('Eliminar Datos', '¿Esta seguro de eliminar este registro?', 
					function(){ eliminarDatos(id) }
                , function(){ alertify.error('Se cancelo')});
}

function eliminarDatos(id){

	cadena="id=" + id;

		$.ajax({
			type:"POST",
			url:"php/eliminarDatos.php",
			data:cadena,
			success:function(r){
				if(r==1){
					$('#tabla').load('componentes/tabla.php');
					alertify.success("Eliminado con exito!");
				}else{
					alertify.error("Fallo el servidor :(");
				}
			}
		});
}