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

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $tituloMaterial = $_POST['titulo_atual'];
    $capaAtual = $_POST['capa_atual'];
    $materialAtual = $_POST['material_atual'];

    // Função para fazer o upload de arquivos
    function fazerUpload($arquivo, $arquivoAtual, $tipo) {
        if (!empty($arquivo['name'])) {
            $nomeArquivo = basename($arquivo['name']);
            $caminhoArquivo = 'uploads/' . $nomeArquivo;

            // Faz o upload do novo arquivo
            if (move_uploaded_file($arquivo['tmp_name'], $caminhoArquivo)) {
                // Se o upload for bem-sucedido, apague o arquivo antigo
                if (file_exists('uploads/' . $arquivoAtual)) {
                    unlink('uploads/' . $arquivoAtual);
                }
                return $nomeArquivo;
            } else {
                die("Falha no upload do arquivo " . $tipo . ".");
            }
        } else {
            // Se nenhum arquivo novo for enviado, mantém o arquivo atual
            return $arquivoAtual;
        }
    }

    // Lida com os uploads da capa e do material
    $nomeCapa = fazerUpload($_FILES['capa'], $capaAtual, 'capa');
    $nomeMaterial = fazerUpload($_FILES['material'], $materialAtual, 'material');

    // Atualiza os dados no banco de dados, incluindo o título do material
    $sql = "UPDATE materiais_estudo SET titulo_material = :titulo_material, nome_capa = :nome_capa, nome_material = :nome_material WHERE id = :id";
    
    // Prepare a query e executa a atualização
    try {
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':titulo_material', $tituloMaterial);
        $stmt->bindParam(':nome_capa', $nomeCapa);
        $stmt->bindParam(':nome_material', $nomeMaterial);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        // Executa a query
        if ($stmt->execute()) {
            echo "<script>alert('Dados atualizados com sucesso.'); window.location.href='guiaEstudo.php';</script>";
        } else {
            die("Erro ao atualizar o material.");
        }
    } catch (PDOException $e) {
        die("Erro na execução da consulta: " . $e->getMessage());
    }
} else {
    die("Requisição inválida.");
}
?>
