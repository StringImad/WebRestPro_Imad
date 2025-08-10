<?php
// Establece un nombre para la sesión 
session_name("22_23");

// Inicia la sesión PHP para poder acceder a variables de sesión
session_start();

// Incluye el archivo donde están las funciones y constantes (conexión a BD, helpers, etc.)
require "src/funciones_ctes.php";

// Verifica si el usuario ya ha iniciado sesión previamente
if(isset($_SESSION["user_name"]))
{
    // Si existe la variable de sesión, redirige directamente a la página principal
    header("Location: principal.php");
    exit(); // Finaliza la ejecución del script para evitar seguir cargando el login
}
else
{
    // Si no hay sesión iniciada, carga la vista de login para que el usuario introduzca sus datos
    require "vistas/vista_login.php";
}
?>