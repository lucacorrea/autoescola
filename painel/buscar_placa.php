<?php

include "conexao.php";

// Verificar se a placa foi enviada via POST
if (isset($_POST['placa'])) {
    $placa = $_POST['placa'];

    try {
        // Consultar o instrutor correspondente à placa
        $query = "SELECT nome_instrutor FROM instrutores WHERE placa_instrutor = :placa";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':placa', $placa, PDO::PARAM_STR);
        $stmt->execute();

        // Verificar se algum resultado foi retornado
        $nome_instrutor = $stmt->fetchColumn();

        if ($nome_instrutor) {
            echo $nome_instrutor; // Retorna o nome do instrutor
        } else {
            echo "Instrutor não encontrado para esta placa.";
        }
    } catch (PDOException $e) {
        echo "Erro ao buscar o instrutor: " . htmlspecialchars($e->getMessage());
    }
} else {
    echo "Nenhuma placa foi enviada.";
}
?>
