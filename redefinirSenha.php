<?php

include "./painel/conexao.php";

function enviarEmailRedefinicao($email, $token) {
    $assunto = "CÓDIGO DE REDEFINIÇÃO DE SENHA - AUTOESCOLA DINÂMICA";

    // Corpo da mensagem com formatação HTML
    $mensagem = "
    <html>
    <head>
        <title>Redefinição de Senha</title>
        <style>
            /* Estilo para centralizar o conteúdo */
            body {
                font-family: Arial, sans-serif;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
                background-color: #f4f4f9;
            }
            .container {
                text-align: center;
                padding: 20px;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }
            
            strong{
                font-size: 13px;
            }
            
            .token {
                font-size: 43px; /* Tamanho do token */
                font-weight: bold;
                color: #007bff;
                margin-top: 20px;
            }
        </style>
    </head>
    <body>
        <div class='container'>
            <p><strong>SEU CÓDIGO DE REDEFINIÇÃO DE SENHA É:</strong></p>
            <p class='token'>$token</p>
        </div>
    </body>
    </html>
    ";

    // Cabeçalhos personalizados para e-mail HTML
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n"; // Configuração de charset para HTML
    $headers .= "From: Autoescola Dinâmica <no-reply@autoescola.com>\r\n";
    $headers .= "Reply-To: autoescoladinamica918@gmail.com\r\n"; // Caso alguém responda, vai para esse e-mail

    // Enviar e-mail
    if (mail($email, $assunto, $mensagem, $headers)) {
        // E-mail enviado com sucesso
        return true;
    } else {
        // Erro no envio do e-mail
        return false;
    }
}


// Verificar se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];

    // Verificar se o e-mail existe no banco de dados
    $sql = "SELECT id FROM login_aluno WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Gerar um token de redefinição de senha
        $token = random_int(100000, 999999); // Gera um código de 6 dígitos
        $validade = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Inserir o token e a validade no banco de dados
        $sql = "UPDATE login_aluno SET reset_token = :token, reset_token_validade = :validade WHERE email = :email";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':token', $token, PDO::PARAM_INT);
        $stmt->bindParam(':validade', $validade, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        // Enviar o e-mail de redefinição de senha
        enviarEmailRedefinicao($email, $token);

        // Redirecionar para a página de verificação do token
        header("Location: verificarToken.php?email=" . urlencode($email));
        exit;
    } else {
        echo "<script>alert('Email não encontrado.');</script>";
        echo "<script>window.location.href = 'redefinirSenha.php';</script>";
    }
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

<body>
    <section class="login">
        <form action="redefinirSenha.php" method="post">
            <div class="card card-login">
                <h5>Redefinir Senha</h5>
                <div class="form-group mb-3">
                    <span class="icon-form">
                        <i class="fas fa-envelope"></i>
                    </span>
                    <input type="email" class="form-control" placeholder="E-mail" name="email" required />
                </div>

                <button type="submit" class="btn btn-yellow btn-login mt-4">Enviar Código de Redefinição</button>
                <a href="./login.php" class="mt-3 link text-center">Voltar</a>
            </div>
        </form>
    </section>
    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
