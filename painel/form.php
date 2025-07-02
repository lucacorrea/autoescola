<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador ou presidente
function verificarAcesso() {
    if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é admin ou presidente
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        // Verificar se o nível de usuário é admin ou presidente
        if($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            // O usuário tem permissão para acessar esta parte do sistema
            return true;
        }
    }
    
    // Se não estiver logado como admin ou presidente, redirecione-o para outra página
    header("Location: loader.php");
    exit(); // Encerra o script após o redirecionamento
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

// Conexão com o banco de dados (substitua as informações de conexão conforme necessário)
$servername = "localhost";
$username = "cfcaut82_autoescola";
$password = "Bt~fC13X5k{l";
$database = "cfcaut82_autoescola";

require "conexao.php";

try {

    // Verifica se a sessão do usuário está ativa
    if (!isset($_SESSION['id_usuario'])) {
        throw new Exception("Usuário não autenticado. Faça login novamente.");
    }

    // Obtém o ID do usuário na sessão
    $id_usuario = $_SESSION['id_usuario'];

    // Consulta para obter o nome e o e-mail do usuário
    $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Obtém os dados do usuário
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nome_usuario = htmlspecialchars($row['nome']);
        $email_usuario = htmlspecialchars($row['email']);
    } else {
        throw new Exception("Nenhum usuário encontrado com o ID fornecido.");
    }
} catch (PDOException $e) {
    // Exibe mensagens de erro de banco de dados
    die("Erro de conexão ou consulta: " . $e->getMessage());
} catch (Exception $e) {
    // Exibe mensagens de erro gerais
    die("Erro: " . $e->getMessage());
} finally {
    // Fecha explicitamente a conexão (opcional com PDO)
    $conn = null;
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Empresa</title>

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
                                <b><i class="fas fa-car"></i>&nbsp; CADASTRAR CATEGORIAS</b>
                            </h1>
                            <div class="container-right">
                                <div class="container-dados">
                                </div>
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
                    <div class="row">
                        <div class="col-12">
                        </div>
                        <div class="container mt-5">
                        <form method="POST" action="procesaPreco.php" enctype="multipart/form-data">
                            <div class="row">
                            <div class="mb-3 col-6">
                                <label for="nome" class="form-label">Nome da Categoria</label>
                                <input type="text" class="form-control" id="nome" name="nome" required>
                            </div>
                            <div class="col-6"></div>
                            <div class="mb-3 col-6">
                                <label for="preco" class="form-label">Preço do Curso</label>
                                <input type="number" class="form-control" id="preco" name="preco" step="0.01" required>
                            </div>
                            <div class="mb-3 col-6">
                                <label for="parcelado" class="form-label">Parcelado</label>
                                <input type="text" class="form-control" id="parcelado" name="parcelado">
                            </div>
                            <div class="mb-3 col-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-control"name="status" required>
                                    <option value="Disponível">Disponível</option>
                                    <option value="Indisponível">Indisponível</option>
                                    <option value="Promoção">Promoção</option>
                                </select>
                            </div>
                            <div class="mb-3 col-6">
                                <label for="imagem" class="form-label">Imagem da Categoria</label>
                                <input type="file" class="form-control" id="imagem" name="imagem">
                            </div>
                        </div>
                         <div class="row">
                           <div class="col-9"></div>
                            <button type="submit" name="action" value="adicionar" class="col-3 btn btn-sm mt-3 btn-white active">Adicionar Categoria</button>
                        </div>
        </form>
    </div>     
</body>
</html>