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

// Incluir o arquivo de conexão com o banco de dados
include 'conexao.php'; // Arquivo de conexão com o banco de dados, assumindo que você já configurou o PDO ou MySQLi dentro dele

// Verificar se o ID da turma foi passado pela URL
if (isset($_GET['id'])) {
    $turmaId = intval($_GET['id']); // Obtém o ID da turma a partir da URL

    // Inicia uma transação para garantir que ambas as tabelas sejam atualizadas corretamente
    try {
        // Começa a transação
        $conn->beginTransaction();

        // Primeiro, exclui todos os registros na tabela `alunos_turmas` relacionados a esta turma
        $deleteAlunosTurma = $conn->prepare("DELETE FROM alunos_turmas WHERE id_turma = :id_turma");
        $deleteAlunosTurma->bindParam(':id_turma', $turmaId, PDO::PARAM_INT);
        $deleteAlunosTurma->execute();

        // Agora, exclui a turma na tabela `turmas`
        $deleteTurma = $conn->prepare("DELETE FROM turmas WHERE id = :id_turma");
        $deleteTurma->bindParam(':id_turma', $turmaId, PDO::PARAM_INT);
        $deleteTurma->execute();

        // Se tudo correr bem, faz commit da transação
        $conn->commit();

        echo "<script>alert('Turma excluída com sucesso.'); window.location.href='legislacao.php';</script>";

    } catch (Exception $e) {
        // Se ocorrer um erro, faz rollback da transação
        $conn->rollBack();
        echo "<script>alert('Erro ao excluir Turma.'); window.location.href='legislacao.php';</script>";
    }
} else {
    echo "<script>alert('ID da turma não fornecido.'); window.location.href='legislacao.php';</script>";
}

// Fechar a conexão
$conn = null;
?>
