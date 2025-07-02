<?php

//------------------------SESSION-----------------------

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: loaderAluno.php");
        exit();
    }

//------------------------END SESSION-------------------


//------------------------GET DADOS ALUNOS--------------

    include "conexao.php";

    $userId = $_SESSION['user_id'];
    $sqlLoginAluno = "SELECT nome_aluno FROM login_aluno WHERE id = ?";
    $stmt = $conn->prepare($sqlLoginAluno);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $resultLoginAluno = $stmt->get_result();

    $nomeAlunoLogado = null;
    if ($resultLoginAluno->num_rows > 0) {
        $rowLoginAluno = $resultLoginAluno->fetch_assoc();
        $nomeAlunoLogado = $rowLoginAluno['nome_aluno'];
    }

    $parcelaAtrasada = false;
    $nenhumaParcelaAtrasada = true;
    $total_preco = 0;
    $exibirRenovacao = false;
    $ocultarRenovacaoHoje = false;

    $formaPagamento = '';

    if ($nomeAlunoLogado) {
        $sqlAluno = "SELECT nome FROM alunos WHERE nome = ?";
        $stmtAluno = $conn->prepare($sqlAluno);
        $stmtAluno->bind_param("s", $nomeAlunoLogado);
        $stmtAluno->execute();
        $resultAluno = $stmtAluno->get_result();

        if ($resultAluno->num_rows > 0) {
            $sqlServicos = "SELECT data_pagamento, preco, forma_pagamento, categoria FROM servicos_aluno WHERE nome_aluno = ?";
            $stmtServicos = $conn->prepare($sqlServicos);
            $stmtServicos->bind_param("s", $nomeAlunoLogado);
            $stmtServicos->execute();
            $resultServicos = $stmtServicos->get_result();

            if ($resultServicos->num_rows > 0) {
                while ($servico = $resultServicos->fetch_assoc()) {
                    $preco = $servico['preco'];
                    $formaPagamento = $servico['forma_pagamento'];
                    $dataPagamento = $servico['data_pagamento'];
                    $categoria = $servico['categoria'];

                    if ($preco > 0) {
                        $total_preco += $preco;
                    }

                    if ($formaPagamento === 'Parcelado') {
                        $dataAtual = strtotime(date('Y-m-d'));
                        $dataPagamentoTimestamp = strtotime($dataPagamento);

                        if ($dataPagamento != '0000-00-00' && $dataPagamentoTimestamp <= $dataAtual) {
                            $parcelaAtrasada = true;
                        }

                        if ($dataPagamentoTimestamp <= strtotime('+10 days', $dataAtual) && $dataPagamentoTimestamp > $dataAtual) {
                            $exibirRenovacao = true;
                        }

                        $ocultarRenovacaoHoje = ($dataPagamentoTimestamp == $dataAtual);
                    }
                }


                $nenhumaParcelaAtrasada = !$parcelaAtrasada;
            }
        } else {
            $nomeAlunoLogado = null;
        }
    }

    $conn->close();

//------------------------END GET DADOS ALUNOS----------

