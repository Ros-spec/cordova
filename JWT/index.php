<?php

require "config/index.php";

$headers = getallheaders();
//var_dump($headers);
// exit;

if (!isset($headers["Authorization"])) {
  	http_response_code(401);
    echo "Token requerido.";
    exit;
}

$token = str_replace("Bearer ", "", $headers["Authorization"]);

try {
$decoded = Firebase\JWT\JWT::decode($token, new Firebase\JWT\Key("Test12345", "HS256"));
    $usuario = explode("/", $decoded->sub);
    $id   = $usuario[0];
    $tipo = $usuario[1];

    // $id   = 1;
    // $tipo = 'admin';


    if (isset($_GET["preferencias"])) {
        $select = $con->select("usuarios", "Id_Usuario, Tipo_Usuario, Preferencias, Token_Tipo, Token_STAT");
        $select->where("Id_Usuario", "=", $id);
        $resultado = $select->fetch();

        header("Content-Type: application/json");
        echo json_encode(array(
            "status" => "ok",
            "usuario" => $resultado[0] ?? null,
            "jwt" => $token
        ));
        exit;
    }
    elseif (isset($_GET["cambiarPreferencias"])) {
        $update = $con->update("usuarios");
        $update->set("Preferencias", $_POST["preferencias"]);
        $update->where("Id_Usuario", "=", $id);
        $update->execute();
    }
    elseif (isset($_GET["productos"])) {
    $offset = $_GET["start"] ?? 0;
	$limit = $_GET["length"] ?? 10;
	$search = $_GET["search"]["value"] ?? "";
            
    // Aplicar el comodín % para búsquedas con LIKE
    $searchLike = "%$search%";

    // Consulta principal con paginación
    $select1 = $con->select("productos", "Id_Producto, Nombre_Producto, Precio, Existencias, IF(Imagen IS NULL, '', 1) AS Imagen");
    $select1->where("Nombre_Producto", "LIKE", $searchLike);
    if (is_numeric($offset) && is_numeric($limit)) {
        $select1->limit("$offset, $limit");
    }

    // Consulta para contar registros filtrados
    $select2 = $con->select("productos", "COUNT(Id_Producto) AS total");
    $select2->where("Nombre_Producto", "LIKE", $searchLike);
    $totalResult = $select2->fetchAll();
    $totalRegistros = (int) ($totalResult[0]["total"] ?? 0);

    $productos = $select1->fetchAll();

    foreach ($productos as $x => $producto) {
        $id_producto = $producto["Id_Producto"];
        $productos[$x]["acciones"] = '<a class="btn btn-primary" href="#/productos/' . $id_producto . '">
            <i class="bi bi-pencil"></i>
             <span class="d-none d-lg-block d-xl-block">Editar</span>
        </a>';

        if ($producto["Imagen"]) {
            $productos[$x]["Imagen"] = '<div class="div-producto-imagen" style="cursor: pointer;" data-id="' . $id_producto . '">
                <div class="text-center mt-2 text-nowrap">
                    <span class="bg-body-tertiary rounded shadow shadow-sm p-2 bi bi-zoom-in"></span>
                </div>
                <div class="mt-1">
                    <span class="bg-body-tertiary rounded shadow shadow-sm p-2 mt-3 bi bi-image"></span>
                </div>
            </div>';
        }
    }

    header("Content-Type: application/json");
    echo json_encode(array(
        "recordsTotal"    => $totalRegistros,
        "recordsFiltered" => $totalRegistros,
        "data"            => $productos
    ));
}

    elseif (isset($_GET["imagenProducto"])) {
        $select = $con->select("productos", "TO_BASE64(Imagen) AS Imagen");
        $select->where("Id_Producto", "=", $_GET["id"]);
        header("Content-Type: application/json");
        echo json_encode($select->Fetch());
    }
    elseif (isset($_GET["producto"])) {
        $select = $con->select("productos", "Id_Producto, Nombre_Producto, Precio, Existencias, TO_BASE64(Imagen) AS Imagen");
        $select->where("Id_Producto", "=", $_GET["id"]);
        header("Content-Type: application/json");
        echo json_encode($select->Fetch());
    }
    elseif (isset($_GET["guardarProducto"])) {
        $id_producto     = $_POST["id"];
        $nombre_producto = $_POST["nombreProducto"];
        $precio          = $_POST["precio"];
        $existencias     = $_POST["existencias"];
        $imagen          = null;

        if (isset($_FILES["imagen"])) {
            $allowed_image_extension = array("image/webp");
            $type = $_FILES["imagen"]["type"];
            if (in_array($type, $allowed_image_extension)) {
                $imagen = file_get_contents($_FILES["imagen"]["tmp_name"]);
            }
        }

        if ($id_producto && is_numeric($id_producto)) {
            $guardar = $con->update("productos");
            $guardar->set("Nombre_Producto", $nombre_producto);
            $guardar->set("Precio", $precio);
            $guardar->set("Existencias", $existencias === "" ? null : $existencias);
            if ($imagen !== null) $guardar->set("Imagen", $imagen);
            $guardar->where("Id_Producto", "=", $id_producto);
        } else {
            $campos = "Nombre_Producto, Precio, Existencias" . ($imagen !== null ? ", Imagen" : "");
            $guardar = $con->insert("productos", $campos);
            $guardar->value($nombre_producto);
            $guardar->value($precio);
            $guardar->value($existencias === "" ? null : $existencias);
            if ($imagen !== null) $guardar->value($imagen);
        }

        $guardar->execute();
        echo "correcto";
    }
    elseif (isset($_GET["ventas"])) {
        $select = $con->select("ventas", "ventas.*, usuarios.Nombre_Usuario");
        $select->join("INNER", "usuarios", "usuarios.Id_Usuario = ventas.Id_Usuario");
        $select->orderby("Fecha_Hora DESC");
        $select->limit(10);
        header("Content-Type: application/json");
        echo json_encode($select->FetchAll());
    }
  elseif (isset($_GET["venta"])) {
    $id = $_GET["id"] ?? null;

    if ($id) {
        $select = $con->select("ventas", "
            SUM(detalles_ventas.Precio_Venta * detalles_ventas.Cantidad) AS Total_Precio,
            SUM(detalles_ventas.Cantidad) AS Total_Cantidad,
            ventas.Id_Venta,
            ventas.Fecha_Hora,
            ventas.Pago,
            usuarios.Nombre_Usuario
        ");

        $select->join("INNER", "usuarios", "usuarios.Id_Usuario = ventas.Id_Usuario");
        $select->join("LEFT", "detalles_ventas", "detalles_ventas.Id_Venta = ventas.Id_Venta");
        $select->where("ventas.Id_Venta", "=", $id);
        $select->groupby("ventas.Id_Venta");

        header("Content-Type: application/json");
        echo json_encode($select->fetch());
    } else {
        http_response_code(400);
        echo json_encode(["error" => "Falta el parámetro 'id'"]);
    }
}


    elseif (isset($_GET["guardarVenta"])) {
        $id_venta   = $_GET["id"];
        $pago       = $_POST["pago"];

        if ($id_venta && is_numeric($id_venta)) {
            $guardar = $con->update("ventas");
            $guardar->set("Pago", $pago === "" ? null : $pago);
            $guardar->where("Id_Venta", "=", $id_venta);
            $guardar->where_and("Id_Usuario", "=", $id);
            $guardar->where_and("Pago", "IS", NULL);
            $guardar->execute();
            echo "correcto";
        } else {
            $guardar = $con->insert("ventas", "Id_Usuario, Fecha_Hora");
            $guardar->value($id);
            $guardar->value(date("Y-m-d H:i:s"));
            $guardar->execute();
            echo $con->lastInsertId();
        }
    }
    elseif (isset($_GET["detallesVenta"])) {
        $select = $con->select("detalles_ventas", "detalles_ventas.*, ventas.Fecha_Hora, ventas.Pago, usuarios.Nombre_Usuario, productos.Nombre_Producto");
        $select->join("INNER", "ventas", "ventas.Id_Venta = detalles_ventas.Id_Venta");
        $select->join("INNER", "usuarios", "usuarios.Id_Usuario = ventas.Id_Usuario");
        $select->join("INNER", "productos" , "productos.Id_Producto = detalles_ventas.Id_Producto");
        $select->where("detalles_ventas.Id_Venta", "=", $_GET["id"]);
        header("Content-Type: application/json");
        echo json_encode($select->FetchAll());
    }
    elseif (isset($_GET["autocompleteProductos"])) {
        $select = $con->select("productos", "Id_Producto AS value, Nombre_Producto AS label, Precio, Existencias");
        $select->where("Nombre_Producto", "LIKE", $_GET["text"]);
        $select->orderby("Nombre_Producto");
        $select->limit(10);
        header("Content-Type: application/json");
        echo json_encode($select->fetch());
    }
    elseif (isset($_GET["guardarDetalleVenta"])) {
        $id_detalle_venta = $_POST["idDetalleVenta"];
        $id_venta         = $_GET["id"];
        $id_producto      = $_POST["idProducto"];
        $precio_venta     = $_POST["precioVenta"];
        $cantidad         = $_POST["cantidad"];

        if (is_numeric($id_venta)) {
            if ($id_detalle_venta && is_numeric($id_detalle_venta)) {
                $guardar = $con->update("detalles_ventas");
                $guardar->set("Id_Producto", $id_producto);
                $guardar->set("Precio_Venta", $precio_venta);
                $guardar->set("Cantidad", $cantidad);
                $guardar->where("Id_Detalle_Venta", "=", $id_detalle_venta);
                $guardar->where_and("Id_Venta", "=", $id_venta);
            } else {
                $guardar = $con->insert("detalles_ventas", "Id_Venta, Id_Producto, Precio_Venta, Cantidad");
                $guardar->value($id_venta);
                $guardar->value($id_producto);
                $guardar->value($precio_venta);
                $guardar->value($cantidad);
            }

            $guardar->execute();
            echo "correcto";
        }
    } 
	elseif (isset($_GET["eliminarDetalleVenta"])) {
        $id_detalle_venta = $_POST["idDetalleVenta"];
        $id_venta         = $_POST["id"];

        if (is_numeric($id_venta)) {
            $delete = $con->delete("detalles_ventas");
            $delete->where("Id_Detalle_Venta", "=", $id_detalle_venta);
            $delete->where_and("Id_Venta", "=", $id_venta);
            $delete->execute();
            $con->truncate_AI("detalles_ventas", "Id_Detalle_Venta");
            echo "correcto";
        }
    } 
	elseif (isset($_GET["notificaciones"])) {
        $select = $con->select("notificaciones");
        $select->join("INNER", "usuarios", "usuarios.Id_Usuario = notificaciones.Id_Usuario");
        $select->limit(10);
        header("Content-Type: application/json");
        echo json_encode($select->FetchAll());
    }
	elseif(isset($_GET["etiquetas"])){
        $select = $con->select("etiquetas");
        header("Content-Type: application/json");
        echo json_encode($select->FetchAll());            
    }
	elseif(isset($_GET["movimientos"])){
        $select = $con->select("movimientos");
        header("Content-Type: application/json");
        echo json_encode($select->FetchAll());            
    }
	elseif(isset($_GET["movimientosEtiqueta"])){
        $select = $con->select("movimientosetiquetas");
        header("Content-Type: application/json");
        echo json_encode($select->FetchAll());            
    }



}

catch (Exception $error) {
    http_response_code(401);
	header("Content-Type: application/json");
    echo json_encode(array("error" => "Token inválido", "detalles" => $error->getMessage()));
}

?>
