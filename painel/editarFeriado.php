<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador, presidente ou suporte
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel']; // Nível de usuário armazenado na sessão

        // Verificar se o nível de usuário é admin, presidente ou suporte
        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true;
        }
    }

    // Redireciona caso o usuário não tenha permissão
    header("Location: loader.php");
    exit();
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

include 'conexao.php'; // Inclui a conexão com o banco de dados

// Verificar se o ID do feriado foi passado
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    try {
        // Consultar o feriado com base no ID
        $sql = "SELECT * FROM feriados WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        // Verificar se o feriado existe
        if ($stmt->rowCount() > 0) {
            $feriado = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "Feriado não encontrado.";
            exit();
        }
    } catch (PDOException $e) {
        echo "Erro ao consultar o feriado: " . $e->getMessage();
        exit();
    }
} else {
    echo "ID do feriado não fornecido.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Editar Feriado</title>
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
                                <b><i class="fas fa-calendar-alt"></i>&nbsp; EDITAR FERIADO</b>
                            </h1>
                            <div class="container-right">
                                <a href="feriadosCadastrados.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main -->
            <main class="main-container">
                <div class="main-title"></div>
                <div class="conteudo-inner">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 mt-0 cadastro">
                                <div class="col-12 mt-5" id="categorias">
                                    <form class="col-md-10" action="updateFeriado.php" method="POST" style="zoom: 90%; margin: 0 auto;">
                                        <input type="hidden" name="id" value="<?php echo $feriado['id']; ?>" />
                                        <div class="form-group mb-4">
                                            <label for="feriado">Feriado:</label>
                                            <input type="date" class="form-control" id="feriado" name="feriado" value="<?php echo $feriado['data']; ?>" required>
                                        </div>
                                        <div class="form-group">
                                            <label for="descricaoFeriado">Descrição:</label>
                                            <textarea class="form-control" id="descricaoFeriado" name="descricaoFeriado" rows="3" required><?php echo $feriado['descricao']; ?></textarea>
                                        </div>
                                        <div class="col-12 text-right">
                                            <button type="submit" class="btn btn-yellow btn-sm mt-4 btn-proximo" style="float:right;">
                                                <i class="fas fa-check"></i> &nbsp; Atualizar
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
    </section>

    <!-- Custom JS -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/logout.js"></script>
    <script src="./js/modal.js"></script>
</body>
</html>
