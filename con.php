<?php
// $host = "sql202.infinityfree.com";
// $dbname = "if0_39018236_prueba";
// $user = "if0_39018236";
// $pass = "9KXouMvOYC";

$host = 'caboose.proxy.rlwy.net';
$port = '44486';
$dbname = 'railway';
$user = 'root';
$pass = 'LtnSDbWbdaJCyOhqZreXJSMbKggVVTod';

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    
    // Opcional: configurar errores como excepciones
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ Conexión exitosa a Railway.";
} catch (PDOException $e) {
    echo "❌ Error de conexión: " . $e->getMessage();
}
