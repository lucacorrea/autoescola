<?php

//---------------------------SESSION--------------------------------

    session_start();

//---------------------------END SESSION----------------------------


//--------------------------PROCESSAR FORMULARIO--------------------

    include './painel/conexao.php';

    //----------------------EMAIL USER------------------------------

        function enviarEmailBoasVindas($nome, $email) {
            $assunto = "Bem-vindo ao Sistema da Autoescola Dinâmica";

            $mensagem = "
            <html>
            <head>
                <title>Bem-vindo!</title>
            </head>
            <style>
                p{
                    font-size: 25px;
                }
            </style>
            <body>
                <p>Olá, <strong>$nome</strong>,</p>
                <p>Você foi cadastrado com sucesso no sistema da Autoescola Dinâmica.</p>
                <p>Agora você pode acessar o sistema usando seu e-mail e a senha cadastrada.</p>
                <br>
                <p>Atenciosamente,</p>
                <p>Equipe Autoescola Dinâmica</p>
            </body>
            </html>
            ";

            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8" . "\r\n"; 
            $headers .= "From: Autoescola Dinâmica <no-reply@autoescola.com>\r\n";
            $headers .= "Reply-To: autoescoladinamica918@gmail.com\r\n"; 

            return mail($email, $assunto, $mensagem, $headers);
        }

    //----------------------END EMAIL USER--------------------------

    //----------------------POST------------------------------------

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nomeAluno = trim($_POST['nome_aluno'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $cpf_aluno = trim($_POST['cpf_aluno'] ?? '');
            $senha_fornecida = trim($_POST['senha'] ?? '');

            if (empty($nomeAluno) || empty($email) || empty($cpf_aluno) || empty($senha_fornecida)) {
                echo "<script>alert('Todos os campos são obrigatórios.');</script>";
                echo "<script>window.location.href = 'criarConta.php';</script>";
                exit();
            }

            $senha_hash = hash('sha256', $senha_fornecida);

            $sql = "SELECT id FROM login_aluno WHERE email = :email";
            $stmt = $conn->prepare($sql); 
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->execute();
            $existingUser = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existingUser) {
                echo "<script>alert('Este e-mail já está cadastrado.');</script>";
                echo "<script>window.location.href = 'criarConta.php';</script>";
                exit();
            }

            $sql = "INSERT INTO login_aluno (nome_aluno, email, cpf_aluno, senha_hash) VALUES (:nome_aluno, :email, :cpf_aluno, :senha_hash)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome_aluno', $nomeAluno, PDO::PARAM_STR);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR);
            $stmt->bindParam(':cpf_aluno', $cpf_aluno, PDO::PARAM_STR);
            $stmt->bindParam(':senha_hash', $senha_hash, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $userId = $conn->lastInsertId();

                if (enviarEmailBoasVindas($nomeAluno, $email)) {
                    echo "<script>alert('Cadastro realizado com sucesso!');</script>";
                } else {
                    echo "<script>alert('Cadastro realizado, mas houve um erro ao enviar o e-mail.');</script>";
                }
                
                //------------------EMAIL ADMIN--------------------

                    $email_admin = "lucasscorrea396@gmail.com";
                    $assunto_admin = "Novo aluno cadastrado";
                    $mensagem_admin = "O aluno $nomeAluno foi cadastrado com sucesso.\r\nEmail: $email";
                    mail($email_admin, $assunto_admin, $mensagem_admin);

                //-------------------END EMAIL ADMIN---------------

                echo "<script>window.location.href = 'login.php';</script>";
            } else {
                echo "<script>alert('Erro ao cadastrar o aluno.');</script>";
                echo "<script>window.location.href = 'criarConta.php';</script>";
            }
        } else {

    //----------------------END POST--------------------------------

        header('Location: index.php');
        exit();
    }

//--------------------------END PROCESSAR FORMULARIO---------------

?>