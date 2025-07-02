<?php
session_start();

// Função para verificar se o usuário está logado como administrador ou presidente
function verificarAcesso() {
    if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        if($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true;
        }
    }
    
    header("Location: loader.php");
    exit();
}

verificarAcesso();

include "conexao.php";

// Buscar a categoria a ser editada
if (isset($_GET['id_categoria'])) {
    $id_categoria = $_GET['id_categoria'];
    $sql = "SELECT * FROM categorias WHERE id_categoria = :id_categoria";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
    $stmt->execute();
    $categoria = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$categoria) {
        echo "Categoria não encontrada.";
        exit();
    }
} else {
    echo "ID da categoria não informado.";
    exit();
}

// Atualizar os dados da categoria
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome_categoria = $_POST['nome'];
    $preco = $_POST['preco'];
    $parcelado = $_POST['parcelado'];
    $status = $_POST['status'];
    $imagem = $categoria['imagem']; // Caminho da imagem atual

    // Se uma nova imagem foi enviada
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
        $target_dir = "../uploads/";
        $imagem_nome = basename($_FILES["imagem"]["name"]);
        $target_file = $target_dir . $imagem_nome;

        // Verificar se é uma imagem válida
        $check = getimagesize($_FILES["imagem"]["tmp_name"]);
        if ($check !== false) {
            // Apagar a imagem antiga
            if (file_exists($categoria['imagem'])) {
                unlink($categoria['imagem']);
            }

            // Mover a nova imagem para o diretório
            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $target_file)) {
                $imagem = $target_file; // Caminho completo da nova imagem
            } else {
                echo "Erro ao fazer upload da nova imagem.";
                exit();
            }
        } else {
            echo "O arquivo enviado não é uma imagem válida.";
            exit();
        }
    }

    // Atualizar a categoria no banco de dados
    try {
        $sql = "UPDATE categorias SET nome = ?, preco = ?, parcelado = ?, status = ?, imagem = ? WHERE id_categoria = ?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$nome_categoria, $preco, $parcelado, $status, $imagem, $id_categoria]);

        echo "<script> alert('Categoria atualizada com sucesso!'); history.back(); </script>";
    } catch (PDOException $e) {
        echo "Erro ao atualizar a categoria: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Editar Categoria - Painel</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
</head>
<body>

    <section class="bg-menu">

        <div class="conteudo" style="margin-left: -240px;">
            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page">
                                <b><i class="fas fa-car"></i>&nbsp; EDITAR CATEGORIA</b>
                            </h1>
                            <div class="container-right">
                                <a href="listarcategorias.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="conteudo-inner">
                <div class="container">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="mb-3 col-6">
                                <label for="nome" class="form-label">Nome da Categoria</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="<?php echo htmlspecialchars($categoria['nome']); ?>" required>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="preco" class="form-label">Preço do Curso</label>
                                <input type="number" class="form-control" id="preco" name="preco" value="<?php echo htmlspecialchars($categoria['preco']); ?>" step="0.01" required>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="parcelado" class="form-label">Parcelado</label>
                                <input type="text" class="form-control" id="parcelado" name="parcelado" value="<?php echo htmlspecialchars($categoria['parcelado']); ?>">
                            </div>

                            <div class="mb-3 col-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control" name="status" required>
                                    <option value="Disponível" <?php echo $categoria['status'] == 'Disponível' ? 'selected' : ''; ?>>Disponível</option>
                                    <option value="Indisponível" <?php echo $categoria['status'] == 'Indisponível' ? 'selected' : ''; ?>>Indisponível</option>
                                    <option value="Promoção" <?php echo $categoria['status'] == 'Promoção' ? 'selected' : ''; ?>>Promoção</option>
                                </select>
                            </div>

                            <div class="mb-3 col-6">
                                <label for="imagem" class="form-label">Imagem da Categoria</label>
                                <input type="file" class="form-control" id="imagem" name="imagem">
                                <small>Deixe em branco para manter a imagem atual.</small>
                            </div>
                        </div>

                        <div class="text-right">
                             <div style="float: right;">
                                <button type="submit" class="col-m-5 btn btn-sm mt-3 btn-white active">Atualizar Categoria</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

</body>
</html>
