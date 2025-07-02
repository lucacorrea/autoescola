<?php

    // SESSION
    session_start();

    // Função para verificar se o usuário está logado como administrador ou presidente
    function verificarAcesso() {
        if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
            // Se o usuário estiver logado, verifique se é admin ou presidente
            $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

            // Verificar se o nível de usuário é admin, presidente ou suporte
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

    //SESSION USER
    include 'conexao.php';

    // ID da associação
    $id_associacao = 1; // ID da associação (fixo)
    $query = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id_associacao, PDO::PARAM_INT);
    $stmt->execute();
    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

    $logoImage = $associacao['logo_image'] ?? ""; // Verifica se o campo logo_image existe e não é vazio

    // Obtém o ID do usuário da sessão
    $id_usuario = $_SESSION['id_usuario'] ?? null;

    if ($id_usuario) {
        try {
            // Consulta para buscar nome e email
            $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
            $stmt->execute();

            // Verifica se há resultados
            if ($stmt->rowCount() > 0) {
                // Obtém os dados do usuário
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
                $nome_usuario = $usuario['nome'];
                $email_usuario = $usuario['email'];
            } else {
                echo "Nenhum usuário encontrado.";
            }
        } catch (PDOException $e) {
            echo "Erro na consulta: " . $e->getMessage();
        }
    } else {
        echo "Usuário não autenticado.";
    }

    //END SESSION USER

    // Inicializa a variável de pesquisa
    $search = "";

    // Verifica se foi feita uma pesquisa
    if (isset($_GET['search'])) {
        $search = $_GET['search'];
    }

    try {
        // Monta a consulta SQL para obter os feriados cadastrados
        $sql = "SELECT id, data, descricao FROM feriados";
        
        // Adiciona o filtro de pesquisa, se necessário
        if (!empty($search)) {
            $sql .= " WHERE descricao LIKE :search";
        }

        // Prepara a consulta
        $stmt = $conn->prepare($sql);

        // Vincula o parâmetro de pesquisa, se aplicável
        if (!empty($search)) {
            $searchParam = '%' . $search . '%';
            $stmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
        }

        // Executa a consulta
        $stmt->execute();

        // Obtém os resultados
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Erro ao buscar os dados: " . $e->getMessage());
    }

?>


