<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "autoescola";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Falha na conex   o: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");


?>
