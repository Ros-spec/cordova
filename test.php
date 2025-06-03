<?php require_once '/JWT/config/conexion.php';

    $host = "sql202.infinityfree.com";
    $bd   = "if0_39018236_prueba";
    $user = "if0_39018236";
    $pw   = "9KXouMvOYC";

header("Content-Type: application/json");
try {
    $pdo = new PDO("mysql:host=$host ;dbname=$bd", $user , $pw, [
        PDO::ATTR_TIMEOUT => 3,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo json_encode(["status" => "ok"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
