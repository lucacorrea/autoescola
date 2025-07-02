<?php

//--------------------SESSION--------------------------

    session_start();

//--------------------END SESSION----------------------

//--------------------PRCESSAR FORMULARIO--------------

    include "./painel/conexao.php";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = trim($_POST['email'] ?? '');
        $senha_fornecida = trim($_POST['senha'] ?? '');

        if (empty($email) || empty($senha_fornecida)) {
            header('Location: login.php?error=empty_fields');
            exit();
        }

        $senha_hash = hash('sha256', $senha_fornecida);

        try {
            $sql = "SELECT * FROM login_aluno WHERE email = :email AND senha_hash = :senha_hash";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':senha_hash', $senha_hash, PDO::PARAM_STR);
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                session_regenerate_id(true);
                $_SESSION['logged_in'] = true;
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['nome_aluno'];

                header('Location: animate.php');
                exit();
            } else {
                echo "<script>alert('Email ou Senha Incorretos. Tente novamente.');</script>";
                echo "<script>window.location.href = 'login.php';</script>";
            }
        } catch (PDOException $e) {
            error_log("Erro no login: " . $e->getMessage());
            echo "<script>alert('Erro no servidor. Tente novamente mais tarde.');</script>";
            echo "<script>window.location.href = 'login.php';</script>";
        }
    } else {
        header('Location: login.php');
        exit();
    }

//--------------------END PROCESSAR FORMULARIO---------

?>
