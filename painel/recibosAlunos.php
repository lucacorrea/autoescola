<?php
session_start(); // Inicia a sessão

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

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

include "conexao.php";

// Obter valor da pesquisa
$search = isset($_GET['search']) ? $_GET['search'] : "";

// Query SQL com JOIN para pegar o id do aluno com base no RG
$sql = "SELECT servicos_aluno.*, alunos.id AS id_aluno 
        FROM servicos_aluno 
        JOIN alunos ON servicos_aluno.nome_aluno = alunos.nome 
        WHERE servicos_aluno.nome_aluno LIKE :search 
        OR servicos_aluno.categoria LIKE :search 
        OR alunos.rg LIKE :search";

$stmt = $conn->prepare($sql);
$search_term = "%" . $search . "%";
$stmt->bindParam(':search', $search_term, PDO::PARAM_STR);


$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Recibos</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="./css/modal.css">
    <link rel="stylesheet" href="./css/pesquisa.css">
</head>

<body>

<section class="bg-menu">

    <div class="conteudo" style="margin-left: -240px;">

        <div class="menu-top">
            <div class="container">
                <div class="row">
                    <div class="col-12 d-flex align-items-center mt-4">
                        <h1 class="title-page">
                            <b>
                                <i class="fas fa-receipt"></i>&nbsp; PAINEL - RECIBOS
                            </b>
                        </h1>

                        <div class="container-right">
                            <div class="container-dados">
                            </div>
                            <a href="alunos.php" class="btn btn-white btn-sm">
                                <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                            </a>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        <!-- Main -->
        <main class="main-container">
            <div class="main-title">
                <h2></h2>
            </div>
            <div class="conteudo-inner">
                <div class="container">
                    <div class="row">

                        <div class="col-12 cadastro">

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
                                                                    <input type="text" class="form-control" name="search" value="<?php echo htmlentities($search); ?>" placeholder="Pesquisar Recibos" />
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
                                                        <span class="me-2"><i class="fas fa-receipt"></i></span>
                                                        <b>Lista de Recibos</b>
                                                    </a>
                                                </div>
                                            </div>

                                            <div id="collapseOne" class="collapse show" data-parent="#categoriasMenu">
                                                <div class="lista-produtos" id="listaProdutos-one">
                                                    <table class="table mt-3 data-table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Nome</th>
                                                                <th scope="col">Categoria</th>
                                                                <th scope="col">Ações</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                                // Exibir resultados da pesquisa
                                                                if (!empty($result)) { // Verifica se $result possui dados
                                                                    if (count($result) > 0) {
                                                                        foreach ($result as $row) {
                                                                            echo "<tr>";
                                                                            echo "<td>" . htmlentities($row['nome_aluno']) . "</td>";
                                                                            echo "<td>" . htmlentities($row['categoria']) . "</td>";
                                                                            // Alterar o link para ir para recibo.php com o id do aluno da tabela 'alunos'
                                                                            echo "<td><a href='recibo.php?id=" . $row['id_aluno'] . "' class='icon-action'><i class='fas fa-eye'></i></a></td>";
                                                                            echo "</tr>";
                                                                        }
                                                                    } else {
                                                                        echo "<tr><td colspan='3'>Nenhum recibo encontrado.</td></tr>";
                                                                    }
                                                                } else {
                                                                    echo "<tr><td colspan='3'>Erro ao processar os dados da pesquisa.</td></tr>";
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

    <!-- Custom JS -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/logout.js"></script>
    <script src="./js/modal.js"></script>
    <script>
          function redirecionarSeRecarregar() {
            // Verifica se a página foi recarregada
            if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
                window.location.href = "recibosAlunos.php";
            }
        }
        window.onload = redirecionarSeRecarregar;

    </script>

</body>
</html>
