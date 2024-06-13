<?php
require "bd_config.php";


function logueado($datos)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            $consulta = "select * from user where user_name=? and passwd=?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute($datos);

            if ($sentencia->rowCount() > 0) {
                $respuesta["user_name"] = $sentencia->fetch(PDO::FETCH_ASSOC);
            } else
                $respuesta["mensaje"] = "Usuario no registrado en BD";

            $sentencia = null;
            $conexion = null;
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
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            $consulta = "select * from user where user_name=? and passwd=?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute($datos);

            if ($sentencia->rowCount() > 0) {
                $respuesta["user_name"] = $sentencia->fetch(PDO::FETCH_ASSOC);
                session_name("api_22_23");
                session_start();
                $_SESSION["user_name"] = $respuesta["user_name"]["user_name"];
                $_SESSION["passwd"] = $respuesta["user_name"]["passwd"];
                $_SESSION["type"] = $respuesta["user_name"]["type"];
                $respuesta["api_session"] = session_id();
            } else
                $respuesta["mensaje"] = "Usuario no registrado en BD";

            $sentencia = null;
            $conexion = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }

    return $respuesta;
}

function obtener_paginas_usuario($id)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            // Obtener el ID del usuario

            // Consulta para obtener las páginas web del restaurante asociado al usuario
            $consulta = "select * from restaurant WHERE id_user = ?";

            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$id]);
            if ($sentencia->rowCount() > 0) {
                $respuesta["paginas_usuario"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            }else{
                $respuesta["mensaje"] = "Usuario sin paginas";

            }

            $sentencia = null;
            $conexion = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }

    return $respuesta;
}

function obtener_usuarios()
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            $consulta = "select * from user";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute();

            $respuesta["usuarios"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);

            $sentencia = null;
            $conexion = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }

    return $respuesta;
}

function insertar_user_name($datos)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            $consulta = "insert into user (user_name, passwd, email) values(?,?,?)";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute($datos);

            session_name("api_22_23");
            session_start();
            $_SESSION["user_name"] = $datos[0];
            $_SESSION["passwd"] = $datos[1];
            $_SESSION["type"] = "normal";
            $respuesta["api_session"] = session_id();

            $sentencia = null;
            $conexion = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }

    return $respuesta;
}



function obtener_user($columna, $valor)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            $consulta = "select * from user where " . $columna . "=?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$valor]);

            if ($sentencia->rowCount() > 0)
                $respuesta["user"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
            else
                $respuesta["mensaje"] = "No existe un user_name con " . $columna . "=" . $valor;

            $sentencia = null;
            $conexion = null;
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
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            $consulta = "select * from user where id_user=?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$id]);

            if ($sentencia->rowCount() > 0)
                $respuesta["user_name"] = $sentencia->fetch(PDO::FETCH_ASSOC);
            else
                $respuesta["mensaje"] = "El user_name no se encuentra registrado en la BD";


            $sentencia = null;
            $conexion = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }

    return $respuesta;
}
 function obtener_pagina($id)
 {
     try {
         $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
         try {
             $consulta = "select * from page_web_content where id_restaurant = ?";
             $sentencia = $conexion->prepare($consulta);
             $sentencia->execute([$id]);

                 $respuesta["pagina"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);


             $sentencia = null;
             $conexion = null;
         } catch (PDOException $e) {
             $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
         }
     } catch (PDOException $e) {
         $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
     }

     return $respuesta;
 }

function obtener_pagina_tipo_comida($id,$type_food)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            $consulta = "select * from page_web_content where id_restaurant = ? and type_food = ?";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute([$id,$type_food]);

//             // if ($sentencia->rowCount() > 0)
                $respuesta["pagina"] = $sentencia->fetchAll(PDO::FETCH_ASSOC);
//             // else
//                 // $respuesta["mensaje"] = "La pagina no se encuentra registrada en la BD";


             $sentencia = null;
             $conexion = null;
         } catch (PDOException $e) {
             $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
         }
     } catch (PDOException $e) {
         $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
     }

     return $respuesta;
 }



// function obtener_pagina($id) {
//     global $conn;
//     $sql = "SELECT * FROM page_web_content WHERE id_restaurant = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("i", $id);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $dishes = array();
//     while ($row = $result->fetch_assoc()) {
//         $dishes[] = $row;
//     }
//     return $dishes;
// }

// function obtener_pagina_tipo_comida($id_restaurant, $type_food) {
//     global $conn;
//     $sql = "SELECT * FROM page_web_content WHERE id_restaurant = ? AND food_type = ?";
//     $stmt = $conn->prepare($sql);
//     $stmt->bind_param("is", $id_restaurant, $type_food);
//     $stmt->execute();
//     $result = $stmt->get_result();
//     $dishes = array();
//     while ($row = $result->fetch_assoc()) {
//         $dishes[] = $row;
//     }
//     return $dishes;
// }
function insertar_plato($datos)
{
    try {
        $conexion = new PDO("mysql:host=" . SERVIDOR_BD . ";dbname=" . NOMBRE_BD, USUARIO_BD, CLAVE_BD, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'"));
        try {
            $consulta = "INSERT INTO page_web_content (plate_name, descrip, allergen, half_price, price, id_restaurant, food_type) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $sentencia = $conexion->prepare($consulta);
            $sentencia->execute($datos);



            $respuesta["mensaje"] = "El plato ha sido insertado correctamente";


            $sentencia = null;
            $conexion = null;
        } catch (PDOException $e) {
            $respuesta["mensaje_error"] = "Imposible realizar la consulta. Error:" . $e->getMessage();
        }
    } catch (PDOException $e) {
        $respuesta["mensaje_error"] = "Imposible conectar a la BD. Error:" . $e->getMessage();
    }

    return $respuesta;
}

