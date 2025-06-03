<?php
header("Content-Type: application/json");
try {
    $pdo = new PDO("mysql:host=REMOTO_HOST;dbname=DB", "USUARIO", "CLAVE", [
        PDO::ATTR_TIMEOUT => 3,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo json_encode(["status" => "ok"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "msg" => $e->getMessage()]);
}
