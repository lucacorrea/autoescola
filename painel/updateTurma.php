<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador ou presidente
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é admin, presidente ou suporte
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        // Verificar se o nível de usuário é admin, presidente ou suporte
        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            // O usuário tem permissão para acessar esta parte do sistema
            return true;
        }
    }
    
    // Se não estiver logado como admin, presidente ou suporte, redirecione-o para outra página
    header("Location: loader.php");
    exit(); // Encerra o script após o redirecionamento
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

// Função para verificar se já existe uma turma com os mesmos dados de instrutor e local, exceto o ID atual
function verificarTurmaExistente($conn, $local, $instrutor, $data_inicio, $data_fim, $horario_inicio, $horario_fim, $turno, $id_turma) {
    $sql_check = "SELECT id FROM turmas 
                  WHERE local = :local 
                    AND data_inicio = :data_inicio 
                    AND data_fim = :data_fim 
                    AND horario_inicio = :horario_inicio 
                    AND horario_fim = :horario_fim 
                    AND turno = :turno 
                    AND instrutor != :instrutor 
                    AND id != :id_turma";
    
    $stmt_check = $conn->prepare($sql_check);
    
    // Associa os parâmetros usando bindParam
    $stmt_check->bindParam(':local', $local, PDO::PARAM_STR);
    $stmt_check->bindParam(':data_inicio', $data_inicio, PDO::PARAM_STR);
    $stmt_check->bindParam(':data_fim', $data_fim, PDO::PARAM_STR);
    $stmt_check->bindParam(':horario_inicio', $horario_inicio, PDO::PARAM_STR);
    $stmt_check->bindParam(':horario_fim', $horario_fim, PDO::PARAM_STR);
    $stmt_check->bindParam(':turno', $turno, PDO::PARAM_STR);
    $stmt_check->bindParam(':instrutor', $instrutor, PDO::PARAM_STR);
    $stmt_check->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
    
    // Executa a consulta
    $stmt_check->execute();
    
    // Verifica se a turma já existe
    return $stmt_check->rowCount() > 0;
}

// Incluir a conexão com o banco de dados
include 'conexao.php';

// Obtendo os dados do formulário
$id_turma = $_POST['id_turma'] ?? '';
$local = $_POST['local'] ?? '';
$instrutor = $_POST['instrutor'] ?? '';
$data_inicio = $_POST['data_inicio'] ?? '';
$data_fim = $_POST['data_fim'] ?? '';
$horario_inicio = $_POST['horario_inicio'] ?? '';
$horario_fim = $_POST['horario_fim'] ?? '';
$turno = $_POST['turno'] ?? '';

// Verificar se os campos obrigatórios estão preenchidos
if (empty($id_turma) || empty($local) || empty($instrutor) || empty($data_inicio) || empty($data_fim) || empty($horario_inicio) || empty($horario_fim) || empty($turno)) {
    echo "<script>alert('Por favor, preencha todos os campos obrigatórios.'); window.location.href='editarTurma.php?id=" . htmlspecialchars($id_turma) . "';</script>";
    exit();
}

// Verificar se já existe uma turma com os mesmos dados, exceto o ID atual
if (verificarTurmaExistente($conn, $local, $instrutor, $data_inicio, $data_fim, $horario_inicio, $horario_fim, $turno, $id_turma)) {
    // Se já existe uma turma com os mesmos dados
    echo "<script>alert('Já existe uma turma com esses dados para outro instrutor.'); window.location.href='editarTurma.php?id=" . htmlspecialchars($id_turma) . "';</script>";
} else {
    // Atualizar os dados da turma
    $sql_update = "UPDATE turmas 
                   SET local = :local, instrutor = :instrutor, data_inicio = :data_inicio, data_fim = :data_fim, horario_inicio = :horario_inicio, horario_fim = :horario_fim, turno = :turno 
                   WHERE id = :id_turma";
    
    $stmt_update = $conn->prepare($sql_update);
    
    // Associa os parâmetros da consulta usando bindParam
    $stmt_update->bindParam(':local', $local, PDO::PARAM_STR);
    $stmt_update->bindParam(':instrutor', $instrutor, PDO::PARAM_STR);
    $stmt_update->bindParam(':data_inicio', $data_inicio, PDO::PARAM_STR);
    $stmt_update->bindParam(':data_fim', $data_fim, PDO::PARAM_STR);
    $stmt_update->bindParam(':horario_inicio', $horario_inicio, PDO::PARAM_STR);
    $stmt_update->bindParam(':horario_fim', $horario_fim, PDO::PARAM_STR);
    $stmt_update->bindParam(':turno', $turno, PDO::PARAM_STR);
    $stmt_update->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);

    // Executa a consulta
    if ($stmt_update->execute()) {
        echo "<script>alert('Turma atualizada com sucesso.'); window.location.href='legislacao.php';</script>";
    } else {
        echo "<script>alert('Erro ao atualizar a turma.'); window.location.href='editarTurma.php?id=" . htmlspecialchars($id_turma) . "';</script>";
    }
}
?>
