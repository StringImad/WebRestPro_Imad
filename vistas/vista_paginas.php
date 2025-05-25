<?php

if(isset($_POST["btnEditarPagina"]))
{
    require "vistas/vista_ver_platos.php";
}
else
{
    

    $url=DIR_SERV."/paginas/".$datos_usu_log->idUser;
    $respuesta=consumir_servicios_REST($url,"GET");
    $obj=json_decode($respuesta);
    if(!$obj)
    {
        session_destroy();
        die("<p>Error consumiendo el servicio: ".$url."</p></body></html>");
    }
    if(isset($obj->mensaje_error))
    {
        session_destroy();
        die("<p>".$obj->mensaje_error."</p></body></html>");
    }
    echo "<pre>";
    print_r($datos_usu_log);
    echo "</pre>";
    echo "<div class='cartas-restaurantes'>";
   

    foreach ($obj->paginas_usuario as $tupla) {
        echo "<div class='carta'>";
        echo "<h3>{$tupla->name}</h3>";
        echo "<div class='carta-body'>";
        echo "<a class='enlace-grande' href='https://www.marabierta.com'>Ver</a>";
        echo "<form method='post'>";
        echo "<button class='enlace-grande' name='btnEditarPagina' value='{$tupla->id}'>Editar</button>";
        echo "</form>";
        echo "</div>";
        echo "</div>";
    }
    echo "</div>";
    
}
?>