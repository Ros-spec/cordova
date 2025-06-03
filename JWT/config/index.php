<?php

ini_set("display_errors", 1);
ini_set("display_startup_errors", 1);
error_reporting(E_ALL);

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: Authorization, X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Allow: GET, POST, OPTIONS");

// Manejar preflight (opcional, pero recomendado)
if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
    http_response_code(200);
    exit();
}

date_default_timezone_set("America/Matamoros");

require_once "conexion.php";
require_once "enviarCorreo.php";
# mkdir firebase-php-jwt
# cd firebase-php-jwt
# composer require firebase/php-jwt
require_once "firebase-php-jwt/vendor/autoload.php";

$con = new Conexion(array(
    "tipo"       => "mysql",
    "servidor"   => "caboose.proxy.rlwy.net",
    "bd"         => "railway",
    "usuario"    => "root",
    "port"       => "44486",
    "contrasena" => "LtnSDbWbdaJCyOhqZreXJSMbKggVVTod"
));

?>
