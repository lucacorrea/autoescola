<?php

    // SESSION
    session_start();

    // Função para verificar se o usuário está logado como administrador ou presidente
    function verificarAcesso() {
        if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
            // Se o usuário estiver logado, verifique se é admin ou presidente
            $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

            // Verificar se o nível de usuário é admin ou presidente
            if($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
                // O usuário tem permissão para acessar esta parte do sistema
                return true;
            }
        }
        
        // Se não estiver logado como admin ou presidente, redirecione-o para outra página
        header("Location: loader.php");
        exit(); // Encerra o script após o redirecionamento
    }

    verificarAcesso();
    // END SESSION


    // SESSION USER
    include 'conexao.php';

    $id = 1; 

    // Consulta para pegar a logo_image
    $sql = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql); // Altere de $pdo para $conn
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Verifica se encontrou algum dado
    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o campo 'logo_image' está definido e não é vazio
    $logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";

    try {
        // Obtém o ID do usuário da sessão
        $id_usuario = $_SESSION['id_usuario'];

        // Prepara a consulta para buscar o nome e o email do usuário
        $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
        $stmt = $conn->prepare($sql);
        
        // Vincula o parâmetro
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        // Executa a consulta
        $stmt->execute();

        // Verifica se há resultados
        if ($stmt->rowCount() > 0) {
            // Obtém os dados do usuário
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $nome_usuario = $usuario['nome'];
            $email_usuario = $usuario['email'];
        } else {
            echo "Nenhum resultado encontrado.";
        }
    } catch (PDOException $e) {
        // Trata erros de execução da consulta
        echo "Erro ao buscar os dados do usuário: " . $e->getMessage();
    }
    // END SESSION USER

?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <title>Painel - Feriados</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css" />
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/painel.css" />
        <link rel="stylesheet" href="./css/dashboard.css">
        <link rel="stylesheet" href="./css/modal.css">
        <!-- End custom CSS -->

    </head>

    <body>

        <!-- section -->
        <section class="bg-menu">

            <!-- menu-left -->
            <div class="menu-left">

                <div class="logo">
                <?php if (!empty($logoImage)): ?>
                    <img class="logo-admin" src="uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" width="100" alt="Logo">
                <?php else: ?>
                    
                <?php endif; ?>
                </div>

                <div class="menus">

                    <a href="./home.php" class="menu-item">
                        <i class="fas fa-home"></i> Início
                    </a>

                    <a href="./feriadosCadastrados.php" class="menu-item active">
                        <i class="fas fa-calendar-alt"></i> Feriados
                    </a>

                    <a href="./legislacao.php" class="menu-item">
                        <i class="fas fa-book-open"></i> Legislação
                    </a>

                    
                    <a href="./alunos.php" class="menu-item " >
                        <i class="fas fa-users"></i> Alunos
                    </a>

                    
                    <a href="./instrutores.php" class="menu-item">
                        <i class="fas fa-chalkboard-teacher"></i> Instrutores/Placa
                    </a>

                    <a href="./configuracoes.html" class="menu-item">
                        <i class="fas fa-cog"></i> Configurações
                    </a>

                    <a href="./relatorio.php" class="menu-item">
                        <i class="fas fa-donate"></i> Financeiro
                    </a>


                    <a href="./empresa.php" class="menu-item">
                        <i class="fas fa-building"></i> Empresa
                    </a>

                </div>

            </div>
            <!-- End menu-left -->

            <!-- conteudo -->
            <div class="conteudo">

                <!-- menu-top -->
                <div class="menu-top">

                    <!-- Container -->
                    <div class="container">

                        <div class="row">

                            <div class="col-12 d-flex align-items-center mt-4">

                                <h1 class="title-page">
                                    <b>
                                        <i class="fas fa-calendar-alt"></i>&nbsp; PAINEL - FERIADOS
                                    </b>
                                </h1>

                                <div class="container-right">

                                    <div class="container-dados">

                                        <p><?php echo $nome_usuario; ?></p>
                                        <?php if ($email_usuario) { ?>
                                            <span><?php echo $email_usuario; ?></span>
                                        <?php } ?>

                                    </div>
                                    <a href="logout.php" class="btn btn-white btn-sm">
                                        <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                    </a>

                                </div>

                            </div>

                        </div>

                    </div>
                    <!-- End container -->

                </div>
                <!-- End  menu-top -->

                <!-- Main -->
                <main class="main-container">
                    <div class="main-title">
                        <h2></h2>
                    </div>
                    <div class="conteudo-inner">
                        <div class="container">
                            <div class="row">


                                <div class="col-12 mt-0 cadastro">

                                    <div class="menus-config mb-5" style="margin-top: -27px;">
                                        <a href="feriadosCadastrados.php" class="btn btn-white btn-sm">
                                            <i class="fas fa-calendar-alt"></i> Feriados Cadastrados
                                        </a>
                                        <a href="cadastrarFeriado.php" class="btn btn-white btn-sm active">
                                            <i class="fas fa-calendar-plus"></i> Cadastrar Feriado
                                        </a>
                                    </div>

                                    <div class="col-12" id="categorias">

                                        <form  action="processarFeriado.php" method="POST" style="zoom: 90%;">
                                            <div class="form-group mb-4">
                                                <label for="feriado">Feriado:</label>
                                                <input type="date" class="form-control" id="feriado" name="feriado" required>
                                            </div>
                                            <div class="form-group">
                                                <label for="descricaoFeriado">Descrição:</label>
                                                <textarea class="form-control" id="descricaoFeriado" name="descricaoFeriado" rows="3" required></textarea>
                                            </div>
                                            <div class="col-12 text-right">
                                                <button type="submit" class="btn btn-yellow btn-sm mt-4 btn-proximo" style="float:right;">
                                                    <i class="fas fa-check"></i> &nbsp; Finalizar
                                                </button>
                                            </div>
                                        </form>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </main>
                <!-- End Main -->

            </div>
            <!-- End conteudo -->

        </section>
        <!-- End Section -->    

        <!-- Scripts-->
        <script src="../js/jquery.min.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
        <script src="./js/script.js"></script>
        <script src="./js/logout.js"></script>
        <script src="./js/modal.js"></script>
        <!-- End scripts -->

    </body>

</html>
