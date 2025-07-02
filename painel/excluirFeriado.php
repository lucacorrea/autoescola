<?php
// Inclui o arquivo de conexão e funções
include './conexao.php'; // Ajuste o caminho conforme necessário

// Chama a função para verificar o acesso do usuário
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel'];
        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true;
        }
    }
    header("Location: loader.php");
    exit();
}


// Verifica se o ID foi passado via GET
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    try {
        // Prepara a consulta SQL para excluir o feriado
        $sqlDelete = "DELETE FROM feriados WHERE id = :id";
        $stmtDelete = $conn->prepare($sqlDelete);
        $stmtDelete->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmtDelete->execute()) {
            echo "<script>alert('Feriado excluído com sucesso.');</script>";
            echo "<script>window.location.href = 'feriadosCadastrados.php';</script>";
        } else {
            echo "<script>alert('Erro ao excluir o feriado.');</script>";
        }
    } catch (PDOException $e) {
        die("Erro ao excluir o feriado: " . $e->getMessage());
    }
} else {
    echo "<script>alert('ID do feriado não especificado.');</script>";
}
?>
