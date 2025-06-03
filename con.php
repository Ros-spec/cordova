<?php
$host = "sql202.infinityfree.com";
$dbname = "if0_39018236_prueba";
$user = "if0_39018236";
$pass = "9KXouMvOYC";

$pdo = new PDO("mysql:host=$host;dbname=$dbname", $user, $pass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
]);
