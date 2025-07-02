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

// Inclui a conexão com o banco de dados
include 'conexao.php';

// Obtendo o ID do usuário da sessão

$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    die("Usuário não autenticado.");
}

try {
    // Consulta para obter o nome e o email do usuário
    $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    // Verifica se encontrou resultados
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nome_usuario = $usuario['nome'];
        $email_usuario = $usuario['email'];
    } else {
        echo "Nenhum resultado encontrado.";
    }
} catch (PDOException $e) {
    echo "Erro ao executar a consulta: " . $e->getMessage();
}


// ID da associação
$id = 1; 

// Consulta para pegar a logo_image
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql); // Usando $conn (consistente com a conexão)
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// Verifica se encontrou algum dado
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o campo 'logo_image' está definido e não é vazio
$logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Instrutores/Placa</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="./css/modal.css">

</head>

<body>

    <section class="bg-menu">

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

                <a href="./feriadosCadastrados.php" class="menu-item">
                    <i class="fas fa-calendar-alt"></i> Feriados
                </a>

                <a href="./legislacao.php" class="menu-item">
                    <i class="fas fa-book-open"></i> Legislação
                </a>

                
                <a href="./alunos.php" class="menu-item">
                    <i class="fas fa-users"></i> Alunos
                </a>

                
                <a href="./instrutores.php" class="menu-item active">
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

        </div>

        <div class="conteudo">

            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">

                            <h1 class="title-page">
                                <b>
                                    <i class="fas fa-chalkboard-teacher"></i>&nbsp; PAINEL - INSTRUTOR/PLACA
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
            </div>

    <!-- Main -->
    <main class="main-container">
        <div class="main-title">
            <h2></h2>
        </div>
        <div class="conteudo-inner">
            <div class="container">
                <div class="row">


                    <div class="col-12 mt-0 cadastro" style="margin-top: -30px !important;">

                        <div class="menus-config mb-5">
                            <a href="instrutores.php" class="btn btn-white btn-sm">
                                <i class="fas fa-chalkboard-teacher"></i> Instrutores Cadastrados
                            </a>

                            <a href="cadastrarIntrutorPlaca.php" class="btn btn-white btn-sm active">
                                <i class="fas fa-plus"></i> Cadastrar Instru/Placa
                            </a>
                        </div>

                        <div class="col-12" id="categorias">

                            <form  action="processarInstrutorPlaca.php" method="POST" style="zoom: 90%;">
                                <div class="form-group mb-4 ">
                                    <p class="title-categoria mb-2">Nome do Instrutor:</p>
                                    <input type="text" class="form-control" name="nome_instrutor" oninput="this.value = this.value.toUpperCase()">
                                </div>
                                <div class="form-group mb-4">
                                    <p class="title-categoria mb-2">Placa:</p>
                                    <input type="text" class="form-control" name="placa" >
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

<!-- Custom JS -->
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
<script src="./js/script.js"></script>
<script src="./js/logout.js"></script>
<script src="./js/modal.js"></script>

</body>
</html>
