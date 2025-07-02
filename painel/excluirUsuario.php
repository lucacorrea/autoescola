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

// Configuração da conexão com o banco de dados
include 'conexao.php' ;

    // Verifica se o ID do usuário foi fornecido
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        // Prepara a consulta SQL para exclusão
        $sql = "DELETE FROM usuarios WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Redireciona para a página de usuários após a exclusão
            echo "<script>alert('Usuário excluído com sucesso!'); window.location.href='usuarios.php';</script>";
        } else {
            echo "Erro ao excluir o usuário.";
        }
    } else {
        echo "ID do usuário não fornecido.";
    }
$conn = null;
?>
