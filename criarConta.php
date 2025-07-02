<?php

//--------------------SESSION IMAGE EMPRESA-----------------

    include "./painel/conexao.php";

    $id = 1;
    $sql = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);


    $logoImage = !empty($associacao['logo_image']) ? $associacao['logo_image'] : '';

//--------------------END SESSION EMPRESA-------------------

?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./painel/img/logo.png" type="image/x-icon">
        <title>Login - Autoescola</title>

        <!-- Custom CSS -->
        <link rel="stylesheet" href="css/bootstrap.min.css" />
        <link rel="stylesheet" href="css/fontawesome.css" />
        <link rel="stylesheet" href="css/animate.css" />
        <link rel="stylesheet" href="css/main.css" />
        <link rel="stylesheet" href="./painel/css/painel.css" />
        <!-- End custom CSS -->

    </head>

    <body>

        <!-- section -->
        <section class="login">

            <!-- form -->
            <form action="processarContaAluno.php" method="post">

                <!-- card-login -->
                <div class="card card-login">

                    <img class="" src="./painel/uploads/<?php echo htmlspecialchars($logoImage); ?>" width="140" alt="Logo">

                    <div class="form-group mb-2">

                        <span class="icon-form">
                            <i class="fas fa-lock"></i>
                        </span>

                        <input type="text" class="form-control" placeholder="Digite o Nome Completo" name="nome_aluno" required oninput="this.value = this.value.toUpperCase()"/>
                    
                    </div>

                    <div class="form-group mb-2">

                        <span class="icon-form">
                            <i class="fas fa-id-card"></i>
                        </span>

                        <input type="text" class="form-control" name="cpf_aluno" id="cpfAluno" placeholder="Digite o CPF" />
                    
                    </div>

                    <div class="form-group mb-2">

                        <span class="icon-form">
                            <i class="fas fa-envelope"></i>
                        </span>

                        <input type="email" class="form-control" placeholder="Digite o E-mail" name="email" required />
                    
                    </div>

                    <div class="form-group mb-2">

                        <span class="icon-form">
                            <i class="fas fa-lock"></i>
                        </span>

                        <input type="password" class="form-control" placeholder="Digite a Senha" name="senha" required />
                    
                    </div>

                    <button type="submit" class="btn btn-yellow btn-login mt-3 mb-3">Fazer Login</button>

                    <a href="login.php" class="link text-center">Cancelar</a>

                </div>
                <!-- End card-login -->

            </form>
            <!-- End form -->

        </section>
        <!-- End Section -->

        <!-- Scripts -->
        <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
        <script>

            $(document).ready(function(){
                $('#cpfAluno').mask('000.000.000-00', {reverse: true});
            });

        </script>
        <!-- End Scripts -->

    </body>

</html>
