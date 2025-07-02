<?php
session_start(); // Inicia a sessão

// Verifica se o aluno está logado
if (!isset($_SESSION['user_id'])) {
    // Redireciona para a página de login se o usuário não estiver logado
    header("Location: loaderAluno.php");
    exit();
}

include "./painel/conexao.php";

// Receber os dados do formulário de forma segura
$user_id = $_POST['user_id'] ?? null;
$cpf_aluno = trim($_POST['cpf_aluno'] ?? '');

// Verificar se o ID do usuário e o CPF não estão vazios
if (!empty($user_id) && !empty($cpf_aluno)) {
    // Atualizar o CPF no banco de dados
    $sql = "UPDATE login_aluno SET cpf_aluno = :cpf WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':cpf', $cpf_aluno);
    $stmt->bindParam(':id', $user_id);

    // Tentar executar a atualização
    if ($stmt->execute()) {
        echo "<script>alert('CPF atualizado com sucesso.');</script>";
        echo "<script>window.location.href = 'user.php';</script>"; // Redireciona para a página do usuário
    } else {
        echo "<script>alert('Erro ao atualizar o CPF.');</script>";
        echo "<script>window.history.back();</script>";
    }
} else {
    echo "<script>alert('O CPF não pode estar vazio.');</script>";
    echo "<script>window.history.back();</script>"; // Redireciona de volta ao formulário
}
?>
