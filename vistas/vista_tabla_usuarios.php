<?php 
    $url=DIR_SERV."/usuarios";
    $respuesta=consumir_servicios_REST($url,"GET",$_SESSION["api_session"]);
    $obj=json_decode($respuesta);
    if(!$obj)
    {
        consumir_servicios_REST(DIR_SERV."/salir","POST",$_SESSION["api_session"]);
        session_destroy();
        die("<p>Error consumiendo el servicio: ".$url."</p></body></html>");
    }
    if(isset($obj->mensaje_error))
    {
        consumir_servicios_REST(DIR_SERV."/salir","POST",$_SESSION["api_session"]);
        session_destroy();
        die("<p>".$obj->mensaje_error."</p></body></html>");
    }

    if(isset($obj->no_login))
    {
        session_destroy();
        die("<p>El tiempo de sesión de la API ha expirado. Vuelva a <a href='index.php'>loguearse</a>.</p></body></html>");
        
    }
    echo "<h2>Todos los usuarios</h2>";
    echo "<span class='crear'>Añadir Usuario <i class='fa-solid fa-plus' title='Añadir'></i></span>";

    echo "<div class='cartas'>";
    foreach ($obj->usuarios as $restaurante) {
        echo "<div class='card'>";
        echo "<h2>".$restaurante->name."</h2>";
        echo "<p>Ver páginas web del usuario ".$restaurante->name.".</p>";
        echo "<button class='btn-enter'>Entrar</button>";
        echo "<button class='btn-edit'>Editar</button>";
        echo "</div>";
    }
    echo "</div>";

?>
<div id="modalAñadirUsuario" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Añadir Usuario</h2>
        <form id="formAñadirUsuario" method="post">
            <label for="userName">Nombre de Usuario:</label>
            <input type="text" id="userName" name="user_name" required>

            <label for="userPassword">Contraseña:</label>
            <input type="password" id="userPassword" name="user_password" required>

            <label for="userEmail">Email:</label>
            <input type="email" id="userEmail" name="user_email" required>

            <button type="submit" name="btnAñadirUsuario">Añadir Usuario</button>
        </form>
    </div>
</div>
