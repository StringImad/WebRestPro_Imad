<?php
define("DIR_SERV","http://localhost/proyectos/WebRestPro/servicios_rest");
define("MINUTOS", 15);
// ====== CONSTANTES Y CONEXIÓN ======
define("DB_HOST","localhost");
define("DB_NAME","web_rest_pro");
define("DB_USER","root");
define("DB_PASS","");
function consumir_servicios_REST($url, $metodo, $datos=null) {
    $llamada = curl_init();

    if ($metodo=="GET" && $datos!==null) {
        // Si es un string (token), añade como ?api_session=XXX
        if (is_string($datos)) {
            $sep = (strpos($url,'?')===false) ? '?' : '&';
            $url .= $sep."api_session=".urlencode($datos);
            $datos = null;
        } elseif (is_array($datos)) {
            $url .= "?".http_build_query($datos);
            $datos = null;
        }
    }

    curl_setopt($llamada, CURLOPT_URL, $url);
    curl_setopt($llamada, CURLOPT_RETURNTRANSFER, true);

    if ($metodo=="POST") {
        curl_setopt($llamada, CURLOPT_POST, true);
        if ($datos!==null) curl_setopt($llamada, CURLOPT_POSTFIELDS, http_build_query($datos));
    } else if ($metodo=="PUT" || $metodo=="DELETE") {
        curl_setopt($llamada, CURLOPT_CUSTOMREQUEST, $metodo);
        if ($datos!==null) curl_setopt($llamada, CURLOPT_POSTFIELDS, http_build_query($datos));
    }

    $respuesta = curl_exec($llamada);
    curl_close($llamada);
    return $respuesta;
}
function error_page($title,$cabecera,$mensaje)
{
    return '<!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>'.$title.'</title>
    </head>
    <body>
        <h1>'.$cabecera.'</h1><p>'.$mensaje.'</p>
    </body>
    </html>';
}
// Punto base para tu API REST local
// Úsalo si quieres hacer llamadas desde fuera. Internamente no es necesario.

// Devuelve conexión PDO
function db() {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
    }
    return $pdo;
}

// ====== SESIÓN Y ROLES ======
function require_login() {
    if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    if (empty($_SESSION["user_name"])) {
        header("Location: index.php");
        exit;
    }
}

function current_username() {
    return $_SESSION["user_name"] ?? null;
}

// ¿El usuario logueado es admin?
function is_admin() {
    // Comprobamos en BD por si la sesión no guardó el type
    $sql = "SELECT type FROM user WHERE user_name = ?";
    $st = db()->prepare($sql);
    $st->execute([current_username()]);
    $row = $st->fetch();
    return $row && $row["type"] === "admin";
}

// ====== PERMISOS ======
// Comprueba si el restaurante pertenece al usuario actual (o si es admin)
function can_access_restaurant($id_restaurant) {
    if (is_admin()) return true;
    $sql = "SELECT r.id 
            FROM restaurant r 
            JOIN user u ON u.idUser = r.id_user
            WHERE r.id = ? AND u.user_name = ?";
    $st = db()->prepare($sql);
    $st->execute([$id_restaurant, current_username()]);
    return (bool)$st->fetch();
}

// ====== UTIL ======
function json_output($data, int $code = 200) {
    http_response_code($code);
    header("Content-Type: application/json; charset=utf-8");
    echo json_encode($data);
    exit;
}
?>