?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./painel/img/logo.png" type="image/x-icon">
        <title>Informações</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="./css/bootstrap.min.css" />
        <link rel="stylesheet" href="./css/fontawesome.css" />
        <link rel="stylesheet" href="./css/animate.css" />
        <link rel="stylesheet" href="./css/main.css" />
        <!-- End custom CSS -->

    </head>

    <body>

        <!-- bg-top -->
        <div class="bg-top pedido"></div>
        <!-- End bg-top -->

        <!-- header -->
        <header class="width-fix mt-3">

            <div class="card">

                <div class="d-flex">

                    <a href="./inicio.php" class="container-voltar">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <div class="infos text-center">
                        <h1 class="mb-0"><b>Informação</b></h1>
                    </div>

                </div>

            </div>

        </header>
        <!-- End header -->

        <!-- Section -->
        <section class="pedido width-fix mt-4">

            <?php if ($nomeAlunoLogado) : ?>

                <div class="card card-status-pedido mb-4">

                    <div class="detalhes-produto">

                        <div class="infos-produto">

                            <p class="name-total mb-0"><b>Valor Total</b></p>
                            <p class="price-total mb-0"><b><?php echo number_format($total_preco, 2, ',', '.'); ?></b></p>

                        </div>

                    </div>

                    <a href="contratoUser.php" class="detalhes-produto-acoes">

                        <i class="far fa-file-alt"></i>
                        <p class="mb-0 mt-1">Ver contrato</p>

                    </a>

                </div>

                <div class="card card-status-pedido mb-0">

                    <div class="detalhes-produto">

                        <div class="infos-produto">

                            <p class="name mb-0"><b>Categoria:</b></p>
                            <p class="price-total mb-0"><b><?php echo ($categoria) ?? ''; ?></b></p>

                        </div>

                    </div>

                </div>

                <a href="fichasAluno.php" class="card card-status-pedido mt-2">

                    <div class="img-icon-details parcela">
                        <i class="fas fa-calendar-alt"></i>
                    </div>

                    <div class="infos">
                        <p class="name mb-1"><b>Ver fichas</b></p>
                    </div>

                </a>

                <?php if ($formaPagamento === 'Parcelado' && $exibirRenovacao && !$ocultarRenovacaoHoje) : ?>

                    <div class="card card-status-pedido mb-3 cancelado alert-danger">

                        <div class="infos">

                            <p class="name mb-0"><b>Renovar Parcela!</b></p>
                            <span class="text mb-0">Prezado(a), gostaríamos de informar que sua parcela está prestes a expirar em <?php echo date('d/m/Y', $dataPagamentoTimestamp); ?>. Para renová-la, basta entrar em contato conosco via WhatsApp.</span>
                        
                        </div>

                        <a href="#" onclick="renovarParcela()" class="detalhes-produto-acoes">

                            <i class="fab fa-whatsapp"></i>
                            <p class="mb-0 mt-1">Mensagem</p>

                        </a>

                    </div>

                <?php endif; ?>


                <?php if ($formaPagamento === 'Parcelado' && $parcelaAtrasada) : ?>

                <div class="card card-status-pedido mt-2 cancelado alert-danger">

                    <div class="img-icon-details" style="background-color: #fff; color: red;">
                        <i class="fas fa-exclamation"></i>
                    </div>

                    <div class="infos">

                        <p class="name mb-1"><b>Parcela em atraso!</b></p>
                        <span class="text mb-0">Entre em contato ou dirija-se até a empresa.</span>

                    </div>

                    <a href="" onclick="renovarParcela()" class="detalhes-produto-acoes">

                        <i class="fab fa-whatsapp"></i>
                        <p class="mb-0 mt-1">Mensagem</p>

                    </a>

                </div>

                <a href="infoParcelas.php" class="card card-status-pedido mt-3">

                    <div class="img-icon-details parcela">
                        <i class="fas fa-divide"></i>
                    </div>

                    <div class="infos">
                        <p class="name mb-1"><b>Ver parcelas</b></p>
                    </div>

                </a>


            <?php elseif ($formaPagamento === 'Parcelado' && $nenhumaParcelaAtrasada) : ?>

                <div class="card card-status-pedido mt-2" style="background-color: #28a745 !important;">

                    <div class="img-icon-details parcela" style="background-color: #fff;">
                        <i class="fas fa-check"></i>
                    </div>

                    <div class="infos">
                        <p class="name mb-1"><b>Nenhuma parcela em atraso!</b></p>
                    </div>

                </div>
                
                <a href="infoParcelas.php" class="card card-status-pedido mt-3">

                    <div class="img-icon-details parcela">
                        <i class="fas fa-divide"></i>
                    </div>

                    <div class="infos">
                        <p class="name mb-1"><b>Ver parcelas</b></p>
                    </div>

                </a>

            <?php endif; ?>

            <?php else : ?>

                <div class="card card-status-pedido mt-2">
                    
                    <div class="img-icon-details">
                        <i class="fas fa-user"></i>
                    </div>

                    <div class="infos">

                        <p class="name mb-1"><b>Ainda não é aluno (a)?</b></p>
                        <span class="text mb-0">Entre em contato com a empresa.</span>

                    </div>

                    <a href="#" onclick="enviarMensagem()" class="detalhes-produto-acoes">

                        <i class="fab fa-whatsapp"></i>
                        <p class="mb-0 mt-1">Mensagem</p>

                    </a>
                </div>

            <?php endif; ?>

        </section>
        <!-- End Section -->

        <!-- Section 2 -->
        <section class="menu-bottom">
            
            <a href="./inicio.php" class="menu-bottom-item">
                <i class="fas fa-home"></i>
            </a>

            <a href="./info.php" class="menu-bottom-item active">
                <i class="fas fa-book"></i>
            </a>

            <a href="./user.php" class="menu-bottom-item">
                <i class="fas fa-user"></i>
            </a>

            <a href="./logoutAluno.php" class="menu-bottom-item">
                <i class="fas fa-sign-out-alt"> Sair</i>
            </a>

        </section>
        <!-- End Section 2 -->

        <!-- Scripts -->
        <script src="./js/enviarMensagemW.js"></script>
        <!-- End Scripts -->

    </body>

</html>
