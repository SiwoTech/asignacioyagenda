<?php
session_start();
include_once("../locale/db.php");
// Agregar operador a la consulta para mostrarlo en el título
$consulta_eventos = "SELECT id, titulo, operador, servicio, color, inicio, fin, observa FROM cancun";
$resultado_eventos = mysqli_query($conexion, $consulta_eventos);
?>

<script>
$(document).ready(function() {
    if(typeof $.fn.fullCalendar === 'undefined') {
        console.error('FullCalendar no está cargado para Cancún');
        return;
    }
    
    $('#calendar-cancun').fullCalendar({
        header: {
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
        },
        locale: 'es',
        navLinks: true,
        editable: true,
        eventLimit: true,
        height: 600,
        eventClick: function(event) {
            $('#visualizar-cancun #id-cancun').text(event.id);
            $('#visualizar-cancun #title-cancun').text(event.hotel); // Solo hotel
            $('#visualizar-cancun #oper-cancun').text(event.oper);
            $('#visualizar-cancun #srv-cancun').text(event.srv);
            $('#visualizar-cancun #observa-cancun').text(event.observa);
            $('#visualizar-cancun #start-cancun').text(event.start.format('DD/MM/YYYY HH:mm:ss'));
            $('#visualizar-cancun #end-cancun').text(event.end.format('DD/MM/YYYY HH:mm:ss'));
            $('#visualizar-cancun').modal('show');

            $('#btnEditar-cancun').off('click').on('click', function() {
                $('#edit_id_cancun').val(event.id);
                $('#edit_titulo_cancun').val(event.hotel); // Solo hotel para editar
                $('#edit_operador_cancun').val(event.oper);
                $('#edit_servicio_cancun').val(event.srv);
                $('#edit_observa_cancun').val(event.observa);
                $('#edit_color_cancun').val(event.color);
                $('#edit_inicio_cancun').val(event.start.format('DD/MM/YYYY HH:mm:ss'));
                $('#edit_fin_cancun').val(event.end.format('DD/MM/YYYY HH:mm:ss'));
                $('#visualizar-cancun').modal('hide');
                $('#modificar-cancun').modal('show');
            });
            return false;
        },
        selectable: true,
        selectHelper: true,
        select: function(start, end){
            $('#cadastrar-cancun #start-cancun').val(moment(start).format('DD/MM/YYYY HH:mm:ss'));
            $('#cadastrar-cancun #end-cancun').val(moment(end).format('DD/MM/YYYY HH:mm:ss'));
            $('#cadastrar-cancun').modal('show');
        },
        events: [
            <?php
            while($registros_eventos = mysqli_fetch_array($resultado_eventos)){
                // Limpiar el título (quitar el 0 del inicio si existe)
                $titulo_limpio = ltrim($registros_eventos['titulo'], '0'); // Quita ceros del inicio
                $titulo_limpio = trim($titulo_limpio); // Quita espacios
                
                // Crear título combinado: Hotel + Operador
                $titulo_completo = $titulo_limpio;
                if (!empty($registros_eventos['operador'])) {
                    $titulo_completo .= ' - ' . $registros_eventos['operador'];
                }
            ?>
                {
                    id: '<?php echo $registros_eventos['id']; ?>',
                    title: '<?php echo $titulo_completo; ?>', // Hotel + Operador
                    hotel: '<?php echo $titulo_limpio; ?>', // Solo hotel (para edición)
                    oper:'<?php echo $registros_eventos['operador']; ?>',
                    srv:'<?php echo $registros_eventos['servicio']; ?>',
                    observa:'<?php echo $registros_eventos['observa']; ?>',
                    start: '<?php echo $registros_eventos['inicio']; ?>',
                    end: '<?php echo $registros_eventos['fin']; ?>',
                    color: '<?php echo $registros_eventos['color']; ?>',
                },<?php
            }
            ?>
        ]
    });
});

function DataHoraCancun(evento, objeto){
    var keypress=(window.event)?event.keyCode:evento.which;
    campo = eval (objeto);
    if (campo.value == '00/00/0000 00:00:00'){
        campo.value=""
    }
    caracteres = '0123456789';
    separacion1 = '/';
    separacion2 = ' ';
    separacion3 = ':';
    conjunto1 = 2;
    conjunto2 = 5;
    conjunto3 = 10;
    conjunto4 = 13;
    conjunto5 = 16;
    if ((caracteres.search(String.fromCharCode (keypress))!=-1) && campo.value.length < (19)){
        if (campo.value.length == conjunto1 )
        campo.value = campo.value + separacion1;
        else if (campo.value.length == conjunto2)
        campo.value = campo.value + separacion1;
        else if (campo.value.length == conjunto3)
        campo.value = campo.value + separacion2;
        else if (campo.value.length == conjunto4)
        campo.value = campo.value + separacion3;
        else if (campo.value.length == conjunto5)
        campo.value = campo.value + separacion3;
    }else{
        event.returnValue = false;
    }
}
</script>

