<?php
session_start();
  unset($_SESSION['consulta']);
  $level=$_GET['c'];
  $_SESSION['level']=$level;
   
  $username = $_GET['b']; 
  $afiliado = $_GET['a'];
  $_SESSION['afiliado']=$afiliado;
  $mysqli = new mysqli('localhost', 'u826340212_orangedb','Cwo9982061148', 'u826340212_orangedb');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Asignación de Operadores</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        :root {
            --naranja-cwo: #E55B26;
            --naranja-cwo-dark: #c44a1f;
            --naranja-cwo-light: #f27c4d;
        }
        body { background: #fff8f5; }
        .navbar { background-color: var(--naranja-cwo) !important; box-shadow: 0 2px 8px rgba(229,91,38,.08);}
        .navbar-brand, .navbar-nav .nav-link { color: #fff !important; font-weight: bold;}
        .btn-cwo { background-color: var(--naranja-cwo); color: #fff; border: none; font-weight: 500; transition: background 0.2s;}
        .btn-cwo:hover, .btn-cwo:focus { background-color: var(--naranja-cwo-dark); color: #fff;}
        .form-control:focus { border-color: var(--naranja-cwo); box-shadow: 0 0 0 0.2rem rgba(229,91,38,.25);}
        .modal-content { border-radius: 12px; box-shadow: 0 4px 16px rgba(229,91,38,.10);}
        h1, h4 { color: var(--naranja-cwo); font-weight: 700;}
        .accent-bg { background: var(--naranja-cwo-light); color: #fff; border-radius: 8px; padding: 1em; margin-bottom: 2em;}
        .navbar-logo { height: 40px;}
        .form-label { font-weight: 500;}
        .btn-secondary-cwo {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            border-radius: 0.375rem;
            border: 1px solid transparent;
            cursor: pointer;
            transition: all 0.15s ease-in-out;
        }
        
        .btn-secondary-cwo:hover {
            background-color: #5c636a;
            border-color: #565e64;
            color: #fff;
        }
        
        .btn-secondary-cwo:focus {
            box-shadow: 0 0 0 0.2rem rgba(108, 117, 125, 0.5);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="#">
                <img src="images/LOGOCWOB.png" alt="Logo" class="navbar-logo"> Asignación de Operadores
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon" style="color:#fff;"></span>
            </button>
            <div style="color:white">Usuario: <?php echo $username ;?> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Nivel: <?php echo $level; ?></div>
            <div class="collapse navbar-collapse" id="navbarNav">
                <button class="btn btn-cwo ms-auto" onclick="window.location.href='https://www.cleanworkorangemx.com/cwo/dashboard.php'">Salir</button>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="text-center mt-4">
            <button class="btn btn-cwo" data-bs-toggle="modal" data-bs-target="#modalNuevo">Agregar operador</button>
            <button class="btn btn-secondary-cwo" id="toggleEliminados" title="Ver operadores eliminados">
                Ver eliminados
            </button>
        </div>
        <script>
        $(document).ready(function(){
            // Cargar buscador
            $('#buscador').load('componentes/buscador.php');
            
            // Cargar tabla
            cargarTabla();
        });
        </script>
        <div id="buscador"></div>
        <div id="tabla"></div>
        
        <!-- Botón Agregar Operador -->
        
    </div>

    <!-- Modal para agregar operador -->
    <div class="modal fade" id="modalNuevo" tabindex="-1" aria-labelledby="modalNuevoLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="modalNuevoLabel">Agregar operador</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form id="formNuevo">
                <label class="form-label">Afiliado</label>
                <input type="text" id="afiliado" class="form-control mb-2">
                <label class="form-label">Nombre</label>
                <input type="text" id="nombre" class="form-control mb-2">
                <label class="form-label">Código</label>
                <input type="text" id="codigo" class="form-control mb-2">
                <label class="form-label">Teléfono</label>
                <input type="text" id="telefono" class="form-control mb-2">
                <label class="form-label">Departamento</label>
                <input type="text" id="departamento" class="form-control mb-2">
                
                <!-- Centro de trabajo (solo para CWO) -->
                <label class="form-label" id="lbl-centro" style="display:none;">Centro de Trabajo</label>
                <select id="centro" class="form-control mb-2" style="display:none;">
                    <option value="">Seleccione:</option>
                    <option value="Playa">Playa</option>
                    <option value="Cancun">Cancún</option>
                    <option value="Xcaret">Xcaret</option>
                </select>
                
                <label class="form-label">Asignación</label>
                <select id="asignacion" class="form-control mb-2">
                    <option value="0">Seleccione:</option>
                    <?php
                      $query = $mysqli->query("SELECT * FROM codhotel WHERE sabana='S' ORDER by hotel");
                      while ($valores = mysqli_fetch_array($query)) {
                        echo '<option value="'.$valores['codhotel'].'">'.$valores['hotel'].'</option>';
                      }
                    ?>
                </select>
                
                <label class="form-label">Contrato</label>
                <select id="contrato" class="form-control mb-2">
                    <option value="0">Seleccione:</option>
                </select>
                
                <label class="form-label">Categoria</label>
                <input type="text" id="categoria" class="form-control mb-2">
                
                <label class="form-label">Equipo</label>
                <input type="text" id="equipo" class="form-control mb-2">
                
                <button type="button" class="btn btn-cwo w-100 mt-2" id="guardarnuevo">Agregar operador</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de edición operador -->
    <div class="modal fade" id="modalEdicion" tabindex="-1" aria-labelledby="modalEdicionLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="modalEdicionLabel">Editar operador</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <form id="formEdicion">
                <input type="hidden" id="id_ed">
                <label class="form-label">Afiliado</label>
                <input type="text" id="afiliado_ed" class="form-control mb-2">
                <label class="form-label">Nombre</label>
                <input type="text" id="nombre_ed" class="form-control mb-2">
                <label class="form-label">Código</label>
                <input type="text" id="codigo_ed" class="form-control mb-2">
                <label class="form-label">Teléfono</label>
                <input type="text" id="telefono_ed" class="form-control mb-2">
                <label class="form-label">Departamento</label>
                <input type="text" id="departamento_ed" class="form-control mb-2">
                <!-- Asignación como SELECT igual que en agregar -->
                <label class="form-label">Asignación</label>
                <select id="asignacion_ed" class="form-control mb-2">
                    <option value="0">Seleccione:</option>
                    <?php
                      $query = $mysqli->query("SELECT * FROM codhotel WHERE sabana='S' ORDER by hotel");
                      while ($valores = mysqli_fetch_array($query)) {
                        echo '<option value="'.$valores['codhotel'].'">'.$valores['hotel'].'</option>';
                      }
                    ?>
                </select>
                <!-- Contrato como SELECT igual que en agregar -->
                <label class="form-label">Contrato</label>
                <select id="contrato_ed" class="form-control mb-2">
                    <option value="0">Seleccione:</option>
                </select>
                <label class="form-label">Categoria</label>
                <input type="text" id="categoria_ed" class="form-control mb-2">
                <label class="form-label">Equipo</label>
                <input type="text" id="equipo_ed" class="form-control mb-2">
                <label class="form-label">Centro</label>
                <input type="text" id="centro_ed" class="form-control mb-2">
                <button type="button" class="btn btn-cwo w-100 mt-2" id="guardarEdicion">Guardar cambios</button>
            </form>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal moderno de confirmación de eliminación -->
    <div class="modal fade" id="modalEliminar" tabindex="-1" aria-labelledby="modalEliminarLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title" id="modalEliminarLabel">Confirmar eliminación</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
          </div>
          <div class="modal-body">
            <p>¿Seguro que deseas eliminar este operador?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
            <button type="button" class="btn btn-danger" id="eliminarConfirmado">Eliminar</button>
          </div>
        </div>
      </div>
    </div>

    <!-- jQuery PRIMERO -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Select2 JS DESPUÉS de jQuery -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        var operadorEliminarId = null;
        var viendoEliminados = false;

        // Cargar contenido dinámico
        $(document).ready(function(){
            $('#tabla').load('componentes/tabla.php');
            $('#buscador').load('componentes/buscador.php');
        });
        
        // Función para cargar tabla según el estado
        function cargarTabla() {
            if (viendoEliminados) {
                $('#tabla').load('componentes/tabla_eliminados.php');
            } else {
                $('#tabla').load('componentes/tabla.php');
            }
        }

        // Toggle entre operadores activos y eliminados
        $('#toggleEliminados').click(function() {
            viendoEliminados = !viendoEliminados;
            if (viendoEliminados) {
                $(this).text('Ver activos').removeClass('btn-secondary-cwo').addClass('btn-warning');
            } else {
                $(this).text('Ver eliminados').removeClass('btn-warning').addClass('btn-secondary-cwo');
            }
            cargarTabla();
        });
        
        // Mostrar/ocultar Centro de Trabajo para nuevos operadores
        $('#afiliado').on('input', function() {
            var valor = this.value.trim().toUpperCase();
            if (valor === 'CWO') {
                $('#centro').show();
                $('#lbl-centro').show();
            } else {
                $('#centro').hide();
                $('#lbl-centro').hide();
                $('#centro').val('');
            }
        });

        // Cargar contratos según asignación para nuevos operadores
        $('#asignacion').change(function() {
            $('#contrato').find('option').remove().end().append('<option value="0">Seleccione:</option>').val('0');
            var elegido = $(this).val();
            if (elegido != '0') {
                $.post("php/modelos.php", { elegido: elegido }, function(data){
                    $("#contrato").html(data);
                });
            }
        });

        // Agregar nuevo operador
        $('#guardarnuevo').click(function(){
            var datos = {
                afiliado: $('#afiliado').val(),
                nombre: $('#nombre').val(),
                codigo: $('#codigo').val(),
                telefono: $('#telefono').val(),
                departamento: $('#departamento').val(),
                centro: $('#centro').is(':visible') ? $('#centro').val() : '',
                asignacion: $('#asignacion').val(),
                contrato: $('#contrato').val(),
                categoria: $('#categoria').val(),
                equipo: $('#equipo').val()
            };
            $.ajax({
                type: "POST",
                url: "php/agregarDatos.php",
                data: datos,
                success: function(r){
                    if(r == 1){
                        $('#tabla').load('componentes/tabla.php');
                        $('#formNuevo')[0].reset();
                        $('#centro').hide();
                        $('#lbl-centro').hide();
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalNuevo'));
                        modal.hide();
                    } else {
                        alert('Error al agregar el operador');
                    }
                }
            });
        });

        // Llenar el modal de edición
        function agregaform(datos) {
            var d = datos.split('||');
            $('#id_ed').val(d[0]);
            $('#afiliado_ed').val(d[1]);
            $('#nombre_ed').val(d[2]);
            $('#codigo_ed').val(d[3]);
            $('#telefono_ed').val(d[4]);
            $('#departamento_ed').val(d[5]);
            $('#asignacion_ed').val(d[6]);
            $('#contrato_ed').val(d[7]);
            $('#categoria_ed').val(d[8]);
            $('#equipo_ed').val(d[9]);
            $('#centro_ed').val(d[10]);
        }

        // Guardar edición vía AJAX
        $('#guardarEdicion').click(function(){
            var datos = {
                id: $('#id_ed').val(),
                afiliado: $('#afiliado_ed').val(),
                nombre: $('#nombre_ed').val(),
                codigo: $('#codigo_ed').val(),
                telefono: $('#telefono_ed').val(),
                departamento: $('#departamento_ed').val(),
                asignacion: $('#asignacion_ed').val(),
                contrato: $('#contrato_ed').val(),
                categoria: $('#categoria_ed').val(),
                equipo: $('#equipo_ed').val(),
                centro: $('#centro_ed').val()
            };
            $.ajax({
                type: "POST",
                url: "php/actualizaDatos.php",
                data: datos,
                success: function(r){
                    if(r == 1){
                        $('#tabla').load('componentes/tabla.php');
                        var modal = bootstrap.Modal.getInstance(document.getElementById('modalEdicion'));
                        modal.hide();
                    } else {
                        alert('Error al actualizar el operador');
                    }
                }
            });
        });

        // Abrir el modal de confirmación al eliminar
        function preguntarSiNo(id){
            operadorEliminarId = id;
            var modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
            modal.show();
        }

        // Confirmar eliminación vía AJAX
        $('#eliminarConfirmado').click(function(){
            if(operadorEliminarId){
                $.ajax({
                    type: "POST",
                    url: "php/eliminarDatos.php",
                    data: {id: operadorEliminarId},
                    success: function(r){
                        if(r == 1){
                            $('#tabla').load('componentes/tabla.php');
                            var modal = bootstrap.Modal.getInstance(document.getElementById('modalEliminar'));
                            modal.hide();
                        } else {
                            alert('Error al eliminar el operador');
                        }
                    }
                });
            }
        });
        
         // Función para restaurar operador eliminado
        function restaurarOperador(id) {
            $.ajax({
                type: "POST",
                url: "php/restaura_operador.php",
                data: {id: id},
                success: function(r){
                    if(r == 1){
                        cargarTabla();
                    } else {
                        alert('Error al restaurar el operador');
                    }
                }
            });
        }
    </script>
</body>
</html>