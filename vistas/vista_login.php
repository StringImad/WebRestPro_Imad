<?php
if (isset($_POST["btnLogin"])) {
    $error_user_name = $_POST["user_name"] == "";
    $error_passwd = $_POST["passwd"] == "";
    if (!$error_user_name && !$error_passwd) {
        $url = DIR_SERV . "/login";
        $datos_env["user_name"] = $_POST["user_name"];
        //$datos_env["passwd"]=md5($_POST["passwd"]);
        $datos_env["passwd"] = $_POST["passwd"];

        $respuesta = consumir_servicios_REST($url, "POST", $datos_env);
        $obj = json_decode($respuesta);
        if (!$obj) {
            session_destroy();
            die(error_page("WebRestPro", "WebRestPro", "Error consumiendo el servicio: " . $url . $respuesta));
        }

        if (isset($obj->mensaje_error)) {
            session_destroy();
            die(error_page("WebRestPro", "WebRestPro", $obj->mensaje_error));
        }
        if (isset($obj->mensaje))
            $error_user_name = true;
        else {
            $_SESSION["user_name"] = $datos_env["user_name"];
            $_SESSION["passwd"] = $datos_env["passwd"];
            $_SESSION["ultima_accion"] = time();
            $_SESSION["api_session"]["api_session"] = $obj->api_session;

            if ($obj->user_name->type == "admin")
                header("Location:admin/gest_paginas.php");
            else
                header("Location:principal.php");

            exit;
        }
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200;500;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous" />
    <link rel="stylesheet" href="styles/main.css" />
    <link rel="stylesheet" href="styles/normalize.css" />
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="js/script.js" type="text/javascript"></script>

    <title>Login</title>
</head>

<body>
    <main class="login-design">
        <div class="waves">
            <img src="assets/loginPerson.png" alt="" />
        </div>
        <div class="login">
            <div class="login-data">
                <img src="assets/collab.png" alt="" />
                <h1>Inicio de Sesión</h1>
                <form action="index.php" method="post" class="login-form">
                    <div class="input-group">

                        <label class="input-fill">
                            <input type="text" name="user_name" id="user_name" value="<?php if (isset($_POST["user_name"])) echo $_POST["user_name"]; ?>" />
                            <span class="input-label">Usuario</span>
                            <i class="fas fa-envelope"></i>
                        </label>
                        <?php
                        if (isset($_POST["btnLogin"]) && $error_user_name) {
                            if ($_POST["user_name"] == "")
                                echo "<span class='error'>Campo Vacío</span>";
                            else
                                echo "<span class='error'>Usuario y/o Contraseña incorrectos</span>";
                        }
                        ?>
                    </div>
                    <div class="input-group">
                        <label class="input-fill">
                            <input type="password" name="passwd" id="passwd" required />
                            <span class="input-label">Contraseña</span>
                            <i class="fas fa-lock"></i>
                        </label>
                        <?php
                        if (isset($_POST["btnLogin"]) && $error_passwd)
                            echo "<span class='error'>Campo Vacío</span>";
                        ?>
                    </div>

                    <p><button class="btn-logins" name="btnLogin">Entrar</button> <button class="btn-logins" name="btnRegistro">Registro</button></p>
                </form>
                <?php
                if (isset($_SESSION["seguridad"])) {
                    echo "<p class='mensaje'>" . $_SESSION["seguridad"] . "</p>";
                    unset($_SESSION["seguridad"]);
                }

                //require "vistas/vista_paginas.php";

                ?>
</body>

</html>