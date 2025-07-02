<?php
session_start(); // Inicia a sessão

// Verifica se o aluno está logado
if (!isset($_SESSION['user_id'])) {
    // Redireciona para a página de login se o usuário não estiver logado
    header("Location: loaderAluno.php");
    exit();
}

include "./painel/conexao.php";

// Receber os dados do formulário
$user_id = $_POST['user_id'];
$email = $_POST['email'];

// Verificar se o e-mail não está vazio
if (!empty($email)) {
    // Atualizar o e-mail no banco de dados
    $sql = "UPDATE login_aluno SET email = :email WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':id', $user_id);
    if ($stmt->execute()) {
        echo "<script>alert('Email atualizado com sucesso.');</script>";
        echo "<script>window.location.href = 'user.php';</script>";
    } else {
        echo "Erro ao atualizar o e-mail.";
    }
} else {
    echo "O e-mail não pode estar vazio.";
}
?>
