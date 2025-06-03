<?php

require "config/index.php";

$nombre_usuario = $_POST["usuario"];
$contrasena     = $_POST["contrasena"];

$select = $con->select("usuarios", "Id_Usuario, Tipo_Usuario, Preferencias, Token_Tipo, Token_STAT");
$select->where("Nombre_Usuario", "=", $nombre_usuario);
$select->where_and("Contrasena", "=", $contrasena);

$usuarios = $select->execute();

if (count($usuarios)) {
    $usuario = $usuarios[0];

    $token_tipo = $usuario["Token_Tipo"];
    $token_stat = $usuario["Token_STAT"];

    if ($token_tipo == "c" && $token_stat == 1) {
        echo "Revisa tu correo para poder iniciar sesión.";
        exit;
    }

    $payload = [
        "iat" => time(),
        "exp" => time() + (60 * 5),
        "sub" => $usuario["Id_Usuario"] . "/" . $usuario["Tipo_Usuario"]
    ];

    $jwt = Firebase\JWT\JWT::encode($payload, "Test12345", "HS256");
    echo "correcto----->" . json_encode($usuario) . "----->";
    echo $jwt;
    exit;
}

echo "error";

?>
