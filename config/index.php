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

require "conexion.php";
require "enviarCorreo.php";
# mkdir firebase-php-jwt
# cd firebase-php-jwt
# composer require firebase/php-jwt
require "firebase-php-jwt/vendor/autoload.php";

$con = new Conexion(array(
     "tipo"       => "mysql",
     "servidor"   => "fdb1028.awardspace.net",
     "bd"         => "4636219_joomlae90723ed",
     "usuario"    => "4636219_joomlae90723ed",
     "contrasena" => "9KXouMvOYC"
));

?>
