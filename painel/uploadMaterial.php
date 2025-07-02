<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador, presidente ou suporte
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        // Verificar se o nível de usuário é admin, presidente ou suporte
        if (in_array($nivel_usuario, ['admin', 'presidente', 'suporte'])) {
            return true; // O usuário tem permissão para acessar esta parte do sistema
        }
    }

    // Se não estiver logado com permissão, redirecione-o para outra página
    header("Location: loader.php");
    exit();
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

// Conecte-se ao banco de dados usando PDO
include 'conexao.php'; // Inclui o arquivo de conexão

// Definir o diretório de upload
$uploadDir = 'uploads/';

// Verificar se o diretório de upload existe, se não, criar
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tituloMaterial = $_POST['titulo_material'];
    $nomeCapa = '';
    $nomeMaterial = '';

    // Verificar se o arquivo da capa foi enviado
    if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['capa']['tmp_name'];
        $fileName = $_FILES['capa']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $newFileName = 'capa_' . time() . '.' . $fileExtension;

        $allowedFileExtensions = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($fileExtension, $allowedFileExtensions)) {
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $nomeCapa = $newFileName;
            } else {
                echo "Erro ao enviar a capa.<br>";
            }
        } else {
            echo "Tipo de arquivo não permitido para a capa.<br>";
        }
    }

    // Verificar se o arquivo do material foi enviado
    if (isset($_FILES['material']) && $_FILES['material']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['material']['tmp_name'];
        $fileName = $_FILES['material']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $newFileName = 'material_' . time() . '.' . $fileExtension;

        $allowedFileExtensions = ['pdf', 'doc', 'docx'];
        if (in_array($fileExtension, $allowedFileExtensions)) {
            $destPath = $uploadDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $destPath)) {
                $nomeMaterial = $newFileName;
            } else {
                echo "Erro ao enviar o material.<br>";
            }
        } else {
            echo "Tipo de arquivo não permitido para o material.<br>";
        }
    }

    // Inserir os dados no banco de dados
    if ($tituloMaterial && $nomeCapa && $nomeMaterial) {
        // Usando PDO para inserir os dados
        $sql = "INSERT INTO materiais_estudo (titulo_material, nome_capa, nome_material) 
                VALUES (:titulo_material, :nome_capa, :nome_material)";

        try {
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':titulo_material', $tituloMaterial);
            $stmt->bindParam(':nome_capa', $nomeCapa);
            $stmt->bindParam(':nome_material', $nomeMaterial);

            // Executa a query
            if ($stmt->execute()) {
                echo "<script>alert('Dados inseridos com sucesso.'); window.location.href='guiaEstudo.php';</script>";
            } else {
                echo "Erro ao cadastrar o material.";
            }
        } catch (PDOException $e) {
            echo "Erro ao inserir dados: " . $e->getMessage();
        }
    }
} else {
    echo "Método de solicitação inválido.";
}
?>