<?php
if(isset($_SESSION['mensaje'])){
    echo $_SESSION['mensaje'];
    unset($_SESSION['mensaje']);
}
?>

<div class="page-header">
    <h2 style="color:#E55B26;">Cancún</h2>
</div>
<div id='calendar-cancun'></div>

<!-- Modal Visualizar Evento Cancún -->
<div class="modal fade" id="visualizar-cancun" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">Información - Cancún</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt>ID</dt><dd id="id-cancun"></dd>
                    <dt>Evento</dt><dd id="title-cancun"></dd>
                    <dt>Operador</dt><dd id="oper-cancun"></dd>
                    <dt>Servicio</dt><dd id="srv-cancun"></dd>
                    <dt>Observaciones</dt><dd id="observa-cancun"></dd>
                    <dt>Inicio</dt><dd id="start-cancun"></dd>
                    <dt>Fin</dt><dd id="end-cancun"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnEditar-cancun">Editar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modificar Cancún -->
<div class="modal fade" id="modificar-cancun" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" method="POST" action="cancun/modificarc.php">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center">Modificar Evento - Cancún</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id_cancun" name="id">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_titulo_cancun" name="titulo" placeholder="Cliente">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Operador</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_operador_cancun" name="operador" placeholder="Operador">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Servicio</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_servicio_cancun" name="servicio" placeholder="Servicio">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Observaciones</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_observa_cancun" name="observa" placeholder="Observaciones">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Color</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_color_cancun" name="color" placeholder="Color">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fecha Inicial</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_inicio_cancun" name="inicio" onKeyPress="DataHoraCancun(event, this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fecha Final</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_fin_cancun" name="fin" onKeyPress="DataHoraCancun(event, this)">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Guardar Cambios</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Registrar Cancún -->
<div class="modal fade" id="cadastrar-cancun" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">Registrar Servicios - Cancún</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="cancun/procesoc.php">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-10">
                            <select name="cbxCHotel" class="form-control" id="cbxCHotel-cancun" onchange="ShowSelectedCancun();">
                                <option value="">Seleccione Cliente</option>
                                <?php 
                                $result1 = mysqli_query($conexion2,"SELECT * FROM codhotel ORDER BY hotel");
                                while ($r1 = mysqli_fetch_array($result1)){
                                    echo '<option value="'.$r1[2].'"> '.$r1[2].'</option>';
                                }
                                ?>
                            </select>
                            <script type="text/javascript">
                            function ShowSelectedCancun(){
                                var codigo=document.getElementById('cbxCHotel-cancun').value;
                                document.getElementById('ebcliente-cancun').value = codigo;
                            }
                            </script>
                            <input type="text" class="form-control" id="ebcliente-cancun" name="titulo" placeholder="Cliente" style="display:none">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Operador</label>
                        <div class="col-sm-10">
                            <select name="operador" class="form-control" id="operador-cancun">
                                <option value="">Seleccione Operador</option>
                                <?php 
                                $result2 = mysqli_query($conexion2,"SELECT * FROM operadores WHERE status=0 AND afiliado='CWO' ORDER BY nombre");
                                while ($r2 = mysqli_fetch_array($result2)){
                                    echo '<option value="'.$r2[2].'"> '.$r2[2].'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Servicio</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="servicio" placeholder="Servicio a Realizar">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Observaciones</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="observa" placeholder="Comentarios y Observaciones">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Color</label>
                        <div class="col-sm-10">
                            <select name="color" class="form-control" id="color-cancun">
                                <option value="">Selecione</option>
                                <option style="color:#FFD700;" value="#FFD700">Amarillo</option>
                                <option style="color:#0071c5;" value="#0071c5">Azul Turquesa</option>
                                <option style="color:#FF4500;" value="#FF4500">Naranja</option>
                                <option style="color:#8B4513;" value="#8B4513">Marron</option>
                                <option style="color:#1C1C1C;" value="#1C1C1C">Negro</option>
                                <option style="color:#436EEE;" value="#436EEE">Azul Real</option>
                                <option style="color:#A020F0;" value="#A020F0">Purpura</option>
                                <option style="color:#40E0D0;" value="#40E0D0">Turquesa</option>
                                <option style="color:#228B22;" value="#228B22">Verde</option>
                                <option style="color:#8B0000;" value="#8B0000">Rojo</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fecha Inicial</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="inicio" id="start-cancun" onKeyPress="DataHoraCancun(event, this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fecha Final</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="fin" id="end-cancun" onKeyPress="DataHoraCancun(event, this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <button type="submit" class="btn btn-success">Registrar</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>