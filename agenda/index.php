<!doctype html>
<?php session_start(); ?>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Agenda Operativa</title>
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css" rel="stylesheet" />
    <link href="https://siwo-net.com/ocore/ooper/gesoper/agenda/css/personalizado.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-default">
        <div class="container">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <span class="navbar-brand">
                    <img src="images/LOGOCWOB.png" id="logohead" alt="" style="height:50px;">
                </span>
            </div>
            <div class="collapse navbar-collapse" id="navbar-collapse-1">
                <ul class="nav navbar-nav"></ul>
                <ul class="nav navbar-nav navbar-right">
                    <li>
                        <a class="btn btn-danger navbar-btn" href="https://www.cleanworkorangemx.com/cwo/dashboard.php">Salir</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <center><h1>Agenda Operativa</h1></center>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="cancun"></div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div id="playa"></div>
            </div>
        </div>
    </div>

    <!-- Scripts al final -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/es.js"></script>
    <script>
    $(document).ready(function() {
        // Cargar agendas después de que todo esté listo
        setTimeout(function() {
            $('#cancun').load('cancun/cancun.php');
            $('#playa').load('playa/playa.php');
        }, 100);
    });
    </script>
</body>
</html>