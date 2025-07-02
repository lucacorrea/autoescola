<?php
header('Content-Type: application/json');

include "conexao.php";

// Define o fuso horário
date_default_timezone_set('America/Manaus');

// Obtém a data atual no formato 'm-d'
$dataAtual = date('m-d');

// SQL para buscar alunos que fazem aniversário hoje e seus respectivos e-mails, excluindo os com status "Parabenizado"
$sql = "
    SELECT a.nome, la.email 
    FROM alunos a
    JOIN login_aluno la ON a.cpf = la.cpf_aluno
    WHERE DATE_FORMAT(a.data_nascimento, '%m-%d') = :dataAtual
    AND (la.status_cadastro IS NULL OR la.status_cadastro != 'Parabenizado')
";

// Prepara a consulta
$stmt = $conn->prepare($sql);

// Verifica se a preparação da consulta foi bem-sucedida
if ($stmt) {
    // Bind da variável
    $stmt->bindParam(':dataAtual', $dataAtual, PDO::PARAM_STR);

    // Executa a consulta
    $stmt->execute();

    // Inicializa um array para armazenar os aniversariantes
    $aniversariantes = [];

    // Recupera os resultados como um array associativo
    $aniversariantes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifica se foram encontrados aniversariantes
    if (empty($aniversariantes)) {
        echo json_encode(['message' => 'Nenhum aniversariante encontrado.']);
        exit; // Finaliza o script
    }
} else {
    echo json_encode(['error' => 'Erro na preparação da consulta: ' . $conn->errorInfo()[2]]);
    exit; // Finaliza o script
}

// Fecha a conexão
$conn = null;

// Retorna os aniversariantes em formato JSON
echo json_encode($aniversariantes);
?>
