<?php

include "conexao.php";

function enviarEmailRedefinicao($email, $token) {
    $assunto = "CÓDIGO DE REDEFINIÇÃO DE SENHA - AUTOESCOLA DINÂMICA";

    $mensagem = "
    <html>
    <head>
        <title>Redefinição de Senha</title>
        <style>
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
            .token {
                font-size: 43px;
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

    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n";
    $headers .= "From: Autoescola Dinâmica <no-reply@autoescola.com>\r\n";
    $headers .= "Reply-To: autoescoladinamica918@gmail.com\r\n";

    return mail($email, $assunto, $mensagem, $headers);
}

// Reenviar código
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['reenviar_token'])) {
    $email = $_POST['email'];
    $novoToken = rand(100000, 999999); // Geração de token simples

    $sql = "UPDATE usuarios SET reset_token = :token, reset_token_validade = DATE_ADD(NOW(), INTERVAL 15 MINUTE) WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':token', $novoToken, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();

    enviarEmailRedefinicao($email, $novoToken);

}

// Verificar token
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['token'])) {
    $email = $_POST['email'];
    $token = $_POST['token'];

    $sql = "SELECT reset_token, reset_token_validade FROM usuarios WHERE email = :email";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->execute();
    
    $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($resultado) {
        $resetToken = $resultado['reset_token'];
        $validadeToken = $resultado['reset_token_validade'];
        $agora = date('Y-m-d H:i:s');

        if ($token == $resetToken && $agora <= $validadeToken) {
            header("Location: redefinirSenhaNova.php?email=" . urlencode($email));
            exit;
        } else {
            echo "<script>alert('Código inválido ou expirado. Tente novamente.');</script>";
        }
    } else {
        echo "<script>alert('E-mail não encontrado.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Redefinir Senha - Autoescola</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/token.css">
</head>
<style>
   
</style>
<body>
    <section class="login">
        <div class="container">
            <div class="custom-card mb-4">
                <h2 class="card-title">Código enviado! Verifique seu e-mail.</h2>
            </div>
            <form action="" method="post">
                <div class="card card-login">
                    <h5>Insira o Código:</h5>
                    <div class="form-group mb-3">
                        <input type="text" class="form-control" placeholder="Código de Redefinição" name="token" required>
                    </div>
                    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                    <button type="submit" class="btn btn-yellow btn-login mt-4">Verificar Código</button>
                    <a href="./login.php" class="mt-4 link text-center">Voltar</a>
                </div>
            </form>

            <div class="timer mt-4" id="timer">Reenvio em 60 segundos</div>
            <form method="post">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email'] ?? ''); ?>">
                <button type="submit" name="reenviar_token" id="reenviar-btn" class="btn disabled-btn mt-3" disabled>Reenviar Código</button>
            </form>
        </div>
    </section>

    <script>
        function iniciarContagemRegressiva() {
            const timerDiv = document.getElementById("timer");
            const reenviarBtn = document.getElementById("reenviar-btn");
        
            // Verifica se existe o tempo restante no localStorage
            let tempoRestante = localStorage.getItem("tempoRestante");
            if (!tempoRestante) {
                tempoRestante = 60; // Se não houver tempo salvo, inicia com 60 segundos
            } else {
                tempoRestante = parseInt(tempoRestante); // Converte de string para número
            }
        
            // Atualiza a interface imediatamente para refletir o tempo correto
            timerDiv.textContent = `Reenvio em ${tempoRestante} segundos`;
        
            const intervalo = setInterval(() => {
                tempoRestante--;
                localStorage.setItem("tempoRestante", tempoRestante); // Salva o tempo restante no localStorage
                timerDiv.textContent = `Reenvio em ${tempoRestante} segundos`;
                if (tempoRestante <= 0) {
                    clearInterval(intervalo);
                    timerDiv.textContent = "Reenviar código novamente.";
                    reenviarBtn.disabled = false;
                    localStorage.removeItem("tempoRestante"); // Remove o tempo do localStorage quando terminar
                }
            }, 1000);
        }
        
        window.onload = iniciarContagemRegressiva;

    </script>
</body>
</html>