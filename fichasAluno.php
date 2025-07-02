<?php
session_start(); // Inicia a sessão

// Verifica se o aluno está logado
if (!isset($_SESSION['user_id'])) {
    // Redireciona para a página de login se o usuário não estiver logado
    header("Location: loaderAluno.php");
    exit();
}

include "conexao.php";

// Consulta ao banco para obter os dados do aluno logado
$userId = $_SESSION['user_id'];

// Verificar se a preparação da consulta falhou
$sqlLoginAluno = "SELECT nome_aluno, cpf_aluno FROM login_aluno WHERE id = ?";
$stmt = $conn->prepare($sqlLoginAluno);

if ($stmt === false) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$resultLoginAluno = $stmt->get_result();

$nomeAlunoLogado = null;
$cpfAlunoLogado = null;
if ($resultLoginAluno->num_rows > 0) {
    $rowLoginAluno = $resultLoginAluno->fetch_assoc();
    $nomeAlunoLogado = $rowLoginAluno['nome_aluno'];
    $cpfAlunoLogado = $rowLoginAluno['cpf_aluno'];
}

// Inicializa um array para armazenar as fichas
$fichas = [];

// Variável para armazenar a categoria da tabela servicos_aluno
$categoriaServicoAluno = null;

// Verifica se o aluno logado existe
if ($nomeAlunoLogado && $cpfAlunoLogado) {
    // Consultar a categoria na tabela servicos_aluno usando nome_aluno
    $sqlServicoAluno = "SELECT categoria FROM servicos_aluno WHERE nome_aluno = ?";
    $stmtServicoAluno = $conn->prepare($sqlServicoAluno);

    if ($stmtServicoAluno === false) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmtServicoAluno->bind_param("s", $nomeAlunoLogado); // Nome do aluno é passado para a consulta
    $stmtServicoAluno->execute();
    $resultServicoAluno = $stmtServicoAluno->get_result();

    if ($resultServicoAluno->num_rows > 0) {
        $rowServicoAluno = $resultServicoAluno->fetch_assoc();
        $categoriaServicoAluno = $rowServicoAluno['categoria'];
    }

    // Consultar a tabela fichas para obter as fichas do aluno
    $sqlFichas = "SELECT * FROM fichas WHERE cpf = ?";
    $stmtFichas = $conn->prepare($sqlFichas);

    if ($stmtFichas === false) {
        die("Erro na preparação da consulta: " . $conn->error);
    }

    $stmtFichas->bind_param("s", $cpfAlunoLogado);
    $stmtFichas->execute();
    $resultFichas = $stmtFichas->get_result();

    while ($ficha = $resultFichas->fetch_assoc()) {
        $fichas[] = $ficha;
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
    <title>Fichas de Direção Veicular</title>

    <link rel="stylesheet" href="./css/bootstrap.min.css" />
    <link rel="stylesheet" href="./css/fontawesome.css" />
    <link rel="stylesheet" href="./css/animate.css" />
    <link rel="stylesheet" href="./css/main.css" />
    <style>
        .sobre {
            text-align: justify;
        }
        .card-ficha {
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
                    <h1 class="header-title mb-0"><b>Informações das Fichas</b></h1>
                </div>
            </div>
        </div>
        
        <?php if ($categoriaServicoAluno) : ?>
            <!-- Exibe a categoria da tabela servicos_aluno -->
            <div class="card card-status-pedido mt-3 mb-4">
                <div class="detalhes-produto">
                    <div class="infos-produto">
                        <p class="name-total mb-0" style="font-size: 17px;"><b>Categoria</b></p>
                        <p class="price-total mb-0 color-primary">
                            <b><?php echo htmlspecialchars($categoriaServicoAluno); ?></b>
                        </p>
                    </div>
                </div>
                <div class="detalhes-produto-acoes">
                    <i class="fab fa-whatsapp"></i>
                    <p class="mb-0 mt-1">Whatsapp</p>
                </div>
            </div>
        <?php endif; ?>
    </header>

    <section class="lista width-fix mt-3 mb-4 pb-5">
        <div class="container">
            <?php if (!empty($fichas)) : ?>
                <!-- Exibe as fichas em cartões, com a categoria da tabela fichas -->
                <?php foreach ($fichas as $ficha) : ?>
                    <div class="card card-ficha">
                        <div class="card-body">
                            <h5 class="card-title"><b>Nome:</b> <?php echo htmlspecialchars($ficha['nome']); ?></h5>
                            <p><strong>Categoria:</strong> <?php echo htmlspecialchars($ficha['categoria']); ?></p>
                            <p><strong>Instrutor:</strong> <?php echo htmlspecialchars($ficha['instrutor']); ?></p>
                            <p><strong>Placa:</strong> <?php echo htmlspecialchars($ficha['placa']); ?></p>
                            <p><strong>Horário Início:</strong> <?php echo date('H:i', strtotime($ficha['horario_inicio'])); ?></p>
                            <p><strong>Horário Fim:</strong> <?php echo date('H:i', strtotime($ficha['horario_fim'])); ?></p>
                            <p><strong>Data da Ficha:</strong> <?php echo date('d/m/Y', strtotime($ficha['data_ficha'])); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="alert alert-danger" style="border-radius: 15px;">Nenhuma ficha encontrada para o aluno logado.</div>
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
