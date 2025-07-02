<?php
session_start(); // Inicia a sessão

// Verifica se o aluno está logado
if (!isset($_SESSION['user_id'])) {
    header("Location: loaderAluno.php");
    exit();
}

include "conexao.php";

// Consulta ao banco para obter os dados do aluno logado
$userId = $_SESSION['user_id'];

// Verificar se a preparação da consulta falhou
$sqlLoginAluno = "SELECT nome_aluno FROM login_aluno WHERE id = ?";
$stmt = $conn->prepare($sqlLoginAluno);

if ($stmt === false) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$resultLoginAluno = $stmt->get_result();

$nomeAlunoLogado = null;
if ($resultLoginAluno->num_rows > 0) {
    $rowLoginAluno = $resultLoginAluno->fetch_assoc();
    $nomeAlunoLogado = $rowLoginAluno['nome_aluno'];
}

// Inicializa uma variável para armazenar o preço e uma lista para armazenar parcelas
$preco = null;
$valorEntrada = null;
$parcelas = [];

// Verifica se o aluno logado existe e consulta os preços e parcelas
if ($nomeAlunoLogado) {
    // Consultar a tabela servicos_aluno para obter o preço e valor de entrada
    $sqlPreco = "SELECT valor_entrada, preco FROM servicos_aluno WHERE nome_aluno = ? LIMIT 1";
    $stmtPreco = $conn->prepare($sqlPreco);

    if ($stmtPreco === false) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmtPreco->bind_param("s", $nomeAlunoLogado);
    $stmtPreco->execute();
    $resultPreco = $stmtPreco->get_result();

    if ($resultPreco->num_rows > 0) {
        $rowPreco = $resultPreco->fetch_assoc();
        $preco = $rowPreco['preco'];
        $valorEntrada = $rowPreco['valor_entrada']; // Captura o valor de entrada
    }

    // Consultar a tabela info_parcelas para obter as parcelas
    $sqlParcelas = "SELECT * FROM info_parcelas WHERE nome_aluno = ?";
    $stmtParcelas = $conn->prepare($sqlParcelas);

    if ($stmtParcelas === false) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmtParcelas->bind_param("s", $nomeAlunoLogado);
    $stmtParcelas->execute();
    $resultParcelas = $stmtParcelas->get_result();

    while ($parcela = $resultParcelas->fetch_assoc()) {
        $parcelas[] = $parcela;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logo.png" type="image/x-icon">
    <title>Autoescola Dinâmica</title>

    <link rel="stylesheet" href="./css/bootstrap.min.css" />
    <link rel="stylesheet" href="./css/fontawesome.css" />
    <link rel="stylesheet" href="./css/animate.css" />
    <link rel="stylesheet" href="./css/main.css" />
    <style>
        .sobre {
            text-align: justify;
        }
        .card-parcela {
            margin-bottom: 20px;
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
       
        .card-body {
            padding: 20px;
        }
        .card-title {
            font-size: 19px;
            color: #333;
            margin-bottom: 10px;
        }
        .card-body p {
            margin: 0;
            padding: 10px 0;
            font-size: 17px;
            color: #555;
            border-bottom: 1px solid #ddd;
        }
        .card-body p:last-of-type {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="bg-top sobre"></div>

    <header class="width-fix mt-3">
        <div class="card">
            <div class="d-flex">
                <a href="./info.php" class="container-voltar">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="infos text-center">
                    <h1 class="header-title mb-0"><b>Informações das Parcelas</b></h1>
                </div>
            </div>
        </div>

        <div class="card card-status-pedido mt-3 mb-4">
            <div class="detalhes-produto">
                <div class="infos-produto">
                    <p class="name-total mb-0" style="font-size: 17px;"><b>Valor Total</b></p>
                    <p class="price-total mb-0 color-primary">
                        <b>
                            <?php 
                            echo $preco !== null ? number_format(floatval($preco), 2, ',', '.') : '0';
                            ?>
                        </b>
                    </p>
                </div>
            </div>
            <div class="detalhes-produto-acoes">
                <i class="fab fa-whatsapp"></i>
                <p class="mb-0 mt-1">Whatsapp</p>
            </div>
        </div>
    </header>

    <section class="lista width-fix mt-3 mb-4 pb-5">
        <div class="container">
            <?php if (!empty($parcelas)) : ?>
                <!-- Exibe as parcelas em cartões -->
                <?php foreach ($parcelas as $parcela) : ?>
                    <div class="card card-parcela">
                        <div class="card-body">
                            <h5 class="card-title"><b>Serviço:</b> <?php echo htmlspecialchars($parcela['servico']); ?></h5>
                            <p><strong>Forma de Pagamento:</strong> <?php echo htmlspecialchars($parcela['status']); ?></p>
                            <p><strong>Valor de Entrada:</strong> R$ <?php echo number_format(floatval($valorEntrada), 2, ',', '.'); ?></p>
                            <p><strong>Pago:</strong> <?php echo htmlspecialchars($parcela['pago'] ?? 'Não definido'); ?></p>
                            <p><strong>Número de Parcelas:</strong> <?php echo htmlspecialchars($parcela['numero_parcelas']); ?></p>
                            <p><strong>Data de Expiração:</strong> <?php echo isset($parcela['data_pagamento']) ? date('d/m/Y', strtotime($parcela['data_pagamento'])) : 'Não definido'; ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="alert alert-info" style="border-radius: 15px;">Nenhuma parcela encontrada.</div>
            <?php endif; ?>

            <a href="./info.php" class="btn btn-yellow btn-full voltar mt-3">
                Voltar 
            </a>
        </div>
    </section>

    <script type="text/javascript" src="./js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="./js/item.js"></script>
</body>
</html>
