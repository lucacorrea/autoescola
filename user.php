<?php

include "./painel/conexao.php";

$id = 1; 
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);


$logoImage = $associacao['logo_image'] ?? '';

session_start(); // Inicia a sessão

// Verifica se o aluno está logado
if (!isset($_SESSION['user_id'])) { // Atualizado para 'user_id'
    // Redireciona para a página de login se o usuário não estiver logado
    header("Location: loaderAluno.php");
    exit();
}

// Verifica se o 'usuario_id' está setado
if (!isset($_SESSION['user_id'])) {
    die("Usuário não logado ou ID do usuário não encontrado.");
}

include "conexao.php";

// Buscar informações do usuário logado
$user_id = $_SESSION['user_id']; // Supondo que você armazena o ID do usuário na sessão
$sql = "SELECT nome_aluno, email, cpf_aluno FROM login_aluno WHERE id = ?";
$stmt = $conn->prepare($sql);

// Verifica se a preparação da consulta foi bem-sucedida
if ($stmt === false) {
    die("Erro na preparação da consulta: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Verifica se o usuário existe
if ($result->num_rows > 0) {
    $usuario = $result->fetch_assoc();
    $nome_usuario = $usuario['nome_aluno'];
    $email_usuario = $usuario['email'];
    $cpf_usuario = $usuario['cpf_aluno'];
} else {
    // Se o usuário não for encontrado, redirecione ou trate o erro
    $nome_usuario = 'Usuário não encontrado';
    $email_usuario = 'Email não disponível';
    $cpf_usuario = 'CPF não disponível';
}

// Feche a conexão
$stmt->close();
$conn->close();

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./painel/img/logo.png" type="image/x-icon">
    <title>Informações do usuário</title>
    <link rel="stylesheet" href="./css/bootstrap.min.css" />
    <link rel="stylesheet" href="./css/fontawesome.css" />
    <link rel="stylesheet" href="./css/animate.css" />
    <link rel="stylesheet" href="./css/main.css" />
    <link rel="stylesheet" href="./painel/css/painel.css">
</head>
<style>
    .card-address {
        border: none !important;
        cursor: default !important;
    }
    .logo-empresa .container-img-sobre {
        position: relative;
        margin-top: -40px;
        border-radius: 10px;
        width: 90px;
        height: 90px;
        right: -20px;
        border: none !important;
    }
    .logo-empresa .icon-action {
        position: absolute;
        right: -10px;
        background-color: var(--color-white) !important;
        color: var(--color-primary) !important;
        bottom: -10px;
    }
    /* Estilos gerais para a modal */
    .modal {
        display: none; /* Inicialmente oculta */
        position: fixed;
        z-index: 9999;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5); /* Fundo escuro com transparência */
        justify-content: center;
        align-items: center;
    }

    .modal-content {
        background-color: #fff;
        width: 100%;
        border-radius: 1px;
        max-width: 500px; /* Largura máxima da modal */
        padding: 20px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        text-align: center;
    }

    .modal-header {
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
        text-align: center;
    }

    .modal-header h2 {
        font-size: 1.5rem;
        margin: 0;
    }

    .modal-body {
        margin: 20px 0;
    }

    .modal-body label {
        display: block;
        font-weight: bold;
        margin-bottom: 5px;
        text-align: left;
    }

    .modal-body input[type="text"] {
        width: 100%;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ddd;
        font-size: 1rem;
    }

    .modal-footer {
        margin-top: 20px;
        display: flex;
        justify-content: space-between;
    }

    .modal-footer button {
        padding: 10px 20px;
        border: none;
        border-radius: 5px;
        font-size: 1rem;
        cursor: pointer;
    }

    .modal-footer .btn-close {
        background-color: #ccc;
        color: #000;
    }

    .modal-footer .btn-update {
        background-color: #28a745;
        color: #fff;
    }

    /* Exibir modal ao estar ativo */
    .modal.active {
        display: flex;
    }

</style>
<body>
    <div class="bg-top pedido"></div>

    <header class="width-fix mt-3">
        <div class="card">
            <div class="d-flex">
                <a href="./inicio.php" class="container-voltar">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="infos text-center">
                    <h1 class="mb-0"><b>Usuário</b></h1>
                </div>
            </div>
        </div>
    </header>

   
    <section class="carrinho width-fix mt-5">
        <div class="card card-address">
            <div class="img-icon-details">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="infos">
                <p class="name mb-0"><b>Ol&aacute;!</b></p>
            </div>
            <div class="logo-empresa">
                <?php if (!empty($logoImage)): ?>
                <div class="container-img-sobre" id="logoContainer" style="background-image: url('./painel/uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>'); background-size: 70%;">  
                <?php else: ?>
                    
                <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    
    <section class="carrinho width-fix mt-4">
    </section>

    <section class="opcionais width-fix mt-5 pb-5">
        <div class="container-group mb-3">
            <p class="title-categoria mb-0"><b>NOME:</b></p>
            <div class="card card-address mt-2">
                <div class="img-icon-details">
                    <i class="fas fa-user"></i>
                </div>
                <div class="infos">
                    <p class="name mb-0"><b><?= htmlspecialchars($nome_usuario) ?></b></p>
                </div>
                <a href="#" class="icon-edit" data-bs-toggle="modal" data-bs-target="#editNameModal">
                    <i class="fas fa-pencil-alt color-primary"></i>
                </a>

            </div>
        </div>

        <div class="container-group mb-3">
            <p class="title-categoria mb-0"><b>EMAIL:</b></p>
            <div class="card card-address mt-2">
                <div class="img-icon-details">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="infos">
                    <p class="name mb-0"><b><?= htmlspecialchars($email_usuario) ?></b></p>
                </div>
                <a href="#" class="icon-edit" data-bs-toggle="modal" data-bs-target="#editEmailModal">
                    <i class="fas fa-pencil-alt color-primary"></i>
                </a>
            </div>
        </div>

        <div class="container-group mb-5">
            <p class="title-categoria mb-0"><b>CPF:</b></p>
            <div class="card card-address mt-2">
                <div class="img-icon-details">
                    <i class="fas fa-id-card"></i> <!-- Ícone atualizado para CPF -->
                </div>
                <div class="infos">
                    <p class="name mb-0"><b><?= htmlspecialchars($cpf_usuario) ?></b></p> <!-- Exibe o CPF do usuário -->
                </div>
                <a href="#" class="icon-edit" data-bs-toggle="modal" data-bs-target="#editCpfModal"> <!-- Modal para editar CPF -->
                    <i class="fas fa-pencil-alt color-primary"></i>
                </a>
            </div>
        </div>


    </section>

    <section class="menu-bottom" id="menu-bottom">
        <a href="./inicio.php" class="menu-bottom-item">
            <i class="fas fa-home"></i>
        </a>
        <a href="./info.php" class="menu-bottom-item">
            <i class="fas fa-book"></i>
        </a>
        <a href="./user.php" class="menu-bottom-item active">
            <i class="fas fa-user"></i>
        </a>
        <a href="./logoutAluno.php" class="menu-bottom-item">
            <i class="fas fa-sign-out-alt"> Sair</i>
        </a>
    </section>
    

    <!-- Modal para editar o nome -->
    <div class="modal fade" id="editNameModal" tabindex="-1" aria-labelledby="editNameModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editNameModalLabel">Editar Nome</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editNameForm" method="POST" action="atualizarNomeUser.php">
                <div class="mb-3">
                    <label for="nomeUsuario" class="form-label">NOME:</label>
                    <input type="text" class="form-control" id="nomeUsuario" name="nome_aluno" value="<?= htmlspecialchars($nome_usuario) ?> " oninput="this.value = this.value.toUpperCase()">
                </div>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                <button type="submit" class="btn btn-primary">Atualizar</button>
                </form>
            </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar o email -->
    <div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editEmailModalLabel">Editar E-mail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEmailForm" method="POST" action="atualizarEmailUser.php">
                        <div class="mb-3">
                            <label for="emailUsuario" class="form-label">EMAIL:</label>
                            <input type="email" class="form-control" id="emailUsuario" name="email" value="<?= htmlspecialchars($email_usuario) ?>">
                        </div>
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>">
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para editar o CPF -->
    <div class="modal fade" id="editCpfModal" tabindex="-1" aria-labelledby="editCpfModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editCpfModalLabel">Editar CPF</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editCpfForm" method="POST" action="atualizarCpfUser.php"> <!-- Corrigido o action -->
                        <div class="mb-3">
                            <label for="cpfUsuario" class="form-label">CPF:</label>
                            <input type="text" class="form-control" id="cpfUsuario" name="cpf_aluno" value="<?= htmlspecialchars($cpf_usuario) ?>" maxlength="14">
                        </div>
                        <input type="hidden" name="user_id" value="<?= htmlspecialchars($user_id) ?>"> <!-- Mantido o campo oculto para o ID do usuário -->
                        <button type="submit" class="btn btn-primary">Atualizar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


    <script type="text/javascript" src="./js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="./js/item.js"></script>

</body>
</html>
