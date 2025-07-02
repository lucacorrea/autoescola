<?php

include "./painel/conexao.php";

// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    
    // Criptografar a nova senha usando SHA-256
    $novaSenha = hash('sha256', $_POST['senha']);

    // Atualizar a senha no banco de dados
    $sql = "UPDATE login_aluno SET senha_hash = :senha WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':senha', $novaSenha, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    echo "<script>alert('Sua senha foi redefinida com sucesso.');</script>";
    echo "<script>window.location.href = 'login.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./painel/img/logo.png" type="image/x-icon">
    <title>Redefinir Senha - Autoescola</title>
    <link rel="stylesheet" href="css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/fontawesome.css" />
    <link rel="stylesheet" href="css/animate.css" />
    <link rel="stylesheet" href="css/main.css" />
    <link rel="stylesheet" href="./painel/css/painel.css" />
</head>
<style>
    h2{
        font-size: 20px;
    }
</style>

<body>
    <section class="login">
        <form action="redefinirSenhaNova.php" method="post">
            <div class="card card-login">
                <h2>Redefinir Senha:</h2>
                <div class="form-group mb-3">
                    <span class="icon-form">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" class="form-control" placeholder="Nova Senha" name="senha" required />
                </div>
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>" />

                <button type="submit" class="btn btn-yellow btn-login mt-4">Redefinir Senha</button>
                <a href="javascript:void(0);" class="mt-3 link text-center" onclick="history.back();">Voltar</a>
            </div>
        </form>
    </section>
    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
