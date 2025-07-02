<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador, presidente ou suporte
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel'];

        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true;
        }
    }
    
    header("Location: loader.php");
    exit();
}

verificarAcesso();

// Captura o ID do aluno, se disponível
$aluno_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Variáveis para armazenar os dados do aluno
$categoria_aluno = '';
$pago_aluno = '';
$nome_aluno = '';
$servico_aluno = '';
$numero_parcelas_aluno = '';
$data_pagamento = ''; // Adicionando a variável data_pagamento
$data_pagamento_formatada = ''; // Variável para armazenar a data de pagamento formatada

// Se um ID válido foi fornecido, busque os dados do aluno
if ($aluno_id > 0) {
    // Inclui a conexão externa ao banco de dados
    include "conexao.php";

    try {
        // Query para buscar os dados do aluno
        $sql_aluno = "SELECT nome FROM alunos WHERE id = :id";
        $stmt = $conn->prepare($sql_aluno);
        $stmt->bindParam(':id', $aluno_id, PDO::PARAM_INT);
        $stmt->execute();

        // Obtém o nome do aluno
        $nome_aluno = $stmt->fetchColumn();
        if (!$nome_aluno) {
            throw new Exception("Aluno não encontrado com o ID fornecido.");
        }

        // Query para buscar os dados do serviço do aluno
        $sql_servico = "SELECT servico, categoria, pago, numero_parcelas, data_pagamento FROM servicos_aluno WHERE nome_aluno = :nome_aluno";
        $stmt = $conn->prepare($sql_servico);
        $stmt->bindParam(':nome_aluno', $nome_aluno, PDO::PARAM_STR);
        $stmt->execute();

        // Obtém os resultados do serviço do aluno
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($result) {
            $servico_aluno = $result['servico'];
            $categoria_aluno = $result['categoria'];
            $pago_aluno = $result['pago'];
            $numero_parcelas_aluno = $result['numero_parcelas'];
            $data_pagamento = $result['data_pagamento'];

            // Formata a data de pagamento no padrão brasileiro, se houver uma data válida
            if ($data_pagamento && $data_pagamento != '9999-12-31') {
                $data_pagamento_formatada = date("d/m/Y", strtotime($data_pagamento));
            } else {
                $data_pagamento_formatada = ''; // Ou qualquer valor padrão
            }
        } else {
            throw new Exception("Dados de serviço não encontrados para o aluno fornecido.");
        }

    } catch (PDOException $e) {
        die("Erro na execução da consulta: " . $e->getMessage());
    } catch (Exception $e) {
        die($e->getMessage());
    } finally {
        // Fecha a conexão com o banco de dados
        $conn = null;
    }
} else {
    die("ID de aluno inválido.");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Alunos</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css">
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
</head>
<body>
    <div class="container-mensagens" id="container-mensagens"></div>
    
    <section class="bg-menu">
        <div class="conteudo">
            <section class="menu-top">
                <div class="container">
                    <div class="row">
                        <div style="margin-left: -70px;" class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page fas fa-users"><b>&nbsp; RENOVAR PARCELA - ALUNOS</b></h1>
                            <div class="container-right">
                                <div class="container-dados"></div>
                                <a href="alunos.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Voltar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="conteudo-inner">
                <div class="container">
                    <div class="row">
                        <div style="margin-left: -90px;" class="col-12 mt-5 tab-item" id="categoria">
                            <div class="col-12" id="categorias">
                                <form id="formAssociado" action="updateServicoAluno.php" method="POST" style="zoom: 95%;">
                                    <div class="container-group mb-5">
                                        <div class="col-12 mb-4 card card-form socio">
                                            <div class="row">
                                                <div class="col-6 mb-2">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-2"><b>Serviço:</b></p>
                                                        <input type="text" name="servico_aluno" class="form-control mb-2" value="<?php echo htmlspecialchars($servico_aluno); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <p class="title-categoria mb-2"><b>Pago:</b></p>
                                                    <select name="pago_aluno" class="form-select form-control" aria-label="Default select example">
                                                        <option value="NÃO" <?php echo (isset($pago_aluno) && $pago_aluno === 'NÃO') ? 'selected' : ''; ?>>NÃO</option>
                                                        <option value="SIM" <?php echo (isset($pago_aluno) && $pago_aluno === 'SIM') ? 'selected' : ''; ?>>SIM</option>
                                                    </select>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-2"><b>Nome Completo:</b></p>
                                                        <input type="text" name="nome_aluno" class="form-control mb-2" value="<?php echo htmlspecialchars($nome_aluno); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <div class="form-group">
                                                        <p class="title-categoria mb-1"><b>Categoria:</b></p>
                                                        <select name="categoria_aluno" class="form-select form-control" aria-label="Default select example" id="pague">
                                                            <option selected value="...">Selecione a categoria:</option>
                                                            <option value="A" <?php echo $categoria_aluno == 'A' ? 'selected' : ''; ?>>A</option>
                                                            <option value="B" <?php echo $categoria_aluno == 'B' ? 'selected' : ''; ?>>B</option>
                                                            <option value="AB" <?php echo $categoria_aluno == 'AB' ? 'selected' : ''; ?>>AB</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <div class="form-group">
                                                        <p class="title-categoria mb-2"><b>Parcelas:</b></p>
                                                        <input type="text" name="numero_parcelas_aluno" class="form-control mb-2" value="<?php echo htmlspecialchars($numero_parcelas_aluno); ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <div class="form-group">
                                                        <p class="title-categoria mb-2"><b>Data de Pagamento:</b></p>
                                                        <input type="text" name="data_pagamento" class="form-control mb-2" value="<?php echo htmlspecialchars($data_pagamento_formatada); ?>" />
                                                    </div>
                                                </div>
                                            </div>
                                            <input type="hidden" name="aluno_id" value="<?php echo htmlspecialchars($aluno_id); ?>" />
                                            <div class="col-12 text-right">
                                            
                                            <button type="submit" class="btn btn-yellow btn-sm btn-proximo mt-4" style="float:right;">
                                                Renovar &nbsp;<i class="fas fa-check"></i>
                                            </button>
                                    
                                        </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
</body>
</html>
