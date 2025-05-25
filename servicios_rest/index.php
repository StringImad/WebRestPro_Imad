<?php

require "src/funciones.php";
require __DIR__ . '/Slim/autoload.php';


$app = new \Slim\App;



$app->post("/salir", function ($request) {
    session_id($request->getParam('api_session'));
    session_start();
    session_destroy();
    echo json_encode(array('logout' => 'Close session'));
});

$app->post('/logueado', function ($request) {

    session_id($request->getParam('api_session'));
    session_start();
    if (isset($_SESSION["type"])) {
        $datos[] = $_SESSION["user_name"];
        $datos[] = $_SESSION["passwd"];
        echo json_encode(logueado($datos));
    } else {
        session_destroy();
        echo json_encode(array('no_login' => 'No logueado'));
    }
});

$app->post('/login', function ($request) {

    $datos[] = $request->getParam("user_name");
    $datos[] = $request->getParam("passwd");

    echo json_encode(login($datos));
});


$app->post('/insertarUsuario', function ($request) {



    $datos[] = $request->getParam('user_name');
    $datos[] = $request->getParam('passwd');
    $datos[] = $request->getParam('email');

    echo json_encode(insertar_user_name($datos));
});

$app->get('/paginas/{id_usuario}', function ($request) {


    echo json_encode(obtener_paginas_usuario($request->getAttribute('id_usuario')));
});
$app->get('/pagina/{id}', function ($request) {


    echo json_encode(obtener_pagina($request->getAttribute('id')));
});
// Endpoint para obtener platos filtrados por ID de restaurante y tipo de comida
$app->get('/paginas/{id}/{type_food}', function ($request) {
    $id_restaurant = $request->getAttribute('id');
    $type_food = $request->getAttribute('type_food');
    echo json_encode(obtener_pagina_tipo_comida($id_restaurant, $type_food));
});

$app->post('/insertarPlato/{id_restaurant}', function ($request) {
    session_id($request->getParam('api_session'));
    session_start();
    var_dump($request);

    $datos[] = $request->getParam("plate_name");
    $datos[] = $request->getParam("descrip");
    $datos[] = $request->getParam("allergen");
    $datos[] = $request->getParam("half_price");
    $datos[] = $request->getParam("price");
    $datos[] = $request->getAttribute("id_restaurant");
    $datos[] = $request->getParam("food_type");


    echo json_encode(insertar_plato($datos));
});

$app->get('/usuarios', function ($request) {

    session_id($request->getParam('api_session'));
    session_start();
    if (isset($_SESSION["type"]) && $_SESSION["type"] == "admin") {
        echo json_encode(obtener_usuarios());
    } else {
        session_destroy();
        echo json_encode(array('no_login' => 'No logueado'));
    }
});









$app->get('/user_name/{id}', function ($request) {

    session_id($request->getParam('api_session'));
    session_start();
    if (isset($_SESSION["type"]) && $_SESSION["type"] == "admin") {
        echo json_encode(obtener_user_name($request->getAttribute('id')));
    } else {
        session_destroy();
        echo json_encode(array('no_login' => 'No logueado'));
    }
});












// Una vez creado servicios los pongo a disposición
$app->run();
