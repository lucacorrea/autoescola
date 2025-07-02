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

include 'conexao.php' ;

// Verifique se os dados foram enviados
if (isset($_POST['id_instrutor'], $_POST['nome_instrutor'], $_POST['placa_instrutor'])) {
    $id_instrutor = $_POST['id_instrutor'];
    $nome_instrutor = strtoupper($_POST['nome_instrutor']);
    $placa_instrutor = strtoupper($_POST['placa_instrutor']);

    try {
        // Verificar se a placa já existe para outro instrutor
        $sql_placa_verificacao = "SELECT id FROM instrutores WHERE placa_instrutor = :placa_instrutor AND id != :id_instrutor";
        $stmt_placa_verificacao = $conn->prepare($sql_placa_verificacao);
        $stmt_placa_verificacao->bindParam(':placa_instrutor', $placa_instrutor, PDO::PARAM_STR);
        $stmt_placa_verificacao->bindParam(':id_instrutor', $id_instrutor, PDO::PARAM_INT);
        $stmt_placa_verificacao->execute();

        if ($stmt_placa_verificacao->rowCount() > 0) {
            // Placa já está registrada para outro instrutor
            echo "<script>alert('A placa já está cadastrada para outro instrutor!'); window.history.back();</script>";
        } else {
            // Atualizar os dados na tabela instrutores
            $sql_instrutor = "UPDATE instrutores SET nome_instrutor = :nome_instrutor, placa_instrutor = :placa_instrutor WHERE id = :id_instrutor";
            $stmt_instrutor = $conn->prepare($sql_instrutor);
            $stmt_instrutor->bindParam(':nome_instrutor', $nome_instrutor, PDO::PARAM_STR);
            $stmt_instrutor->bindParam(':placa_instrutor', $placa_instrutor, PDO::PARAM_STR);
            $stmt_instrutor->bindParam(':id_instrutor', $id_instrutor, PDO::PARAM_INT);

            if ($stmt_instrutor->execute()) {
                // Atualizar a placa na tabela placas
                $sql_update_placa = "UPDATE placas SET data_cadastro = CURRENT_TIMESTAMP WHERE placa = :placa_instrutor";
                $stmt_update_placa = $conn->prepare($sql_update_placa);
                $stmt_update_placa->bindParam(':placa_instrutor', $placa_instrutor, PDO::PARAM_STR);
                $stmt_update_placa->execute();

                // Retorna mensagem de sucesso
                echo "<script>alert('Instrutor e placa atualizados com sucesso!'); window.location.href = 'instrutores.php';</script>";
            } else {
                // Retorna mensagem de erro em caso de falha no update
                echo "<script>alert('Erro ao atualizar o instrutor. Tente novamente.'); window.history.back();</script>";
            }
        }

    } catch (PDOException $e) {
        // Tratar erro da consulta
        echo "<script>alert('Erro ao processar a requisição: " . $e->getMessage() . "'); window.history.back();</script>";
    }
}


else
{
    // Retorna erro se faltarem dados no POST
    echo "<script>alert('Erro: Dados incompletos. Tente novamente.'); window.history.back();</script>";
}
?>
