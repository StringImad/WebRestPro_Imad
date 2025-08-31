<?php
require_once __DIR__ . "/bd_config.php";

/* =======================
   Conexión por función
   ======================= */
function nuevaConexion()
{
    return new PDO(
        "mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD,
        USUARIO_BD,
        CLAVE_BD,
        array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'")
    );
}

/* =======================
   LOGIN / LOGUEADO (CLARO)
   $datos = [user_name, passwd]
   ======================= */
function logueado($datos)
{
    try {
        $cn = nuevaConexion();
        try {
            // ANTIGUO: usuario + contraseña en claro
            $sql = "SELECT * FROM user WHERE user_name=? AND passwd=? LIMIT 1";
            $st = $cn->prepare($sql);
            $st->execute($datos);

            if ($st->rowCount() > 0) {
                $respuesta["user_name"] = $st->fetch(PDO::FETCH_ASSOC);
            } else {
                $respuesta["mensaje"] = "Usuario no registrado en BD";
            }

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

function login($datos)
{
    try {
        $cn = nuevaConexion();
        try {
            // ANTIGUO: usuario + contraseña en claro
            $sql = "SELECT * FROM user WHERE user_name=? AND passwd=? LIMIT 1";
            $st = $cn->prepare($sql);
            $st->execute($datos);

            if ($st->rowCount() > 0) {
                $u = $st->fetch(PDO::FETCH_ASSOC);
                $respuesta["user_name"] = $u;

                // Sesión API antigua
                session_name("api_22_23");
                session_start();
                $_SESSION["user_name"] = $u["user_name"];
                $_SESSION["passwd"]    = $u["passwd"];  // (sí, así estaba antes)
                $_SESSION["type"]      = $u["type"];
                $_SESSION["idUser"]    = $u["idUser"];
                $respuesta["api_session"] = session_id();
            } else {
                $respuesta["mensaje"] = "Usuario no registrado en BD";
            }

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

/* =======================
   PÁGINAS DEL USUARIO
   ======================= */
function obtener_paginas_usuario($id)
{
    try {
        $cn = nuevaConexion();
        try {
            $sql = "SELECT * FROM restaurant WHERE id_user = ?";
            $st = $cn->prepare($sql);
            $st->execute([$id]);

            if ($st->rowCount() > 0)
                $respuesta["paginas_usuario"] = $st->fetchAll(PDO::FETCH_ASSOC);
            else
                $respuesta["mensaje"] = "Usuario sin paginas";

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

/* =======================
   USUARIOS
   ======================= */
function obtener_usuarios()
{
    try {
        $cn = nuevaConexion();
        try {
            $st = $cn->prepare("SELECT * FROM user");
            $st->execute();
            $respuesta["usuarios"] = $st->fetchAll(PDO::FETCH_ASSOC);

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

/**
 * INSERTAR USUARIO (antiguo)
 * $datos = [name, user_name, email, passwd, type]
 */
function insertar_user_name($datos)
{
    try {
        $cn = nuevaConexion();
        try {
            $sql = "INSERT INTO user (`name`, user_name, email, passwd, type) VALUES (?,?,?,?,?)";
            $st = $cn->prepare($sql);
            $st->execute([
                $datos[0] ?: null,           // name
                $datos[1],                   // user_name
                $datos[2] ?: null,           // email
                $datos[3],                   // passwd en claro
                $datos[4] ?? 'normal'        // type
            ]);

            // Mantén solo el resultado de inserción (no abras sesión aquí)
            $respuesta["ok"] = true;
            $respuesta["id"] = $cn->lastInsertId();

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

/**
 * ACTUALIZAR USUARIO (antiguo, sin hash)
 * $datos = [id, name, user_name, email, passwd, type]
 */
function actualizar_usuario($datos)
{
    try {
        $cn = nuevaConexion();
        try {
            $id        = $datos[0];
            $user_name = $datos[1];
            $name      = $datos[2];
            $email     = $datos[3] ?: null;
            $passwd    = $datos[4]; // cadena vacía = no cambiar
            $type      = $datos[5];

            if ($passwd !== "") {
                $sql = "UPDATE user SET `name`=?, user_name=?, email=?, passwd=?, type=? WHERE idUser=?";
                $cn->prepare($sql)->execute([$name ?: null, $user_name, $email, $passwd, $type, $id]);
            } else {
                $sql = "UPDATE user SET `name`=?, user_name=?, email=?, type=? WHERE idUser=?";
                $cn->prepare($sql)->execute([$name ?: null, $user_name, $email, $type, $id]);
            }

            $respuesta["ok"] = true;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

function eliminar_usuario($id)
{
    try {
        $cn = nuevaConexion();
        try {
            $st = $cn->prepare("DELETE FROM user WHERE idUser=?");
            $st->execute([$id]);
            $respuesta["ok"] = true;

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

/* =======================
   Utilidades de usuario
   ======================= */
function obtener_user($columna, $valor)
{
    // Por compatibilidad; si quieres, limita columnas a ['idUser','user_name','email']
    try {
        $cn = nuevaConexion();
        try {
            $sql = "SELECT * FROM user WHERE $columna = ?";
            $st = $cn->prepare($sql);
            $st->execute([$valor]);

            if ($st->rowCount() > 0)
                $respuesta["user"] = $st->fetchAll(PDO::FETCH_ASSOC);
            else
                $respuesta["mensaje"] = "No existe un user con $columna=$valor";

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

function obtener_user_name($id)
{
    try {
        $cn = nuevaConexion();
        try {
            $sql = "SELECT * FROM user WHERE idUser=?"; // corregido (antes id_user)
            $st = $cn->prepare($sql);
            $st->execute([$id]);

            if ($st->rowCount() > 0)
                $respuesta["user_name"] = $st->fetch(PDO::FETCH_ASSOC);
            else
                $respuesta["mensaje"] = "El usuario no se encuentra registrado en la BD";

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

/* =======================
   PÁGINA WEB / PLATOS
   ======================= */
function obtener_pagina($id_restaurant)
{
    try {
        $cn = nuevaConexion();
        try {
            $sql = "SELECT * FROM page_web_content WHERE id_restaurant = ?";
            $st = $cn->prepare($sql);
            $st->execute([$id_restaurant]);

            $respuesta["pagina"] = $st->fetchAll(PDO::FETCH_ASSOC);

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

function obtener_pagina_tipo_comida($id_restaurant, $type_food)
{
    try {
        $cn = nuevaConexion();
        try {
            $sql = "SELECT * FROM page_web_content WHERE id_restaurant = ? AND type_food = ?";
            $st = $cn->prepare($sql);
            $st->execute([$id_restaurant, $type_food]);

            $respuesta["pagina"] = $st->fetchAll(PDO::FETCH_ASSOC);

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}

function insertar_plato($datos)
{
    try {
        $cn = nuevaConexion();
        try {
            $sql = "INSERT INTO page_web_content (plate_name, descrip, allergen, half_price, price, id_restaurant, type_food)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            $st = $cn->prepare($sql);
            $st->execute($datos);

            $respuesta["mensaje"] = "El plato ha sido insertado correctamente";

            $st = null;
            $cn = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}
function obtener_restaurante($id_restaurant)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            $consulta = "SELECT * FROM restaurant WHERE id_restaurant = ?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$id_restaurant]);

            if ($sentencia->rowCount() > 0)
                $respuesta["restaurante"] = $sentencia->fetch(PDO::FETCH_ASSOC);
            else
                $respuesta["mensaje"] = "Restaurante no encontrado";

            $sentencia = null;
            $conexion  = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }
    return $respuesta;
}
