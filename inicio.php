<?php

//----------------------------SESSION-----------------------

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: loaderAluno.php");
        exit();
    }

//----------------------------END SESSION--------------------


//---------------------------GET DADOS ALUNO-----------------

    include "./painel/conexao.php";

    if (!date_default_timezone_set('America/Manaus')) {
        die('Erro ao definir o fuso horário.');
    }

    $currentDay = date('w');  
    $currentTime = date('H:i:s');  

    $sql = "SELECT nome_associacao, sobre_associacao, logo_image FROM associacoes WHERE id = 1";
    $result = $conn->query($sql);

    $nomeAssociacao = $sobreAssociacao = $logoImage = '';

    if ($result && $result->rowCount() > 0) {
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $nomeAssociacao = $row['nome_associacao'] ?? '';
        $sobreAssociacao = $row['sobre_associacao'] ?? '';
        $logoImage = $row['logo_image'] ?? '';
    }


    $sqlHorarios = "
        SELECT * 
        FROM horarios 
        WHERE 
            (dia_inicio <= :currentDay AND dia_fim >= :currentDay) 
            OR (dia_inicio <= :currentDay AND dia_fim = -1) 
            OR (dia_inicio = -1 AND dia_fim >= :currentDay)
            OR (dia_inicio = -1 AND dia_fim = -1)
            OR (dia_inicio > dia_fim AND (dia_inicio <= :currentDay OR dia_fim >= :currentDay))
    ";
    $stmtHorarios = $conn->prepare($sqlHorarios);
    $stmtHorarios->bindParam(':currentDay', $currentDay, PDO::PARAM_INT);
    $stmtHorarios->execute();

    $isOpen = false;

    if ($stmtHorarios && $stmtHorarios->rowCount() > 0) {
        while ($row = $stmtHorarios->fetch(PDO::FETCH_ASSOC)) {
            $horaInicio1 = $row['hora_inicio_1'];
            $horaFim1 = $row['hora_fim_1'];
            $horaInicio2 = $row['hora_inicio_2'];
            $horaFim2 = $row['hora_fim_2'];

            if ($horaInicio1 <= $currentTime && $currentTime <= $horaFim1) {
                $isOpen = true;
                break;
            }

            if (!empty($horaInicio2) && !empty($horaFim2) && $horaInicio2 != '00:00:00' && $horaFim2 != '00:00:00') {
                if ($horaInicio2 <= $currentTime && $currentTime <= $horaFim2) {
                    $isOpen = true;
                    break;
                }
            }
        }
    }


    $userId = $_SESSION['user_id'];
    $sqlLoginAluno = "SELECT cpf_aluno FROM login_aluno WHERE id = :id";
    $stmtLoginAluno = $conn->prepare($sqlLoginAluno);
    $stmtLoginAluno->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmtLoginAluno->execute();

    $nomeAlunoLogado = null;
    if ($stmtLoginAluno->rowCount() > 0) {
        $rowLoginAluno = $stmtLoginAluno->fetch(PDO::FETCH_ASSOC);
        $nomeAlunoLogado = $rowLoginAluno['cpf_aluno'];
    }

    $sqlAluno = "SELECT cpf FROM alunos WHERE cpf = :cpf";
    $stmtAluno = $conn->prepare($sqlAluno);
    $stmtAluno->bindParam(':cpf', $nomeAlunoLogado, PDO::PARAM_STR);
    $stmtAluno->execute();

    $nomeAlunoCorrespondente = $stmtAluno->rowCount() > 0;

    $sqlMateriais = "SELECT id, titulo_material, nome_capa, nome_material FROM materiais_estudo";
    $resultMateriais = $conn->query($sqlMateriais);

    $conn = null;

