<?php
header('Content-Type: application/json');

include "conexao.php";

$type = $_GET['type'] ?? ''; // 'placa' ou 'instrutor'
$value = $_GET['value'] ?? '';

$response = [];

if ($type === 'placa') {
    // Se a solicitação é para buscar o instrutor por placa
    $query = "SELECT nome_instrutor FROM instrutores WHERE placa_instrutor = :value";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':value', $value, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $response = ['instrutor' => $data['nome_instrutor'] ?? ''];
} elseif ($type === 'instrutor') {
    // Se a solicitação é para buscar a placa por instrutor
    $query = "SELECT placa_instrutor FROM instrutores WHERE nome_instrutor = :value";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':value', $value, PDO::PARAM_STR);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);
    $response = ['placa' => $data['placa_instrutor'] ?? ''];
}

// Enviar resposta em JSON
echo json_encode($response);
?>