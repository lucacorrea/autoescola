<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador ou presidente
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é admin, presidente ou suporte
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        if (in_array($nivel_usuario, ['admin', 'presidente', 'suporte'])) {
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

// Verifica se o método de requisição é POST e se os campos necessários foram enviados
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["pagamento"])) {

        include 'conexao.php';

        // Limpa todas as formas de pagamento existentes no banco de dados
        $sqlDelete = "DELETE FROM formas_pagamento";
        $conn->exec($sqlDelete);

        // Insere as novas formas de pagamento no banco de dados
        $pagamentos = $_POST["pagamento"];
        $stmtInsert = $conn->prepare("INSERT INTO formas_pagamento (forma) VALUES (:forma)");

        foreach ($pagamentos as $pagamento) {
            $stmtInsert->bindParam(':forma', $pagamento, PDO::PARAM_STR);
            $stmtInsert->execute();
        }

        // Redireciona de volta para a página do formulário com uma mensagem de sucesso
        echo "<script>alert('Dados inseridos com sucesso.'); window.location.href='formasPagamento.php';</script>";
 
    echo "Por favor, selecione pelo menos uma forma de pagamento.";
}
?>