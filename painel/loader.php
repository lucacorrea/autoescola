<?php

//------------------------SESSION IMAGE EMPRESA-------------------

    include "conexao.php";

    $id = 1; 

    $sql = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

    $logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";

//------------------------END SESSION IMAGE EMPRESA---------------

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <title>Autoescola</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="./css/logout.css">
        <!-- End custom CSS -->

    </head>

    <body>

        <!-- loader-container -->
        <div class="loader-container">

            <?php if (!empty($logoImage)): ?>
                <img src="uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" class="loader-img logo-img mb-5" alt="Logo">
            <?php else: ?>

                <p class="access-denied">Logo não encontrada.</p>

            <?php endif; ?>

            <div class="access-denied access">

                Você não tem acesso a essa página. <br>
                <a href="index.php">Volte para o início</a>.

            </div>

        </div>
        <!-- End loader-container -->

    </body>

</html>
