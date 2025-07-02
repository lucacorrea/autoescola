<?php

//--------------------SESSION IMAGE EMPRESA-------------------------

    include './painel/conexao.php';

    $id = 1;
    $sql = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql); 
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

    $logoImage = !empty($associacao['logo_image']) ? $associacao['logo_image'] : '';

//--------------------END SESSION IMAGE EMPRESA---------------------

?>


<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./painel/img/logo.png" type="image/x-icon">
        <title>Login - Autoescola</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/fontawesome.css" />
        <link rel="stylesheet" href="css/animate.css" />
        <link rel="stylesheet" href="css/main.css" />
        <link rel="stylesheet" href="./painel/css/painel.css" />
        <!-- End custom CSS -->

    </head>

    <body>

        <!-- Section -->
        <section class="login">

            <!-- form -->
            <form action="processarLoginAluno.php" method="post">

                <!-- card-login -->
                <div class="card card-login"
                >
                    <img class="" src="./painel/uploads/<?php echo htmlspecialchars($logoImage); ?>" width="140" alt="Logo">

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

                    
                    <a href="criarConta.php" class="link mt-2">Criar conta</a>

                    <button type="submit" class="btn btn-yellow btn-login mt-4 mb-4">Fazer Login</button>

                    <a href="redefinirSenha.php" class="link text-center">Esqueceu sua senha?</a>

                </div>
                <!-- End card-login -->

            </form>
            <!-- End form -->

        </section>
        <!-- End Section -->

        <!-- Scripts -->
        <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
        <!-- End  Scripts -->

    </body>

</html>
