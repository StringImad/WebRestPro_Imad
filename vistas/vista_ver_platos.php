<?php
if (isset($_POST["btnVerPagina"]))
    $id_usuario = $_POST["btnVerPagina"];
elseif (isset($_POST["btnEditarPagina"]))
    $id_usuario = $_POST["btnEditarPagina"];


$url = DIR_SERV . "/pagina/" . $id_usuario;
$respuesta = consumir_servicios_REST($url, "GET");
$obj = json_decode($respuesta);
if (!$obj) {
    if (isset($_SESSION["user_name"]))
        consumir_servicios_REST(DIR_SERV . "/salir", "POST", $_SESSION["api_session"]);

    session_destroy();
    die("<p>Error consumiendo el servicio: " . $url . "</p></body></html>");
}
if (isset($obj->mensaje_error)) {
    if (isset($_SESSION["user_name"]))
        consumir_servicios_REST(DIR_SERV . "/salir", "POST", $_SESSION["api_session"]);
    session_destroy();
    die("<p>" . $obj->mensaje_error . "</p></body></html>");
}
echo "<pre>";
var_dump($obj);
echo "</pre>";
$id_restaurante = $obj->id_restaurant;

echo "<p>------------------------------<p>";

if (isset($_POST["btnCrearPlato"])) {
    echo "<p>---------------1---------------<p>";
    // Asumimos que todos los campos son obligatorios, por lo que verificamos que todos estén establecidos
    $error_form = empty($_POST["plate_name"]) || empty($_POST["descripcion"]) || empty($_POST["precio"]) || empty($_POST["half_price"]) || empty($_POST["food_type"]) || empty($_POST["alergenos"]);

    if (!$error_form) {
        $url = DIR_SERV . "/insertarPlato/" . $id_usuario;;
        $datos_env = array(
            "plate_name" => $_POST["plate_name"],
            "descrip" => $_POST["descripcion"],
            "price" => $_POST["precio"],
            "half_price" => $_POST["half_price"],
            "food_type" => $_POST["food_type"],
            "allergen" => $_POST["alergenos"],
            "api_session" => $_SESSION["api_session"] // Asumiendo que esta es la sesión de la API
        );
    
        $respuesta = consumir_servicios_REST($url, "POST", $datos_env);
              // Mensajes de depuración
              echo "<pre>";
              var_dump($datos_env);
              echo "</pre>";
        $obj = json_decode($respuesta);
        if (!$obj) {
            if (isset($_SESSION["user_name"]))
                consumir_servicios_REST(DIR_SERV . "/salir", "POST", $_SESSION["api_session"]);

            session_destroy();
            die("<p>Error consumiendo el servicio: " . $url . "</p></body></html>");
        }
        if (isset($obj->mensaje_error)) {
            if (isset($_SESSION["user_name"]))
                consumir_servicios_REST(DIR_SERV . "/salir", "POST", $_SESSION["api_session"]);
            session_destroy();
            die("<p>" . $obj->mensaje_error . "</p></body></html>");
        }
    } else {
        // Manejo del error cuando hay campos vacíos en el formulario
        $_SESSION["comentario"] = "Todos los campos son obligatorios.";
    }
}
?>

<nav class="nav-menu">
    <div class="menu-toggle">
        <div class="hamburguesa">
            <span class="barra"></span>
            <span class="barra"></span>
            <span class="barra"></span>
        </div>
    </div>
    <ul id="op-menu-comida" class="menu">
     
        <li id="Entrante" class="opcion-menu">
            <a class="opcion-menu" href="#Ensaladas">Entrantes</a>
        </li>
        <li id="Ensalada" class="opcion-menu">
            <a class="opcion-menu" href="#Pescados">Ensaladas</a>

        </li>

        <li id="Pescado" class="opcion-menu">
            <a class="opcion-menu" href="#Ensaladas">Pescados</a>

        </li>
        <li id="Wok" class="opcion-menu">
            <a class="opcion-menu" href="#Woks">Woks y Pastas</a>

        </li>
        <li id="Carne" class="opcion-menu">
            <a class="opcion-menu" href="#Carnes">Carnes</a>

        </li>
        <li id="Arroce" class="opcion-menu">
            <a class="opcion-menu" href="#Arroces">Arroces</a>

        </li>
        <li id="Nino" class="opcion-menu">
            <a class="opcion-menu" href="#Ninos">Niños</a>

        </li>
        <li id="Postre" class="opcion-menu">
            <a class="opcion-menu" href="#Postres">Postres</a>

        </li>
    </ul>
</nav>

