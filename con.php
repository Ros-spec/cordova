<?php
// $host = "sql202.infinityfree.com";
// $dbname = "if0_39018236_prueba";
// $user = "if0_39018236";
// $pass = "9KXouMvOYC";

$host = 'caboose.proxy.rlwy.net';
$port = '44486';
$dbname = 'railway';
$user = 'root';
$password = 'LtnSDbWbdaJCyOhqZreXJSMbKggVVTod';

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
