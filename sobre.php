<?php

//------------------------SESSION------------------------

    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: loaderAluno.php");
        exit();
    }

//------------------------END SESSION--------------------

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./painel/logo.png" type="image/x-icon">
        <title>Autoescola Dinâmica</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="./css/bootstrap.min.css" />
        <link rel="stylesheet" href="./css/fontawesome.css" />
        <link rel="stylesheet" href="./css/animate.css" />
        <link rel="stylesheet" href="./css/main.css" />
        <!-- End custom CSS -->

    </head>

    <style>

        .sobre{
            text-align: justify;
        }

    </style>


    <body>

        <!-- bg-top -->
        <div class="bg-top sobre"></div>
        <!-- End bg-top -->

        <!-- header -->
        <header class="width-fix mt-3">

            <div class="card">

                <div class="d-flex">

                    <a href="./inicio.php" class="container-voltar">
                        <i class="fas fa-arrow-left"></i>
                    </a>

                    <div class="infos text-center">

                        <h1 class="mb-0"><b>Sobre a Empresa</b></h1>

                    </div>

                </div>

            </div>

        </header>
        <!-- header -->

        <!-- Section -->
        <section class="width-fix mt-5 mb-4">

            <div class="card">

                <?php
                    include './painel/conexao.php';

                    try {
                        $associacao_query = "SELECT nome_associacao, sobre_associacao, logo_image FROM associacoes LIMIT 1";
                        $stmt = $conn->prepare($associacao_query);
                        $stmt->execute();

                        if ($stmt->rowCount() > 0) {
                            $associacao = $stmt->fetch(PDO::FETCH_ASSOC);
                            $nome_associacao = $associacao['nome_associacao'];
                            $sobre_associacao = $associacao['sobre_associacao'];
                            $logo_image = $associacao['logo_image'] ?? '';
                        } else {
                            $nome_associacao = "Nome da Associação";
                            $sobre_associacao = "Descrição da associação não disponível.";
                            $logo_image = "";
                        }
                    } catch (PDOException $e) {
                        die("Erro ao consultar a associação: " . $e->getMessage());
                    }
                ?>

                <div class="d-flex">

                    <div class="container-img-sobre"style="background-image: url('./painel/uploads/<?php echo htmlspecialchars($logo_image); ?>'); background-size: 70%;">
                    </div>

                    <div class="infos">

                        <h1 class="title-sobre"><b><?php echo htmlspecialchars($nome_associacao); ?></b></h1>

                        <div class="infos-sub">

                            <p class="sobre mb-2">
                                <?php echo nl2br(htmlspecialchars($sobre_associacao)); ?>
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </section>
        <!-- End Section -->

        <!-- Section 2 -->
        <section class="lista width-fix mt-3 pb-5">

            <!-- container-group 1 -->
            <div class="container-group mb-4">

                <p class="title-categoria mb-0">
                    <i class="fas fa-map-marker-alt"></i>&nbsp; <b>Endereço</b>
                </p>

                <div class="card mt-2">

                    <?php

                        include './painel/conexao.php';

                        try {
                            $endereco_query = "SELECT endereco, bairro, numero, cidade, uf, cep FROM enderecos LIMIT 1";
                            $stmt = $conn->prepare($endereco_query);
                            $stmt->execute();

                            if ($stmt->rowCount() > 0) {
                                $endereco = $stmt->fetch(PDO::FETCH_ASSOC);
                                $endereco_completo = $endereco['endereco'] . ", " . $endereco['numero'] . " - " . $endereco['bairro'] . ", " . $endereco['cidade'] . "/" . $endereco['uf'] ." -  CEP:  " . $endereco['cep'];
                            } else {
                                $endereco_completo = "Endereço não disponível.";
                            }
                        } catch (PDOException $e) {
                            die("Erro ao consultar o endereço: " . $e->getMessage());
                        }

                    ?>

                    <p class="normal-text mb-0"><?php echo htmlspecialchars($endereco_completo); ?></p>
                </div>

            </div>
            <!-- End container-group 1 -->

            <!-- container-group 2 -->
            <div class="container-group mb-4">

                <p class="title-categoria mb-0">
                    <i class="fas fa-building"></i>&nbsp; <b>CNPJ</b>
                </p>

                <div class="card mt-2">

                    <?php

                        include './painel/conexao.php';

                        try {
                            $endereco_query = "SELECT cnpj FROM enderecos LIMIT 1";
                            $stmt = $conn->prepare($endereco_query);
                            $stmt->execute();

                            if ($stmt->rowCount() > 0) {
                                $endereco = $stmt->fetch(PDO::FETCH_ASSOC);
                                $endereco_completo = $endereco['cnpj'];
                            } else {
                                $endereco_completo = "CNPJ não disponível.";
                            }
                        } catch (PDOException $e) {
                            die("Erro ao consultar o CNPJ: " . $e->getMessage());
                        }

                    ?>


                    <p class="normal-text mb-0"><?php echo htmlspecialchars($endereco_completo); ?></p>
                </div>

            </div>
            <!-- End container group 2 -->

            <!-- container group 3 -->
            <div class="container-group mb-4">

                <p class="title-categoria mb-0">
                    <i class="fas fa-phone"></i>&nbsp; <b>Telefone</b>
                </p>

                <div class="card mt-2">
                
                    <?php

                        include './painel/conexao.php';

                        try {
                            $endereco_query = "SELECT telefone FROM enderecos  LIMIT 1";
                            $stmt = $conn->prepare($endereco_query);
                            $stmt->execute();

                            if ($stmt->rowCount() > 0) {
                                $endereco = $stmt->fetch(PDO::FETCH_ASSOC);
                                $endereco_completo = $endereco['telefone'];
                            } else {
                                $endereco_completo = "Telefone não disponível..";
                            }
                        } catch (PDOException $e) {
                            die("Erro ao consultar o Telefone: " . $e->getMessage());
                        }

                    ?>

                    <p class="normal-text mb-0"><?php echo htmlspecialchars($endereco_completo); ?></p>

                </div>

            </div>
            <!-- End container group 3 -->

            <!-- container-group 4 -->
            <div class="container-group mb-4">

                <p class="title-categoria mb-0">
                    <i class="fas fa-clock"></i>&nbsp; <b>Horário de funcionamento</b>
                </p>

                <?php

                    function formatarHora($hora) {
                        return !empty($hora) ? date("H:i", strtotime($hora)) : "";
                    }

                    include './painel/conexao.php';

                    try {
                        $horarios_query = "SELECT dia_inicio, dia_fim, hora_inicio_1, hora_fim_1, hora_inicio_2, hora_fim_2 FROM horarios";
                        $stmt = $conn->prepare($horarios_query);
                        $stmt->execute();

                        $dias_da_semana = ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"];

                        if ($stmt->rowCount() > 0) {
                            while ($horario = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                $dia_inicio = $dias_da_semana[$horario['dia_inicio']] ?? "";
                                $dia_fim = $dias_da_semana[$horario['dia_fim']] ?? "";
                                $hora_inicio_1 = formatarHora($horario['hora_inicio_1']);
                                $hora_fim_1 = formatarHora($horario['hora_fim_1']);
                                $hora_inicio_2 = formatarHora($horario['hora_inicio_2']);
                                $hora_fim_2 = formatarHora($horario['hora_fim_2']);

                                if ($dia_inicio === $dia_fim) {
                                    $dias_texto = $dia_inicio;
                                } else {
                                    $dias_texto = "$dia_inicio à $dia_fim";
                                }

                                echo "<div class='card mt-2'>";
                                echo "<p class='normal-text mb-0'><b>$dias_texto</b></p>";
                                echo "<p class='normal-text mb-0'>$hora_inicio_1 às $hora_fim_1";

                                if ($hora_inicio_2 && $hora_fim_2) {
                                    echo "<br>$hora_inicio_2 às $hora_fim_2";
                                }

                                echo "</p>";
                                echo "</div>";
                            }
                        } else {
                            echo "<div class='card mt-2'><p class='normal-text mb-0'>Horário não disponível.</p></div>";
                        }
                    } catch (PDOException $e) {
                        die("Erro ao consultar os horários: " . $e->getMessage());
                    }

                ?>

            </div>
            <!-- End container-group 4 -->

            <!-- container-group 5 -->
            <div class="container-group mb-5">

                <p class="title-categoria mb-0">
                    <i class="fas fa-coins"></i>&nbsp; <b>Formas de pagamento</b>
                </p>
            
                <?php

                    include './painel/conexao.php';

                    try {
                        $sql = "SELECT forma FROM formas_pagamento";
                        $stmt = $conn->prepare($sql);
                        $stmt->execute();

                        if ($stmt->rowCount() > 0) {
                            while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                echo '<div class="card mt-2">';
                                echo '<p class="normal-text mb-0"><b>' . $row["forma"] . '</b></p>';
                                echo '</div>';
                            }
                        } else {
                            echo '<p class="normal-text">Nenhuma forma de pagamento disponível.</p>';
                        }
                    } catch (PDOException $e) {
                        die("Erro ao consultar as formas de pagamento: " . $e->getMessage());
                    }
                    
                ?>

            </div>
            <!-- End container-group 5 -->

            <a href="./inicio.php" class="btn btn-yellow btn-full voltar">
                Voltar para o início
            </a>

        </section>
        <!-- End Section 2 -->

        <!-- Scripts -->
        <script type="text/javascript" src="./js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="./js/item.js"></script>
        <!-- End Scripts -->

    </body>

</html>
