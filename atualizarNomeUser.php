<?php
session_start(); // Inicia a sessão

// Verifica se o aluno está logado
if (!isset($_SESSION['user_id'])) {
    // Redireciona para a página de login se o usuário não estiver logado
    header("Location: loaderAluno.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verifica se o nome do aluno foi enviado
    if (isset($_POST['nome_aluno'])) {
        $nome_aluno = $_POST['nome_aluno'];
        $user_id = $_SESSION['user_id']; // Obtém o ID do usuário da sessão

        include "conexao.php";

        // Atualiza o nome do aluno
        $sql = "UPDATE login_aluno SET nome_aluno = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }

        $stmt->bind_param("si", $nome_aluno, $user_id);
        if ($stmt->execute()) {
            echo "<script>alert('Nome atualizado com sucesso.');</script>";
            echo "<script>window.location.href = 'user.php';</script>";
        } else {
            echo "Erro ao atualizar o nome.";
        }

        // Fecha a conexão
        $stmt->close();
        $conn->close();
    }
}
?>
