<?php
// Função para obter o nome do mês em português
function nomeMesPortugues($mes) {
    $meses = [
        1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março', 4 => 'Abril',
        5 => 'Maio', 6 => 'Junho', 7 => 'Julho', 8 => 'Agosto',
        9 => 'Setembro', 10 => 'Outubro', 11 => 'Novembro', 12 => 'Dezembro'
    ];
    return $meses[$mes];
}

include "conexao.php";

// Obter a data atual e três meses anteriores
$meses = [
    date('Y-m-01', strtotime('-3 months')),  // Três meses atrás
    date('Y-m-01', strtotime('-2 months')), // Dois meses atrás
    date('Y-m-01', strtotime('-1 month')),  // Mês anterior
    date('Y-m-01', strtotime('now')),       // Mês atual
];

// Array para armazenar os totais por mês
$totaisPorMes = [];

// Iterar sobre os meses e contar os processos de cada mês
foreach ($meses as $mes) {
    $sql = "SELECT COUNT(*) AS total 
            FROM alunos 
            WHERE MONTH(vencimento_processo) = MONTH(:mes) 
              AND YEAR(vencimento_processo) = YEAR(:mes)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':mes', $mes);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $totaisPorMes[] = [
        'mes' => nomeMesPortugues((int)date('m', strtotime($mes))), // Nome do mês em português
        'total' => (int) $row['total'] // Total de processos no mês
    ];
}

// Fechar a conexão (opcional, já que o PDO fecha automaticamente ao final do script)
$conn = null;

// Enviar os dados em formato JSON
header('Content-Type: application/json');
echo json_encode($totaisPorMes);
?>
