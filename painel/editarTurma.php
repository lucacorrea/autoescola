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


// Incluir a conexão com o banco de dados
include 'conexao.php';  // Verifique se o caminho está correto

// Verifica se o ID foi passado pela URL
if (isset($_GET['id'])) {
    $id_turma = $_GET['id'];

    // Busca os dados da turma pelo ID usando PDO
    $sql = "SELECT * FROM turmas WHERE id = :id_turma";
    $stmt = $conn->prepare($sql);
    
    // Bind do parâmetro
    $stmt->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
    
    // Executa a consulta
    $stmt->execute();
    
    // Se encontrar a turma, armazena os dados
    if ($stmt->rowCount() > 0) {
        $turma = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        // Mensagem de erro caso não encontre a turma
        echo "<script>alert('Turma não encontrada.'); window.location.href='legislacao.php';</script>";
        exit();
    }
} else {
    // Mensagem de erro caso o ID da turma não seja especificado
    echo "<script>alert('ID da turma não especificado.'); window.location.href='legislacao.php';</script>";
    exit();
}

// Busca os instrutores para o campo de seleção usando PDO
$query_instrutores = "SELECT id, nome_instrutor FROM instrutores";
$result_instrutores = $conn->query($query_instrutores);

if (!$result_instrutores) {
    // Mensagem de erro ao buscar instrutores
    die('Erro ao buscar instrutores: ' . $conn->errorInfo()[2]);
}

// Fechar a conexão PDO
$conn = null;
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Legislação</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css">
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
</head>


<body>

    <div class="container-mensagens" id="container-mensagens">
    </div>

    <div class="loader-full animated fadeIn hidden">
        <img src="../img/loader.png" width="100" class="animated pulse infinite" />
    </div>

    <section class="bg-menu">
        <div class="conteudo" style="margin-left: -240px;">
            <section class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page fas fa-users"><b>&nbsp; EDITAR TURMA</b></h1>
                            <div class="container-right">
                                <div class="container-dados">
                                </div>
                                <a href="legislacao.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="conteudo-inner">
                <div class="container">
                    <div class="row">
                        <div class="col-12 mt-5 tab-item" id="categoria">
                            <div class="col-12" id="categorias">
                                <form id="formAssociado" action="updateTurma.php" method="POST" style="zoom: 95%;">
                                    <input type="hidden" name="id_turma" value="<?php echo $turma['id']; ?>" />
                                    <div class="container-group mb-5">
                                        <div class="col-12 mb-4 card card-form socio">
                                            <div class="row">
                                                <div class="col-4 mb-2">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-2"><b>Local:</b></p>
                                                        <input type="text" name="local" class="form-control mb-2" value="<?php echo htmlspecialchars($turma['local']); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                    </div>
                                                </div>
                                                <div class="col-4 mb-2"></div>
                                                <div class="col-4 mb-2"></div>
                                                <div class="col-4 mb-2">
                                                    <div class="form-group container-cep mb-4">
                                                        <p class="title-categoria mb-2"><b>Instrutor:</b></p>
                                                        <select name="instrutor" class="form-control">
                                                            <option value="">Selecione o instrutor</option>
                                                            <?php
                                                            // Verifica se o resultado dos instrutores está disponível
                                                            while ($instrutor = $result_instrutores->fetch(PDO::FETCH_ASSOC)): ?>
                                                                <option value="<?= htmlspecialchars($instrutor['nome_instrutor']) ?>" <?= $instrutor['nome_instrutor'] == $turma['instrutor'] ? 'selected' : ''; ?>>
                                                                    <?= htmlspecialchars($instrutor['nome_instrutor']) ?>
                                                                </option>
                                                            <?php endwhile; ?>
                                                        </select>

                                                    </div>
                                                </div>
                                                <div class="col-4 mb-2">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-2"><b>Data Inicial:</b></p>
                                                        <input type="date" name="data_inicio" class="form-control" value="<?php echo $turma['data_inicio']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-4 mb-3">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-1"><b>Data Final:</b></p>
                                                        <input type="date" name="data_fim" class="form-control" value="<?php echo $turma['data_fim']; ?>" />
                                                    </div>
                                                </div>
                                                <div class="col-4 mb-3">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-1"><b>Horário Inicial:</b></p>
                                                        <input type="time" name="horario_inicio" class="form-control" value="<?php echo $turma['horario_inicio']; ?>" oninput="this.value = this.value.toUpperCase()" />
                                                    </div>
                                                </div>
                                                <div class="col-4 mb-3">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-1"><b>Horário Final:</b></p>
                                                        <input type="time" name="horario_fim" class="form-control" value="<?php echo $turma['horario_fim']; ?>" oninput="this.value = this.value.toUpperCase()" />
                                                    </div>
                                                </div>
                                                <div class="col-4 mb-3">
                                                    <div class="form-group container-cep mb-4">
                                                        <p class="title-categoria mb-1"><b>Turno:</b></p>
                                                        <select class="form-control" id="turno" name="turno">
                                                            <option value="">Selecione o turno</option>
                                                            <option value="Matutino" <?= $turma['turno'] == 'Matutino' ? 'selected' : ''; ?>>Matutino</option>
                                                            <option value="Vespertino" <?= $turma['turno'] == 'Vespertino' ? 'selected' : ''; ?>>Vespertino</option>
                                                            <option value="Noturno" <?= $turma['turno'] == 'Noturno' ? 'selected' : ''; ?>>Noturno</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 text-right">
                                                    <button type="submit" class="btn btn-yellow next btn-sm btn-proximo mt-4" style="float:right;">
                                                        Finalizar &nbsp;<i class="fas fa-check"></i>
                                                    </button>
                                                </div>
                                                </div>
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

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/fontawesome.js"></script>
    <script src="../js/main.js"></script>
</body>

</html>
