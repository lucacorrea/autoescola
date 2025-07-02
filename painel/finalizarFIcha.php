<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador, presidente ou suporte
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é admin, presidente ou suporte
        $nivel_usuario = $_SESSION['nivel'];

        // Verificar se o nível de usuário é permitido
        if (in_array($nivel_usuario, ['admin', 'presidente', 'suporte'])) {
            return true; // O usuário tem permissão para acessar
        }
    }

    // Se não estiver logado ou não tiver permissão, redireciona para loader.php
    header("Location: loader.php");
    exit(); // Encerra o script após o redirecionamento
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

include "conexao.php";

// Verifica se o ID da ficha foi passado na URL
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_ficha = intval($_GET['id']); // Garante que o ID seja um número inteiro

    try {
        // Primeiro, obtenha os detalhes da ficha
        $sql = "SELECT rg FROM fichas WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_ficha, PDO::PARAM_INT);
        $stmt->execute();
        $rg_ficha = $stmt->fetchColumn();

        if (empty($rg_ficha)) {
            echo "<script>alert('Ficha não encontrada.'); window.location.href = 'index.php';</script>";
            exit();
        }

        // Agora, obtenha o ID do aluno baseado no RG da ficha
        $sql = "SELECT id FROM alunos WHERE rg = :rg";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':rg', $rg_ficha, PDO::PARAM_STR);
        $stmt->execute();
        $id_aluno = $stmt->fetchColumn();

        if (empty($id_aluno)) {
            echo "<script>alert('ID do aluno não encontrado para o RG fornecido.'); window.location.href = 'index.php';</script>";
            exit();
        }

        // Atualiza o status da ficha para 'Finalizado'
        $sql = "UPDATE fichas SET status = 'Finalizado' WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_ficha, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            // Sucesso na atualização
            echo "<script>alert('Ficha finalizada com sucesso!'); window.location.href = 'ficha.php?id={$id_aluno}';</script>";
        } else {
            // Falha na atualização
            echo "<script>alert('Erro ao finalizar a Ficha.'); window.location.href = 'ficha.php?id={$id_aluno}';</script>";
        }

    } catch (PDOException $e) {
        // Captura erros relacionados ao banco de dados
        die("Erro no banco de dados: " . $e->getMessage());
    }
} else {
    echo "<script>alert('ID da ficha não encontrado.'); window.location.href = 'index.php';</script>";
}

?>
