<?php

    //----------SESSION---------------

        session_start();

        function verificarAcesso() {
            if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
                $nivel_usuario = $_SESSION['nivel'];

                if($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
                    return true;
                }
            }
            
            header("Location: loader.php");
            exit(); 
        }

        verificarAcesso();

    //----------END SESSION-----------


    //----------SESSION IMAGE EMPRESA-------------

    include "conexao.php";

    $id_associacao = 1;
    $query = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':id', $id_associacao, PDO::PARAM_INT);
    $stmt->execute();
    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

    $logoImage = $associacao['logo_image'] ?? "";

    //----------END SESSION IMAGE EMPRESA---------

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Loader Page</title>
        <!-- custom CSS -->
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css">
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/loader.css" />
        <!-- End custom CSS -->
    </head>
    <body>

        <!-- Loader Full Screen -->

            <?php if (!empty($logoImage)): ?>
                <div class="loader-full animated fadeIn">
                    <img src="uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" width="100" class="animated pulse infinite" />
                </div>
            <?php else: ?>
                            
            <?php endif; ?>

        <!-- End Loader Full Screen -->

        <!-- Scripts -->
        <script src="./js/loader.js"></script>
        <!-- End Scripts -->

    </body>

</html>
