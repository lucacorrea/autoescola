<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador ou presidente
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é admin, presidente ou suporte
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        if (in_array($nivel_usuario, ['admin', 'presidente', 'suporte'])) {
            return true; // O usuário tem permissão
        }
    }

    // Redirecionar para outra página caso o acesso não seja permitido
    header("Location: loader.php");
    exit();
}

// Verificar o acesso antes de continuar
verificarAcesso();

require "conexao.php"; // Inclui a conexão com o banco de dados

// Captura os dados do formulário
$nomeAssociacao = trim($_POST['nomeAssociacao'] ?? '');
$sobreAssociacao = trim($_POST['sobreAssociacao'] ?? '');
$id = isset($_POST['id']) ? intval($_POST['id']) : 0;

$logoImage = '';
$uploadDirectory = 'uploads/';

if (isset($_FILES['logoImage']) && $_FILES['logoImage']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['logoImage']['tmp_name'];
    $fileName = $_FILES['logoImage']['name'];
    $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];

    // Gera um nome único para o arquivo
    $newFileName = uniqid('logo_', true) . '.' . $fileExtension;

    if (in_array($fileExtension, $allowedExts)) {
        $uploadFile = $uploadDirectory . $newFileName;

        // Cria o diretório de upload se não existir
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0755, true);
        }

        // Se houver uma imagem atual, apague antes de salvar a nova
        if (!empty($_POST['currentLogoImage'])) {
            $currentLogoPath = $uploadDirectory . trim($_POST['currentLogoImage']);
            if (file_exists($currentLogoPath)) {
                unlink($currentLogoPath); // Apaga o arquivo anterior
            }
        }

        // Move o arquivo para o diretório de upload
        if (move_uploaded_file($fileTmpPath, $uploadFile)) {
            $logoImage = $newFileName; // Atualiza o nome do logo com o novo arquivo
        } else {
            die("Erro ao mover o arquivo para o diretório de upload.");
        }
    } else {
        die("Extensão de arquivo não permitida.");
    }
} else {
    // Se nenhum novo arquivo for enviado, mantém o logo atual
    $logoImage = trim($_POST['currentLogoImage'] ?? '');
}

// Verifica se estamos fazendo uma inserção ou atualização
try {
    if ($id > 0) {
        // Atualiza os dados no banco de dados
        $sql = "UPDATE associacoes 
                SET nome_associacao = :nome_associacao, 
                    sobre_associacao = :sobre_associacao, 
                    logo_image = :logo_image 
                WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome_associacao', $nomeAssociacao);
        $stmt->bindParam(':sobre_associacao', $sobreAssociacao);
        $stmt->bindParam(':logo_image', $logoImage);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        $stmt->execute();
        echo "<script>alert('Dados atualizados com sucesso.'); window.location.href='empresa.php';</script>";
    } else {
        // Insere os dados no banco de dados
        $sql = "INSERT INTO associacoes (nome_associacao, sobre_associacao, logo_image) 
                VALUES (:nome_associacao, :sobre_associacao, :logo_image)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome_associacao', $nomeAssociacao);
        $stmt->bindParam(':sobre_associacao', $sobreAssociacao);
        $stmt->bindParam(':logo_image', $logoImage);

        $stmt->execute();
        echo "<script>alert('Dados inseridos com sucesso.'); window.location.href='empresa.php';</script>";
    }
} catch (PDOException $e) {
    die("Erro ao salvar os dados: " . $e->getMessage());
}
?>
