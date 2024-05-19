<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webrestpro</title>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200;500;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous" />
    <link rel="stylesheet" href="../styles/main.css" />
    <link rel="stylesheet" href="../styles/normalize.css" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link re
</head>

<body>
<header>
    <h1>WebRestPro</h1>
    <div class="usuario-logout">
        <span class="nombre-usuario"><i class="fa-solid fa-user"></i> <?php echo $datos_usu_log->user_name; ?></span>
        <form action="gest_paginas.php" method="post">
            <button name="btnSalir" class="boton-salir"><i class="fa-solid fa-right-from-bracket"></i> Salir</button>
        </form>
    </div>
</header>

    

    <?php

    require "../vistas/vista_tabla_usuarios.php";

    if (isset($_SESSION["accion"])) {
        echo "<p class='mensaje'>" . $_SESSION["accion"] . "</p>";
        unset($_SESSION["accion"]);
    }
    ?>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="js/script.js"></script>
</body>

</html>