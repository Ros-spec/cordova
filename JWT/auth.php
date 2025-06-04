<?php

require "config/index.php";

header("Content-Type: application/json");

// Intenta leer desde $_POST o desde el cuerpo JSON crudo
$nombre_usuario = $_POST["usuario"] ?? null;
$contrasena     = $_POST["contrasena"] ?? null;

// $nombre_usuario = 'admin';
// $contrasena     = 'admin';


if (!$nombre_usuario || !$contrasena) {
    $input = json_decode(file_get_contents("php://input"), true);
    $nombre_usuario = $input["usuario"] ?? null;
    $contrasena     = $input["contrasena"] ?? null;
}

// Si aún faltan datos, terminar
if (!$nombre_usuario || !$contrasena) {
   echo json_encode(["status" => "error", "message" => "Usuario o contraseña no recibidos"]);
   exit;
}

// Realiza la consulta segura con parámetros
$select = $con->select("usuarios", "Id_Usuario, Tipo_Usuario, Preferencias, Token_Tipo, Token_STAT");
$select->where("Nombre_Usuario", "=", $nombre_usuario)
       ->where("Contrasena", "=", $contrasena);

$usuario = $select->fetch();

// Verifica si se encontró el usuario
if ($usuario && is_array($usuario)) {
    if ($usuario["Token_Tipo"] === "c" && $usuario["Token_STAT"] == 1) {
        echo json_encode([
            "status" => "verificar",
            "message" => "Revisa tu correo para poder iniciar sesión."
        ]);
        exit;
    }

    $payload = [
        "iat" => time(),
        "exp" => time() + (60 * 10), // 60 seg * 10 = 10 min
        "sub" => $usuario["Id_Usuario"] . "/" . $usuario["Tipo_Usuario"]
    ];

    $jwt = Firebase\JWT\JWT::encode($payload, "Test12345", "HS256");

    echo json_encode([
        "status"  => "ok",
        "usuario" => $usuario,
        "jwt"     => $jwt
    ]);
    exit;
}

// Si no se encontró el usuario
echo json_encode(["status" => "error", "message" => "Usuario o contraseña incorrectos"]);
?>
