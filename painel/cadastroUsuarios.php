<?php

session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como presidente
function verificarAcesso() {
    if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é presidente
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        // Verificar se o nível de usuário é presidente
        if($nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            // O usuário é presidente, então ele tem permissão para acessar esta parte do sistema
            return true;
        } elseif($nivel_usuario == 'admin' ) {
            // Se o usuário é administrador, mas não presidente, ele não tem permissão
            // Redirecionar para outra página ou exibir uma mensagem de erro
            header("Location: paginaProtegida.php");
            exit(); // Encerra o script após o redirecionamento
        }
        
        
    }
    
    // Se não estiver logado como presidente, redirecione-o para a página de login
    header("Location: loader.php");
    exit(); // Encerra o script após o redirecionamento
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

// Configuração da conexão com o banco de dados
    include 'conexao.php';
// Obtém a imagem do logotipo da associação
$id = 1; 
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);

$logoImage = $associacao['logo_image'] ?? '';
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Cadastro de Usuários - Autoescola</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
</head>

<body>
    <div class="container">
        <div class="d-flex justify-content-end mb-0 mt-5">
            <a href="usuarios.php" class="btn btn-white btn-sm">
                <i class="fas fa-sign-out-alt"></i>&nbsp; Voltar
            </a>
        </div>
    </div>

    <section class="login" style=" margin-top: -70px !important;">
        <form action="processarUsuario.php" method="post">

            <div class="card card-login">

                <?php if (!empty($logoImage)): ?>
                    <img src="uploads/<?php echo htmlspecialchars($logoImage ?? 'default.png'); ?>" width="120" alt="Logo">
                <?php endif; ?>

                <div class="form-group mb-2">
                    <span class="icon-form">
                        <i class="fas fa-user"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Nome" name="nome" required />
                </div>

                <div class="form-group mb-2">
                    <span class="icon-form">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" class="form-control" placeholder="E-mail" name="email" required />
                </div>

                <div class="form-group mb-2">
                    <span class="icon-form">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" placeholder="Senha" name="senha" required />
                </div>

                <div class="form-group mb-3">
                    <span class="icon-form">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" placeholder="Confirme a Senha" name="confirma_senha" required />
                </div>

                <button type="submit" class="btn btn-yellow btn-login mt-4">
                    Cadastrar Usuário
                </button>

            </div>

        </form>
    </section>

    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>

</body>
</html>
