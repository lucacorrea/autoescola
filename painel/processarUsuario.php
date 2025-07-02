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
// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);
    $confirma_senha = trim($_POST['confirma_senha']);

    // Verifica se as senhas coincidem
    if ($senha !== $confirma_senha) {
        echo "<script>alert('As senhas não coincidem. Por favor, tente novamente.');</script>";
        echo "<script>window.location.href = 'cadastroUsuarios.php';</script>";
        exit(); // Encerra o script para não continuar com a execução
    }

    // Hash da senha usando SHA-256
    $senha_hash = hash('sha256', $senha);

    // Define o nível como 'admin'
    $nivel = 'admin';

    // Prepara e executa a consulta SQL para inserção
    $sql = "INSERT INTO usuarios (nome, email, senha_hash, nivel) VALUES (:nome, :email, :senha_hash, :nivel)";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':senha_hash', $senha_hash);
    $stmt->bindParam(':nivel', $nivel);

    if ($stmt->execute()) {
        // Envia uma mensagem de sucesso e redireciona para a página de login
        echo "<script>alert('Cadastro realizado com sucesso.'); window.location.href='cadastroUsuarios.php';</script>";
    } else {
        echo "Erro ao cadastrar usuário.";
    }
}

?>
