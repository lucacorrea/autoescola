<?php

    include "conexao.php";

    // ID da associação
    $id = 1; 

    // Consulta para pegar a logo_image
    $sql = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    // Verifica se encontrou algum dado
    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifica se o campo 'logo_image' está definido e não é vazio
    $logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login - Autoescola</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css" />
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/painel.css" />
        <!-- End custom CSS -->

    </head>

    <body>

        <!-- section -->
        <section class="login">

            <!-- form -->
            <form action="processarLogin.php" method="post">

                <!-- card-login -->
                <div class="card card-login">

                    <?php if (!empty($logoImage)): ?>

                        <img src="uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" width="140" alt="Logo">
                    
                        <?php else: ?>
                
                    <?php endif; ?>

                    <div class="form-group mb-2">
                        
                        <span class="icon-form">
                            <i class="fas fa-envelope"></i>
                        </span>

                        <input type="email" class="form-control" placeholder="E-mail" name="email" /> <!-- Added name attribute -->
                    
                    </div>

                    <div class="form-group mb-3">

                        <span class="icon-form">
                            <i class="fas fa-lock"></i>
                        </span>

                        <input type="password" class="form-control" placeholder="Senha" name="senha" /> <!-- Added name attribute -->
                    
                    </div>

                    <button type="submit" class="btn btn-yellow btn-login mt-4">
                        Fazer Login
                    </button>

                    <a href="./redefinirSenha.php" class="link text-center mt-3">Esqueceu sua senha ?</a>

                </div>
                <!-- End card-login -->

            </form>
            <!-- End form -->

        </section>
        <!-- End section -->


        <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
        
    </body>

</html>
