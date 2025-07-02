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
 include 'conexao.php';
// Verifique se o ID foi passado na URL
if (isset($_GET['id'])) {
    $id_instrutor = $_GET['id'];

    // Prepare a consulta SQL para buscar os dados do instrutor
    $sql = "SELECT nome_instrutor, placa_instrutor FROM instrutores WHERE id = :id";
    
    // Prepara a consulta
    $stmt = $conn->prepare($sql);
    
    // Bind do parâmetro
    $stmt->bindParam(':id', $id_instrutor, PDO::PARAM_INT);
    
    // Executa a consulta
    $stmt->execute();
    
    // Recupera o resultado
    $instrutor = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($instrutor) {
        $nome_instrutor = $instrutor['nome_instrutor'];
        $placa_instrutor = $instrutor['placa_instrutor'];
    } else {
        // Se o instrutor não for encontrado
        echo "Instrutor não encontrado.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Editar Instrutor</title>

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

        <div class="conteudo" style="margin-left: -240px;">

            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">

                            <h1 class="title-page">
                                <b>
                                    <i class="fas fa-chalkboard-teacher"></i>&nbsp; EDITAR INSTRUTOR/PLACA
                                </b>
                            </h1>

                            <div class="container-right">
                                <div class="container-dados">
                                </div>
                                <a href="instrutores.php" class="btn btn-white btn-sm">
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

                            <div class="col-12 mt-0 cadastro">
                                <div class="col-12 mt-5" id="categorias">

                                    <!-- Formulário de edição de instrutor -->
                                    <form class="col-md-10" action="updateInstrutorPlaca.php" method="POST" style="zoom: 90%; margin: 0 auto;">
                                        <!-- Campo oculto para enviar o ID do instrutor -->
                                        <input type="hidden" name="id_instrutor" value="<?php echo $id_instrutor; ?>">

                                        <div class="form-group mb-4">
                                            <p class="title-categoria mb-2">Nome do Instrutor:</p>
                                            <input type="text" class="form-control" name="nome_instrutor" value="<?php echo htmlspecialchars($nome_instrutor); ?>" oninput="this.value = this.value.toUpperCase()" required>
                                        </div>

                                        <div class="form-group mb-4">
                                            <p class="title-categoria mb-2">Placa:</p>
                                            <input type="text" class="form-control" name="placa_instrutor" value="<?php echo htmlspecialchars($placa_instrutor); ?>" required>
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

    </section>
</body>
</html>
