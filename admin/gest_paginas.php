<?php
session_name("22_23");
session_start();

require "../src/funciones_ctes.php";

if(isset($_POST["btnSalir"]))
{
    consumir_servicios_REST(DIR_SERV."/salir","POST",$_SESSION["api_session"]);
    session_destroy();
    header("Location:../index.php");
    exit;

}

if(isset($_SESSION["user_name"]))
{
    $salto="../index.php";
    require "../src/seguridad.php";

    if($datos_usu_log->type=="admin")
    {
        require "../vistas/vista_admin.php";
    }
    else
    {
        header("Location:../principal.php");
        exit;
    }   

}
else
{
    header("Location:../index.php");
    exit;
}