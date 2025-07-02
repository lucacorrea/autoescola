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

include 'conexao.php';

// Verificar se o ID do instrutor foi passado
if (isset($_GET['id'])) {
    $id_instrutor = $_GET['id'];

    try {
        // Preparar a consulta para deletar o instrutor
        $query = "DELETE FROM instrutores WHERE id = :id_instrutor";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_instrutor', $id_instrutor, PDO::PARAM_INT);

        // Executar a consulta
        if ($stmt->execute()) {
            echo "<script>alert('Instrutor excluído com sucesso!'); window.location.href='instrutores.php';</script>";
        } else {
            echo "<script>alert('Erro ao excluir o instrutor.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        // Tratar erros de execução da consulta
        echo "<script>alert('Erro ao processar a exclusão: " . $e->getMessage() . "'); window.history.back();</script>";
    }
} else {
    echo "<script>alert('ID do instrutor não especificado.'); window.history.back();</script>";
}
?>

