<?php

    session_start(); // Inicia a sessão

    // Verifica se os dados do formulário foram enviados
    if (isset($_POST['email']) && isset($_POST['senha'])) {
        // Inclui a conexão com o banco de dados
        include 'conexao.php';

        // Dados fornecidos pelo usuário durante o login
        $email = $_POST['email'];
        $senha = $_POST['senha'];

        // Criptografando a senha fornecida com SHA-256
        $senha_hash = hash('sha256', $senha);

        try {
            // Atualize o nome da coluna para "senha_hash" ou o nome correto
            $query = "SELECT id, nome, nivel FROM usuarios WHERE email = :email AND senha_hash = :senha";
            $stmt = $conn->prepare($query);

            // Vincula os parâmetros
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':senha', $senha_hash, PDO::PARAM_STR);

            // Executa a consulta
            $stmt->execute();

            // Verifica se encontrou o usuário
            if ($stmt->rowCount() > 0) {
                // Obtém os dados do usuário
                $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

                // Armazena os dados do usuário na sessão
                $_SESSION['id_usuario'] = $usuario['id'];
                $_SESSION['nome_usuario'] = $usuario['nome'];
                $_SESSION['nivel'] = $usuario['nivel'];

                // Redireciona para a página principal do sistema
                header("Location: animate.php");
                exit(); // Encerra o script após o redirecionamento
            } else {
                // Login inválido
                echo "<script>alert('E-mail ou senha inválidos. Tente novamente.');</script>";
                echo "<script>window.location.href = 'index.php';</script>";
            }
        } catch (PDOException $e) {
            // Trata erros na execução da consulta
            echo "Erro ao realizar o login: " . $e->getMessage();
        }
    } else {
        // Dados do formulário não foram enviados
        echo "Por favor, preencha todos os campos do formulário.";
    }
    
?>
