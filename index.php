<?php
session_name("22_23");
session_start();

require "src/funciones_ctes.php";


if(isset($_SESSION["user_name"]))
{
    header("Location:principal.php");
    exit();
}
else
{
    require "vistas/vista_login.php";
}

?>