//---------------------------END GET DADOS ALUNO-------------

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./painel/img/logo.png" type="image/x-icon">
        <title><?php echo htmlspecialchars($nomeAssociacao); ?></title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="./css/bootstrap.min.css" />
        <link rel="stylesheet" href="./css/fontawesome.css" />
        <link rel="stylesheet" href="./css/animate.css" />
        <link rel="stylesheet" href="./css/main.css" />
        <!-- End custom CSS -->

    </head>

    <body>

        <!-- bg-top -->
        <div class="bg-top">
        </div>
        <!-- End bg-top -->

        <!-- Header -->
        <header class="width-fix mt-5">

            <!-- card -->
            <div class="card">

                <div class="d-flex">

                    <div class="container-img" style="background-image: url('./painel/uploads/<?php echo htmlspecialchars($logoImage); ?>'); background-size: cover;"></div>
                    
                    <div class="infos">

                        <h1><b><?php echo htmlspecialchars($nomeAssociacao); ?></b></h1>

                        <div class="infos-sub">

                            <p class="status-open <?php echo $isOpen ? '' : 'closed'; ?>">
                                <i class="fa fa-clock"></i> <?php echo $isOpen ? 'Aberto' : 'Fechado'; ?>
                            </p>

                            <a href="./sobre.php" class="link">ver mais</a>

                        </div>

                    </div>

                </div>

            </div>
            <!-- End card -->

        </header>
        <!-- End Header -->

        <section class="categoria width-fix mt-4"></section>

        <!-- Section -->
        <section class="lista width-fix mt-0 pb-5" id="listaItensCardapio">
    
            <?php if (!$nomeAlunoCorrespondente): ?>

                <section class="categoria width-fix mt-2 mb-3">

                    <div class="card card-status-pedido">

                        <div class="img-icon-details">
                            <i class="fas fa-user"></i>
                        </div>

                        <div class="infos">

                            <p class="name mb-1"><b>Ainda não é aluno (a)?</b></p>
                            <span class="text mb-0">Entre em contato com a empresa</span>

                        </div>

                        <a href="#" onclick="enviarMensagem()" class="detalhes-produto-acoes">

                            <i class="fab fa-whatsapp"></i>
                            <p class="mb-0 mt-1">Mensagem</p>

                        </a>

                    </div> 
                    
                </section>
                
            <?php endif; ?>
        


            <!-- container-group -->
            <div class="container-group mb-5 <?php echo $nomeAlunoCorrespondente ? '' : 'hidden'; ?>">
                
                <p class="title-categoria"><b>Guia de estudo</b></p>

                <?php
                    // Verifica se há materiais cadastrados
                    if ($resultMateriais->rowCount() > 0) {
                        while ($row = $resultMateriais->fetch(PDO::FETCH_ASSOC)) {
                            echo '<div class="card mb-2 item-cardapio" style="cursor: default !important;">';
                            echo '  <div class="d-flex">';
                            echo '    <div class="container-img-produto" style="background-color:#fff; border: 1.5px solid #f8f8bb; background-image: url(\'./painel/uploads/' . htmlspecialchars($row['nome_capa']) . '\'); background-size: cover;"></div>';
                            echo '    <div class="infos-produto">';
                            echo '      <p class="name"><b>' . htmlspecialchars($row['titulo_material']) . '</b></p>';
                            echo '      <p class="description">&nbsp;</p>';
                            echo '      <a href="./painel/uploads/' . htmlspecialchars($row['nome_material']) . '" class="price" style="color: var(--color-primary) !important;"><b>Fazer download</b></a>';
                            echo '    </div>';
                            echo '  </div>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>Nenhum material encontrado.</p>';
                    }
                ?>

            </div>
            <!-- End container-group -->
             
        </section>
        <!-- End Section -->

        <!-- Section 2 -->
        <section class="menu-bottom" id="menu-bottom">

            <a href="./inicio.php" class="menu-bottom-item active">
                <i class="fas fa-home"></i>
            </a>

            <a href="./info.php" class="menu-bottom-item">
                <i class="fas fa-book"></i>
            </a>

            <a href="./user.php" class="menu-bottom-item">
                <i class="fas fa-user"></i>
            </a>

            <a href="./logoutAluno.php" class="menu-bottom-item">
                <i class="fas fa-sign-out-alt"> Sair</i>
            </a>
            
        </section>
        <!-- End Section -->

        <!-- Scripts -->
        <script type="text/javascript" src="./js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="./js/cardapio.js"></script>
        <script type="text/javascript" src="./js/enviarMensagem.js"></script>
        <!-- End Scripts -->
    
    </body>
    
</html>
