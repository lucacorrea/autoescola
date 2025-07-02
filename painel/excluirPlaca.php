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

// Conexão com o banco de dados
$servername = "localhost";
$username = "cfcaut82_autoescola";
$password = "Bt~fC13X5k{l";
$dbname = "cfcaut82_autoescola";

// Criar a conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Configurar o charset para evitar problemas com acentuação
$conn->set_charset("utf8");

// Verificar se o ID da placa foi passado
if (isset($_GET['id'])) {
    $id_placa = $_GET['id'];

    // Preparar a consulta para deletar a placa
    $query = "DELETE FROM placas WHERE id = ?";
    if ($stmt = $conn->prepare($query)) {
        $stmt->bind_param("i", $id_placa);

        // Executar a consulta
        if ($stmt->execute()) {
            echo "<script>alert('Placa excluída com sucesso!'); window.location.href='placasCadastradas.php';</script>";
        } else {
            echo "<script>alert('Erro ao excluir a placa.'); window.history.back();</script>";
        }
    } else {
        die("Erro na preparação da consulta SQL: " . $conn->error);
    }

    // Fechar a conexão
    $stmt->close();
} else {
    echo "<script>alert('ID da placa não especificado.'); window.history.back();</script>";
}

$conn->close();
?>