<?php
echo "<span class='crear'>Añadir Plato <i class='fa-solid fa-plus' title='Añadir'></i></span>";
?>
<div id="modalAñadirPlato" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <form id="formAñadirPlato" class="estiloForm" method="post">
            <h2>Añadir Plato</h2>
            <label for="plateName">Nombre del Plato:</label>
            <input type="text" id="plateName" name="plate_name" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" required>

            <label for="halfPrice">Precio Media Ración:</label>
            <input type="number" id="halfPrice" name="half_price" required>

            <label for="foodType">Tipo de Comida:</label>
            <select id="foodType" name="food_type">
                <option value="entrante">Entrantes</option>
                <option value="ensalada">Ensaladas</option>
                <option value="pescado">Pescados</option>
                <option value="wok">Woks y Pastas</option>
                <option value="carne">Carnes</option>
                <option value="arroce">Arroces</option>
                <option value="nino">Ninos</option>
                <option value="postre">Postres</option>
            </select>


            <label for="alergenos">Alergeno:</label>
            <select multiple id="alergenos" name="alergenos">
                <option value="gluten">Gluten</option>
                <option value="crustaceos">Crustáceos</option>
                <option value="huevos">Huevos</option>
                <option value="pescado">Pescado</option>
                <option value="cacahuetes">Cacahuetes</option>
                <option value="soja">Soja</option>
                <option value="lacteo">Lácteos</option>
                <option value="frutos_con_cascara">Frutos con cáscara</option>
                <option value="apio">Apio</option>
                <option value="mostaza">Mostaza</option>
                <option value="sesamo">Sésamo</option>
                <option value="sulfitos">Sulfitos</option>
                <option value="altramuces">Altramuces</option>
                <option value="moluscos">Moluscos</option>
            </select>


            <button type="submit" name="btnCrearPlato">Añadir Plato</button>
        </form>
    </div>
</div>
<div id="modalEditar" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Editar Plato</h2>
        <form id="formEditarPlato" class="estiloForm" method="post">
            <input type="hidden" id="editarIdPlato" name="id_plato">

            <label for="editarPlateName">Nombre del Plato:</label>
            <input type="text" id="editarPlateName" name="plate_name" required>

            <label for="editarDescripcion">Descripción:</label>
            <textarea id="editarDescripcion" name="descripcion" required></textarea>

            <label for="editarPrecio">Precio:</label>
            <input type="number" id="editarPrecio" name="precio" required>

            <label for="editarHalfPrice">Precio Media Ración:</label>
            <input type="number" id="editarHalfPrice" name="half_price" required>

            <label for="foodType">Tipo de Comida:</label>
            <select id="foodType" name="food_type">
                <option value="entrante">Entrantes</option>
                <option value="ensalada">Ensaladas</option>
                <option value="pescado">Pescados</option>
                <option value="wok">Woks y Pastas</option>
                <option value="carne">Carnes</option>
                <option value="arroce">Arroces</option>
                <option value="nino">Ninos</option>
                <option value="postre">Postres</option>
            </select>


            <label for="alergenos">Alergeno:</label>
            <select multiple id="alergenos" name="alergenos">
                <option value="gluten">Gluten</option>
                <option value="crustaceos">Crustáceos</option>
                <option value="huevos">Huevos</option>
                <option value="pescado">Pescado</option>
                <option value="cacahuetes">Cacahuetes</option>
                <option value="soja">Soja</option>
                <option value="lacteo">Lácteos</option>
                <option value="frutos_con_cascara">Frutos con cáscara</option>
                <option value="apio">Apio</option>
                <option value="mostaza">Mostaza</option>
                <option value="sesamo">Sésamo</option>
                <option value="sulfitos">Sulfitos</option>
                <option value="altramuces">Altramuces</option>
                <option value="moluscos">Moluscos</option>
            </select>

            <button type="submit" name="btnEditarPlato">Actualizar Plato</button>
        </form>
    </div>
</div>

<div id="modalBorrar" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <h2>Eliminar Plato</h2>
        <p>¿Estás seguro de que deseas eliminar este plato?</p>
        <button id="confirmarBorrar">Eliminar</button>
        <button class="close">Cancelar</button>
    </div>
</div>
    <?php


echo "<div class='cartas-platos'>";

foreach ($obj->pagina as $tupla) {
    echo "<div id='{$tupla->food_type}'class='plato {$tupla->food_type}'>";
    echo "<h2>Nombre Plato: {$tupla->plate_name}</h2>";
    echo "<p>Descripción: <strong>{$tupla->descrip}</strong></p>";
    echo "<p>Precio: {$tupla->price}</p>";
    echo "<p>Precio media ración: {$tupla->half_price}</p>";
    echo "<p>Alergeno: {$tupla->allergen}</p>";
    echo "<p>Tipo: {$tupla->food_type}</p>";
    echo "<div class='icons'>";
    echo "<i class='fas fa-pencil-alt editar-icon' data-id='{$tupla->id}' title='Editar'></i>";
    echo "<i class='fas fa-trash-alt borrar-icon' data-id='{$tupla->id}' title='Borrar'></i>";
    echo "</div>";
    echo "</div>";
}
echo "</div>";
echo "<p class='mensaje'>" . $_SESSION["comentario"] . "</p>";

    ?>
    <button id="scrollTopBtn" title="Ir Arriba">↑</button>
