<?php

require "config/index.php";

$headers = getallheaders();

if (!isset($headers["Authorization"])) {
    http_response_code(401);
    echo "Token requerido.";
    exit;
}

$token = str_replace("Bearer ", "", $headers["Authorization"]);

try {
    $decoded = Firebase\JWT\JWT::decode($token, new Firebase\JWT\Key("Test12345", "HS256"));
    # header("Content-Type: application/json");
    # echo json_encode(array("message" => "Acceso autorizado", "user_id" => $decoded->sub));
    $usuario = explode("/", $decoded->sub);
    $id   = $usuario[0];
    $tipo = $usuario[1];

    if (isset($_GET["preferencias"])) {
        $select = $con->select("usuarios", "Id_Usuario, Tipo_Usuario, Preferencias, Token_Tipo, Token_STAT");
        $select->where("Id_Usuario", "=", $id);
        header("Content-Type: application/json");
        echo json_encode($select->execute());
    }
    elseif (isset($_GET["cambiarPreferencias"])) {
        $update = $con->update("usuarios");
        $update->set("Preferencias", $_POST["preferencias"]);
        $update->where("Id_Usuario", "=", $id);
        $update->execute();
    }

    elseif (isset($_GET["sensor"])) {
        if ($tipo != 1) {
            http_response_code(401);
            echo "Debes ser administrador.";
            exit;
        }
        
        $offset = $_GET["offset"];
        $limit  = $_GET["limit"];

        $select = $con->select("sensor");
        $select->orderby("Id_Log DESC");
        if (is_numeric($offset)
        &&  is_numeric($limit)) {
            $select->limit("$offset, $limit");
        }
        header("Content-Type: application/json");
        echo json_encode($select->execute());
    }
    elseif (isset($_GET["sensorCount"])) {
        $limit = $_GET["limit"];

        if (!is_numeric($limit)) {
            $limit = 10;
        }

        $select = $con->select("sensor", "CEIL(COUNT(Id_Log)/$limit)");
        $count = $select->execute();
        echo $count[0][0];
    }

    elseif (isset($_GET["productos"])) {
        $offset = $_GET["offset"];
        $limit  = $_GET["limit"];
        $search = $_GET["search"];

        $select = $con->select("productos", "Id_Producto, Nombre_Producto, Precio, Existencias, TO_BASE64(Imagen) AS Imagen");
        $select->where("Nombre_Producto", "LIKE", $search);
        if (is_numeric($offset)
        &&  is_numeric($limit)) {
            $select->limit("$offset, $limit");
        }
        header("Content-Type: application/json");
        echo json_encode($select->execute());
    }
    elseif (isset($_GET["productosCount"])) {
        $limit = $_GET["limit"];

        if (!is_numeric($limit)) {
            $limit = 10;
        }

        $select = $con->select("productos", "CEIL(COUNT(Id_Producto)/$limit)");
        $count = $select->execute();
        echo $count[0][0];
    }

    elseif (isset($_GET["ventas"])) {
        if ($tipo != 1) {
            http_response_code(401);
            echo "Debes ser administrador.";
            exit;
        }

        $select = $con->select("ventas");
        $select->innerjoin("usuarios ON usuarios.Id_Usuario = ventas.Id_Usuario");
        $select->limit(10);
        header("Content-Type: application/json");
        echo json_encode($select->execute());
    }

    elseif (isset($_GET["notificaciones"])) {
        if ($tipo != 1) {
            http_response_code(401);
            echo "Debes ser administrador.";
            exit;
        }

        $select = $con->select("notificaciones");
        $select->innerjoin("usuarios ON usuarios.Id_Usuario = notificaciones.Id_Usuario");
        $select->limit(10);
        header("Content-Type: application/json");
        echo json_encode($select->execute());
    }
}
catch (Exception $error) {
    http_response_code(401);
    echo "Token inválido.";
}

?>
