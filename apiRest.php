<?php
session_name("22_23");
session_start();
require_once __DIR__ . "/src/funciones_ctes.php";
require_login();

// Ruteo simple por método + parámetros
$method = $_SERVER["REQUEST_METHOD"];
$resource = $_GET["resource"] ?? ""; // "platos" | "restaurantes"

try {
    if ($resource === "users") {
    // Solo el admin puede listar/crear usuarios
    if (!is_admin()) json_output(["error"=>"Sin permiso"], 403);

    if ($method === "GET") {
        // Lista básica de usuarios
        $sql = "SELECT idUser, user_name, email, type FROM user ORDER BY type DESC, user_name";
        $rows = db()->query($sql)->fetchAll();
        json_output(["data"=>$rows]);
    }

    if ($method === "POST") {
        // Crear usuario nuevo
        $payload = json_decode(file_get_contents("php://input"), true);
        if (!$payload) json_output(["error"=>"JSON inválido"], 400);

        $user_name = trim($payload["user_name"] ?? "");
        $email     = trim($payload["email"] ?? "");
        $passwd    = trim($payload["passwd"] ?? "");
        $type      = trim($payload["type"] ?? "normal"); // 'admin' | 'normal'

        if ($user_name === "" || $passwd === "") {
            json_output(["error"=>"Usuario y contraseña son obligatorios"], 422);
        }
        if (!in_array($type, ["admin","normal"], true)) {
            json_output(["error"=>"Tipo de usuario inválido"], 422);
        }

        // ¿existe ya ese user_name?
        $st = db()->prepare("SELECT 1 FROM user WHERE user_name=?");
        $st->execute([$user_name]);
        if ($st->fetch()) {
            json_output(["error"=>"El nombre de usuario ya existe"], 409);
        }

        // Inserta (MVP: contraseña sin hash para no romper login actual)
        $st = db()->prepare("INSERT INTO user (user_name, email, passwd, type) VALUES (?,?,?,?)");
        $st->execute([$user_name, $email ?: null, $passwd, $type]);

        json_output(["ok"=>true, "idUser"=>db()->lastInsertId()], 201);
    }

    json_output(["error"=>"Método no permitido"], 405);
}

    if ($resource === "restaurantes") {
        // GET /apiRest.php?resource=restaurantes
        if ($method !== "GET") json_output(["error"=>"Método no permitido"], 405);

        if (is_admin()) {
            $sql = "SELECT r.id, r.name FROM restaurant r ORDER BY r.name";
            $rows = db()->query($sql)->fetchAll();
        } else {
            $sql = "SELECT r.id, r.name 
                    FROM restaurant r 
                    JOIN user u ON u.idUser = r.id_user
                    WHERE u.user_name = ?
                    ORDER BY r.name";
            $st = db()->prepare($sql);
            $st->execute([current_username()]);
            $rows = $st->fetchAll();
        }
        json_output(["data"=>$rows]);
    }

    if ($resource === "platos") {
        if ($method === "GET") {
            // GET /apiRest.php?resource=platos&id_restaurant=1
            $id_rest = (int)($_GET["id_restaurant"] ?? 0);
            if ($id_rest <= 0) json_output(["error"=>"id_restaurant requerido"], 400);
            if (!can_access_restaurant($id_rest)) json_output(["error"=>"Sin permiso"], 403);

            $sql = "SELECT id, plate_name, descrip, allergen, half_price, price, food_type
                    FROM page_web_content
                    WHERE id_restaurant = ?
                    ORDER BY food_type, plate_name";
            $st = db()->prepare($sql);
            $st->execute([$id_rest]);
            json_output(["data"=>$st->fetchAll()]);
        }

        if ($method === "POST") {
            // POST JSON: {plate_name, descrip, allergen, half_price, price, food_type, id_restaurant}
            $payload = json_decode(file_get_contents("php://input"), true);
            if (!$payload) json_output(["error"=>"JSON inválido"], 400);

            $id_rest = (int)($payload["id_restaurant"] ?? 0);
            if ($id_rest <= 0) json_output(["error"=>"id_restaurant requerido"], 400);
            if (!can_access_restaurant($id_rest)) json_output(["error"=>"Sin permiso"], 403);

            $sql = "INSERT INTO page_web_content
                    (plate_name, descrip, allergen, half_price, price, id_restaurant, food_type)
                    VALUES (:plate_name, :descrip, :allergen, :half_price, :price, :id_restaurant, :food_type)";
            $st = db()->prepare($sql);
            $st->execute([
                ":plate_name"   => trim($payload["plate_name"] ?? ""),
                ":descrip"      => trim($payload["descrip"] ?? ""),
                ":allergen"     => trim($payload["allergen"] ?? ""),
                ":half_price"   => trim($payload["half_price"] ?? ""),
                ":price"        => trim($payload["price"] ?? ""),
                ":id_restaurant"=> $id_rest,
                ":food_type"    => trim($payload["food_type"] ?? "")
            ]);
            json_output(["ok"=>true, "id"=>$GLOBALS['pdo_last_id'] ?? db()->lastInsertId()], 201);
        }

        if ($method === "PUT") {
            // PUT /apiRest.php?resource=platos&id=XX
            $id = (int)($_GET["id"] ?? 0);
            if ($id <= 0) json_output(["error"=>"id requerido"], 400);

            // Validamos restaurante propietario del plato
            $sqlOwn = "SELECT id_restaurant FROM page_web_content WHERE id = ?";
            $stOwn = db()->prepare($sqlOwn);
            $stOwn->execute([$id]);
            $row = $stOwn->fetch();
            if (!$row) json_output(["error"=>"Plato no existe"], 404);
            if (!can_access_restaurant((int)$row["id_restaurant"])) json_output(["error"=>"Sin permiso"], 403);

            $payload = json_decode(file_get_contents("php://input"), true);
            if (!$payload) json_output(["error"=>"JSON inválido"], 400);

            $sql = "UPDATE page_web_content
                    SET plate_name=:plate_name, descrip=:descrip, allergen=:allergen,
                        half_price=:half_price, price=:price, food_type=:food_type
                    WHERE id=:id";
            $st = db()->prepare($sql);
            $st->execute([
                ":plate_name" => trim($payload["plate_name"] ?? ""),
                ":descrip"    => trim($payload["descrip"] ?? ""),
                ":allergen"   => trim($payload["allergen"] ?? ""),
                ":half_price" => trim($payload["half_price"] ?? ""),
                ":price"      => trim($payload["price"] ?? ""),
                ":food_type"  => trim($payload["food_type"] ?? ""),
                ":id"         => $id
            ]);
            json_output(["ok"=>true]);
        }

        if ($method === "DELETE") {
            // DELETE /apiRest.php?resource=platos&id=XX
            $id = (int)($_GET["id"] ?? 0);
            if ($id <= 0) json_output(["error"=>"id requerido"], 400);

            $sqlOwn = "SELECT id_restaurant FROM page_web_content WHERE id = ?";
            $stOwn = db()->prepare($sqlOwn);
            $stOwn->execute([$id]);
            $row = $stOwn->fetch();
            if (!$row) json_output(["error"=>"Plato no existe"], 404);
            if (!can_access_restaurant((int)$row["id_restaurant"])) json_output(["error"=>"Sin permiso"], 403);

            $st = db()->prepare("DELETE FROM page_web_content WHERE id = ?");
            $st->execute([$id]);
            json_output(["ok"=>true]);
        }

        json_output(["error"=>"Método no permitido"], 405);
    }

    json_output(["error"=>"Recurso no encontrado"], 404);

} catch (Throwable $e) {
    json_output(["error"=>"Excepción: ".$e->getMessage()], 500);
}