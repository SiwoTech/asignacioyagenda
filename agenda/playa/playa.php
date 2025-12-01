<?php
session_start();
include_once("../locale/db.php");
$consulta_eventos = "SELECT id, titulo, operador, servicio, color, inicio, fin, observa FROM playa";
$resultado_eventos = mysqli_query($conexion, $consulta_eventos);
?>

<script>
$(document).ready(function() {
    if(typeof $.fn.fullCalendar === 'undefined') {
        console.error('FullCalendar no está cargado para Playa');
        return;
    }
    
    $('#calendar-playa').fullCalendar({
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
            $('#visualizar-playa #id-playa').text(event.id);
            $('#visualizar-playa #title-playa').text(event.hotel); // Solo hotel
            $('#visualizar-playa #oper-playa').text(event.oper);
            $('#visualizar-playa #srv-playa').text(event.srv);
            $('#visualizar-playa #observa-playa').text(event.observa);
            $('#visualizar-playa #start-playa').text(event.start.format('DD/MM/YYYY HH:mm:ss'));
            $('#visualizar-playa #end-playa').text(event.end.format('DD/MM/YYYY HH:mm:ss'));
            $('#visualizar-playa').modal('show');

            $('#btnEditar-playa').off('click').on('click', function() {
                $('#edit_id_playa').val(event.id);
                $('#edit_titulo_playa').val(event.hotel); // Solo hotel para editar
                $('#edit_operador_playa').val(event.oper);
                $('#edit_servicio_playa').val(event.srv);
                $('#edit_observa_playa').val(event.observa);
                $('#edit_color_playa').val(event.color);
                $('#edit_inicio_playa').val(event.start.format('DD/MM/YYYY HH:mm:ss'));
                $('#edit_fin_playa').val(event.end.format('DD/MM/YYYY HH:mm:ss'));
                $('#visualizar-playa').modal('hide');
                $('#modificar-playa').modal('show');
            });
            return false;
        },
        selectable: true,
        selectHelper: true,
        select: function(start, end){
            $('#cadastrar-playa #start-playa').val(moment(start).format('DD/MM/YYYY HH:mm:ss'));
            $('#cadastrar-playa #end-playa').val(moment(end).format('DD/MM/YYYY HH:mm:ss'));
            $('#cadastrar-playa').modal('show');
        },
        events: [
            <?php
            while($registros_eventos = mysqli_fetch_array($resultado_eventos)){
                // Limpiar el título (quitar el 0 del inicio si existe)
                $titulo_limpio = ltrim($registros_eventos['titulo'], '0');
                $titulo_limpio = trim($titulo_limpio);
                
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

function DataHoraPlaya(evento, objeto){
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
    <h2 style="color:#E55B26;">Playa del Carmen</h2>
</div>
<div id='calendar-playa'></div>

<!-- Modal Visualizar Evento Playa -->
<div class="modal fade" id="visualizar-playa" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">Información - Playa del Carmen</h4>
            </div>
            <div class="modal-body">
                <dl class="dl-horizontal">
                    <dt>ID</dt><dd id="id-playa"></dd>
                    <dt>Evento</dt><dd id="title-playa"></dd>
                    <dt>Operador</dt><dd id="oper-playa"></dd>
                    <dt>Servicio</dt><dd id="srv-playa"></dd>
                    <dt>Observaciones</dt><dd id="observa-playa"></dd>
                    <dt>Inicio</dt><dd id="start-playa"></dd>
                    <dt>Fin</dt><dd id="end-playa"></dd>
                </dl>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="btnEditar-playa">Editar</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Modificar Playa -->
<div class="modal fade" id="modificar-playa" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form class="form-horizontal" method="POST" action="playa/modificarp.php">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title text-center">Modificar Evento - Playa</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" id="edit_id_playa" name="id">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_titulo_playa" name="titulo" placeholder="Cliente">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Operador</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_operador_playa" name="operador" placeholder="Operador">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Servicio</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_servicio_playa" name="servicio" placeholder="Servicio">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Observaciones</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_observa_playa" name="observa" placeholder="Observaciones">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Color</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_color_playa" name="color" placeholder="Color">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fecha Inicial</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_inicio_playa" name="inicio" onKeyPress="DataHoraPlaya(event, this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fecha Final</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="edit_fin_playa" name="fin" onKeyPress="DataHoraPlaya(event, this)">
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

<!-- Modal Registrar Playa -->
<div class="modal fade" id="cadastrar-playa" tabindex="-1" role="dialog" data-backdrop="static">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title text-center">Registrar Servicios - Playa</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="playa/procesop.php">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Cliente</label>
                        <div class="col-sm-10">
                            <select name="cbxCHotel" class="form-control" id="cbxCHotel-playa" onchange="ShowSelectedPlaya();">
                                <option value="">Seleccione Cliente</option>
                                <?php 
                                $result1 = mysqli_query($conexion2,"SELECT * FROM codhotel ORDER BY hotel");
                                while ($r1 = mysqli_fetch_array($result1)){
                                    echo '<option value="'.$r1[2].'"> '.$r1[2].'</option>';
                                }
                                ?>
                            </select>
                            <script type="text/javascript">
                            function ShowSelectedPlaya(){
                                var codigo=document.getElementById('cbxCHotel-playa').value;
                                document.getElementById('ebcliente-playa').value = codigo;
                            }
                            </script>
                            <input type="text" class="form-control" id="ebcliente-playa" name="titulo" placeholder="Cliente" style="display:none">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Operador</label>
                        <div class="col-sm-10">
                            <select name="operador" class="form-control" id="operador-playa">
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
                    <!-- Resto de campos con IDs únicos -->
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
                            <select name="color" class="form-control" id="color-playa">
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
                            <input type="text" class="form-control" name="inicio" id="start-playa" onKeyPress="DataHoraPlaya(event, this)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">Fecha Final</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" name="fin" id="end-playa" onKeyPress="DataHoraPlaya(event, this)">
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