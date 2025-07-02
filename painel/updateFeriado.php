<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador, presidente ou suporte
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel']; // Nível de usuário armazenado na sessão

        // Verificar se o nível de usuário é admin, presidente ou suporte
        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true;
        }
    }

    // Redireciona caso o usuário não tenha permissão
    header("Location: loader.php");
    exit();
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

include 'conexao.php'; // Inclui a conexão com o banco de dados

// Verificar se o formulário foi enviado corretamente
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $data = $_POST['feriado'];
    $descricao = $_POST['descricaoFeriado'];

    try {
        // Atualizar o feriado no banco de dados
        $sql = "UPDATE feriados SET data = :data, descricao = :descricao WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':data', $data);
        $stmt->bindParam(':descricao', $descricao);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "<script>alert('Feriado atualizado com sucesso!'); window.location.href='feriadosCadastrados.php';</script>";
        } else {
            echo "<script>alert('Erro ao atualizar o feriado.'); window.history.back();</script>";
        }
    } catch (PDOException $e) {
        echo "Erro ao atualizar o feriado: " . $e->getMessage();
        exit();
    }
} else {
    echo "<script>alert('Requisição inválida.'); window.history.back();</script>";
}
?>
