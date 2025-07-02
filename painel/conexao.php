<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "autoescola";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Falha na conexão: " . $e->getMessage());
}

?>
