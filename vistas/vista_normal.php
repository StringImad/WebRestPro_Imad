<?php

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Expires" content="0">
    <meta http-equiv="Last-Modified" content="0">
    <meta http-equiv="Cache-Control" content="no-cache, mustrevalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Webrestpro</title>

    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="preconnect" href="https://fonts.gstatic.com" />
    <link href="https://fonts.googleapis.com/css2?family=Dosis:wght@200;500;700;800&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.1/css/all.css" integrity="sha384-vp86vTRFVJgpjF9jiIGPEEqYqlDwgyBgEF109VFjmqGmIY/Y4HV4d3Gp2irVfcrp" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="styles/main.css" />
    <link rel="stylesheet" href="styles/normalize.css" />


    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="js/script.js" />

    <script>
        $(document).ready(function() {
            $(".opcion-menu-mov").click(function(event) {
                event.preventDefault();

                // Oculta todas las páginas de platos
                $(".plato").hide();

                // Obtiene el tipo de comida seleccionado
                console.log($(this).attr("href").substring(1));
                var foodType = $(this).attr("href").substring(1);

                // Muestra solo las páginas de platos con el tipo de comida seleccionado
                $("." + foodType).show();
            });

            var modal = $("#modalAñadirPlato");


            $(".crear>i").click(function() {
                modal.show();
            });

            $(".close").click(function() {
                modal.hide();
            });

            $(window).click(function(event) {
                if ($(event.target).is(modal)) {
                    modal.hide();
                }
            });
            $('.menu-toggle').click(function() {
                $(this).toggleClass('active');
                $('#op-menu-comida').toggleClass('active');
            });

            // Cerrar el menú cuando se hace clic en un enlace (opcional)
            $('#op-menu-comida a').click(function() {
                $('.menu-toggle').removeClass('active');
                $('#op-menu-comida').removeClass('active');
            });

            // Cerrar el menú al hacer clic fuera de él (opcional)
            $(document).click(function(e) {
                var target = e.target;
                if (!$(target).is('.menu-toggle') && !$(target).parents().is('.menu-toggle') && $('#op-menu-comida').hasClass('active')) {
                    $('.menu-toggle').removeClass('active');
                    $('#op-menu-comida').removeClass('active');
                }
            });
            $('.editar-icon').click(function() {
                var idPlato = $(this).data('id');
                var contenedorPlato = $(this).closest('.plato');

                // Rellenando los datos en el formulario
                $('#editarIdPlato').val(idPlato);
                $('#editarPlateName').val(contenedorPlato.find('h2').text().replace('Nombre Plato: ', ''));
                $('#editarDescripcion').val(contenedorPlato.find('p strong').text());
                $('#editarPrecio').val(contenedorPlato.find('p').eq(1).text().replace('Precio: ', ''));
                $('#editarHalfPrice').val(contenedorPlato.find('p').eq(2).text().replace('Precio media ración: ', ''));
                $('#editarFoodType').val(contenedorPlato.find('p').eq(4).text().replace('Tipo: ', ''));
                $('#editarAlergenos').val(contenedorPlato.find('p').eq(3).text().replace('Alergeno: ', ''));

                $('#modalEditar').show();
            });



            $('.borrar-icon').click(function() {
                var idPlato = $(this).data('id');
                // Puedes guardar el id en un atributo del botón de confirmación
                $('#confirmarBorrar').data('id', idPlato);
                $('#modalBorrar').show();
            });

            $('.close').click(function() {
                $('.modal').hide();
            });

            $('#confirmarBorrar').click(function() {
                var idPlato = $(this).data('id');
                // Aquí lógica para borrar el plato, como una solicitud AJAX
                $('.modal').hide();
            });

            $("#op-menu-comida>.opcion-menu").click(function() {
                var id = $(this).attr("id");

                console.log("Mostrando id: "+id+"s")

            });

      
});
window.onscroll = function() {
    scrollFunction();
};

function scrollFunction() {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        document.getElementById("scrollTopBtn").style.display = "block";
    } else {
        document.getElementById("scrollTopBtn").style.display = "none";
    }
}

// Función para deslizar hacia arriba
document.getElementById("scrollTopBtn").onclick = function() {
    scrollToTop();
};

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth' // Deslizamiento suave
    });
}
    </script>
</head>

<body>
    <!-- <script src="js/script.js"></script> -->

    <header>
        <h1>WebRestPro</h1>
        <div class="usuario-logout">
            <span class="nombre-usuario"><i class="fa-solid fa-user"></i> <?php echo $datos_usu_log->user_name; ?></span>
            <form action="principal.php" method="post">
                <button name="btnSalir" class="boton-salir"><i class="fa-solid fa-right-from-bracket"></i> Salir</button>
            </form>
        </div>
    </header>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- <script src="js/script.js"></script> -->
    <?php
    require "vistas/vista_paginas.php";
    ?>
</body>

</html>