<!DOCTYPE html>
<html lang="pt-br">
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
        <link rel="stylesheet" href="./css/pesquisa.css">
        <!-- End custom CSS -->

    </head>

    <body>

        <!-- Section -->
        <section class="bg-menu">

            <!-- menu-left -->
            <div class="menu-left">

                <!-- logo -->
                <div class="logo">

                    <?php if (!empty($logoImage)): ?>
                        <img class="logo-admin" src="uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" width="100" alt="Logo">
                    <?php else: ?>
                        
                    <?php endif; ?>

                </div>
                <!-- End logo -->

                <!-- menus -->
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
                    
                    <a href="./alunos.php" class="menu-item">
                        <i class="fas fa-users"></i> Alunos
                    </a>
                    
                    <a href="./instrutores.php" class="menu-item">
                        <i class="fas fa-chalkboard-teacher"></i> Instrutores/Placa
                    </a>

                    <a href="./configuracoes.php" class="menu-item">
                        <i class="fas fa-cog"></i> Configurações
                    </a>

                    <a href="./relatorio.php" class="menu-item">
                        <i class="fas fa-donate"></i> Financeiro
                    </a>

                    <a href="./empresa.php" class="menu-item">
                        <i class="fas fa-building"></i> Empresa
                    </a>

                </div>
                <!-- End menus -->

            </div>
            <!-- End menu-left -->

            <!-- conteudo -->
            <div class="conteudo">

                <!-- menu-top -->
                <div class="menu-top">

                    <!-- container -->
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
                <!-- End menu-top -->

                <!-- Main -->
                <main class="main-container">

                    <div class="main-title">

                        <h2></h2>

                    </div>

                    <div class="conteudo-inner">

                        <div class="container">

                            <div class="row">

                                <div class="col-12 cadastro">

                                    <div class="menus-config mb-5 mt-3">

                                        <a href="feriadosCadastrados.php" class="btn btn-white btn-sm active">
                                            <i class="fas fa-calendar-alt"></i> Feriados Cadastrados
                                        </a>

                                        <a href="cadastrarFeriado.php" class="btn btn-white btn-sm">
                                            <i class="fas fa-calendar-plus"></i> Cadastrar Feriado
                                        </a>

                                    </div>

                                    <div class="col-12" id="categorias">

                                        <div class="container-group mb-5">

                                            <div class="accordion" id="categoriasMenu">

                                                <div class="row">

                                                    <div class="col-md-12 mb-2">

                                                        <div class="card-search">

                                                            <div class="card-body">

                                                                <form method="GET" class="row g-3 align-items-center">

                                                                    <div class="col-md-5">

                                                                        <div class="container-cep">

                                                                            <input type="text" class="form-control" name="search" value="<?php echo htmlentities($search); ?>" placeholder="Pesquisar Feriados" />
                                                                            
                                                                            <div class="search">

                                                                                <button type="submit" class="btn btn-search active">
                                                                                    <i class="fa fa-search"></i>
                                                                                </button>

                                                                            </div>

                                                                        </div>

                                                                    </div>

                                                                </form>

                                                            </div>

                                                        </div>

                                                    </div>

                                                </div>

                                                <div class="mt-3 card-table">

                                                    <div class="card-drag" id="headingOne">

                                                        <div class="infos">

                                                            <a href="#" class="name-table mb-0" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                <span class="me-2"><i class="fas fa-calendar-alt"></i></span>
                                                                <b>Lista de Feriados</b>
                                                            </a>

                                                        </div>

                                                    </div>

                                                    <div id="collapseOne" class="collapse show" data-parent="#categoriasMenu">

                                                        <div class="lista-produtos" id="listaProdutos-one">

                                                            <table class="table mt-3 data-table">

                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">Data</th>
                                                                        <th scope="col">Descrição</th>
                                                                        <th scope="col">Ações</th>
                                                                    </tr>
                                                                </thead>

                                                                <tbody>

                                                                    <?php
                                                                    if (!empty($result)) {
                                                                        // Loop através dos resultados e cria as linhas da tabela
                                                                        foreach ($result as $row) {
                                                                            echo "<tr>";
                                                                            echo "<td>" . date("d/m/Y", strtotime($row["data"])) . "</td>";
                                                                            echo "<td style='width: 600px;'>" . htmlspecialchars($row["descricao"]) . "</td>";
                                                                            echo "<td>";
                                                                            echo "<div class='actions'>";
                                                                            echo '<a href="editarFeriado.php?id=' . $row["id"] . '" class="icon-action"><i class="fas fa-pencil-alt"></i></a>';
                                                                            echo '<button onclick="openModal(' . $row["id"] . ')" class="btn icon-action"><i class="fas fa-trash-alt"></i></button>';
                                                                            echo "</div>";
                                                                            echo "</td>";
                                                                            echo "</tr>";

                                                                            // Modal de exclusão
                                                                            echo '<div id="confirmDeleteHorarioModal' . $row["id"] . '" class="modal-horarios" style="display: none;">';
                                                                            echo '<div class="modal-horarios-content">';
                                                                            echo '<div class="modal-horarios-header">';
                                                                            echo '<span class="close" onclick="closeModal(' . $row["id"] . ')">&times;</span>';
                                                                            echo '<h2 class="title-modal"><i class="fas fa-trash-alt"></i> Excluir Feriado</h2>';
                                                                            echo '</div>';
                                                                            echo '<p class="p mt-5">Tem certeza que deseja excluir este feriado?</p>';
                                                                            echo '<button class="excluir excluir-secondary" onclick="closeModal(' . $row["id"] . ')">';
                                                                            echo '<span class="text">Não</span>';
                                                                            echo '</button>';
                                                                            echo '<a href="excluirFeriado.php?id=' . $row["id"] . '" class="excluir">Sim</a>';
                                                                            echo '</div>';
                                                                            echo '</div>';
                                                                        }
                                                                    } else {
                                                                        echo "<tr><td colspan='3'>Nenhum feriado cadastrado.</td></tr>";
                                                                    }
                                                                    ?>

                                                                </tbody>

                                                            </table>

                                                        </div>

                                                    </div>

                                                </div>  

                                            </div>

                                        </div>

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
        <!-- End section -->

        <!-- Scripts -->
        <script src="../js/jquery.min.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
        <script src="./js/script.js"></script>
        <script src="./js/logout.js"></script>
        <script src="./js/modal.js"></script>
        <script>

            function openModal(id) {
                console.log('Abrindo modal com ID:', id);
                var modal = document.getElementById('confirmDeleteHorarioModal' + id);
                console.log('Modal:', modal);
                if (modal) {
                    modal.style.display = 'block';
                    adjustTableOpacity(0); // Define a opacidade para 50%
                } else {
                    console.error('Modal não encontrada.');
                }
            }

            function closeModal(id) {
                console.log('Fechando modal com ID:', id);
                var modal = document.getElementById('confirmDeleteHorarioModal' + id);
                if (modal) {
                    modal.style.display = 'none';
                    adjustTableOpacity(1); // Retorna a opacidade ao normal (100%)
                } else {
                    console.error('Modal não encontrada.');
                }
            }

            function adjustTableOpacity(opacity) {
                var thElements = document.querySelectorAll('.table-responsive thead th');
                thElements.forEach(function(th) {
                    th.style.opacity = opacity;
                });
            }

            function redirecionarSeRecarregar() {
            // Verifica se a página foi recarregada
            if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
                window.location.href = "feriadosCadastrados.php";
            }
        }


        window.onload = redirecionarSeRecarregar;


        </script>
        <!-- End Scripts -->

    </body>

</html>
