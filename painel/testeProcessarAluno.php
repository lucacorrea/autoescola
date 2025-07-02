<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador, presidente ou suporte
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

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

// Conexão com o banco de dados
$servername = "localhost";
$username = "cfcaut82_autoescola";
$password = "Bt~fC13X5k{l";
$dbname = "cfcaut82_autoescola";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se a conexão foi bem-sucedida
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Função para exibir uma mensagem de erro com JavaScript e redirecionar de volta ao formulário
function showErrorAndRedirect($message) {
    echo "<script>alert('$message'); window.history.back();</script>";
    exit();
}

// Função para enviar e-mails de boas-vindas
function enviarEmailBoasVindas($nome, $email) {
    $assunto = "Bem-vindo ao Sistema da Autoescola Dinâmica";
    $mensagem = "
    <html>
    <head>
        <title>Bem-vindo!</title>
    </head>
    <style>
        p {
            font-size: 18px;
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

    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Autoescola Dinâmica <no-reply@autoescola.com>\r\n";
    $headers .= "Reply-To: autoescoladinamica918@gmail.com\r\n";

    return mail($email, $assunto, $mensagem, $headers);
}


// Função para lidar com o upload de imagem
function uploadFoto($foto) {
    $target_dir = "uploads/"; // Pasta onde as fotos serão armazenadas
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Cria o diretório se ele não existir
    }
    $target_file = $target_dir . basename($foto["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar se o arquivo é uma imagem real
    $check = getimagesize($foto["tmp_name"]);
    if ($check === false) {
        return [false, "O arquivo não é uma imagem válida."];
    }

    // Verificar o tamanho do arquivo (limite de 8MB)
    if ($foto["size"] > 8000000) {
        return [false, "Desculpe, o arquivo é muito grande. O limite é de 8MB."];
    }

    // Permitir apenas determinados formatos de arquivo
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        return [false, "Desculpe, apenas arquivos JPG, JPEG, PNG e GIF são permitidos."];
    }

    // Tentar fazer o upload do arquivo
    if (!move_uploaded_file($foto["tmp_name"], $target_file)) {
        return [false, "Desculpe, houve um erro ao fazer o upload da sua foto."];
    }

    return [true, $target_file]; // Retorna o caminho do arquivo em caso de sucesso
}

// Função para lidar com o upload de documentos
function uploadDocumento($documento) {
    $target_dir = "uploads/documentos/"; // Pasta onde os documentos serão armazenados
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0777, true); // Cria o diretório se ele não existir
    }
    
    // Converte o nome do arquivo para um formato seguro
    $nomeArquivoSeguro = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $documento["name"]);
    $target_file = $target_dir . basename($nomeArquivoSeguro);
    $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar o tipo de arquivo (permitido: PDF, DOCX, DOC)
    if ($fileType != "pdf" && $fileType != "docx" && $fileType != "doc") {
        return [false, "Desculpe, apenas arquivos PDF, DOCX e DOC são permitidos."];
    }

    // Verificar o tamanho do arquivo (limite de 8MB)
    if ($documento["size"] > 8000000) {
        return [false, "Desculpe, o arquivo é muito grande. O limite é de 8MB."];
    }

    // Tentar fazer o upload do arquivo
    if (!move_uploaded_file($documento["tmp_name"], $target_file)) {
        return [false, "Desculpe, houve um erro ao fazer o upload do seu documento."];
    }

    return [true, $target_file]; // Retorna o caminho do arquivo em caso de sucesso
}

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Coleta e sanitiza os dados do formulário
    $nome_aluno = strtoupper(trim($_POST['nome_aluno'] ?? ''));
    $rg_aluno = strtoupper(trim($_POST['rg_aluno'] ?? ''));
    $cpf_aluno = strtoupper(trim($_POST['cpf_aluno'] ?? ''));
    $data_nascimento_aluno = $_POST['data_nascimento_aluno'] ?? null;
    $telefone_aluno = trim($_POST['telefone_aluno'] ?? '');
    $renach_aluno = strtoupper(trim($_POST['renach_aluno'] ?? ''));
    $ladv_aluno = isset($_POST['ladv_aluno']) && $_POST['ladv_aluno'] !== '' ? $_POST['ladv_aluno'] : null;
    $vencimento_processo_aluno = $_POST['vencimento_processo_aluno'] ?? null;
    $rua_aluno = strtoupper(trim($_POST['rua_aluno'] ?? ''));
    $bairro_aluno = strtoupper(trim($_POST['bairro_aluno'] ?? ''));
    $numero_aluno = strtoupper(trim($_POST['numero_aluno'] ?? ''));
    $observacao_aluno = strtoupper(trim($_POST['observacao_aluno'] ?? ''));
    $email_aluno = trim($_POST['email_aluno'] ?? '');
    $senha_aluno = trim($_POST['senha_aluno'] ?? '');
    $confirmar_senha_aluno = trim($_POST['confirmar_senha_aluno'] ?? '');

    // Verifica se as senhas coincidem
    if ($senha_aluno !== $confirmar_senha_aluno) {
        showErrorAndRedirect("As senhas não coincidem.");
    }

    // Verifica se os campos obrigatórios estão preenchidos
    if (empty($nome_aluno) || empty($rg_aluno) || empty($cpf_aluno) || empty($data_nascimento_aluno) || empty($renach_aluno) || empty($vencimento_processo_aluno)) {
        showErrorAndRedirect("Por favor, preencha os campos obrigatórios.");
    }

    // Verifica se o CPF já está cadastrado
    $sql_verifica_cpf = "SELECT id FROM alunos WHERE cpf = ?";
    $stmt_verifica_cpf = $conn->prepare($sql_verifica_cpf);
    if (!$stmt_verifica_cpf) {
        showErrorAndRedirect("Erro ao preparar a consulta de verificação de CPF: " . $conn->error);
    }
    $stmt_verifica_cpf->bind_param('s', $cpf_aluno);
    $stmt_verifica_cpf->execute();
    $stmt_verifica_cpf->store_result();

    if ($stmt_verifica_cpf->num_rows > 0) {
        showErrorAndRedirect("Um aluno com este CPF já está cadastrado.");
    }

    // Verifica se uma foto foi carregada e faça o upload
    if (isset($_FILES['foto_aluno']) && $_FILES['foto_aluno']['error'] == 0) {
        list($uploadOk, $uploadMessage) = uploadFoto($_FILES['foto_aluno']);
        if (!$uploadOk) {
            showErrorAndRedirect($uploadMessage);
        }
        $foto_aluno = $uploadMessage; // Caminho da foto carregada
    } else {
        $foto_aluno = null; // Caso o upload não tenha sido feito
    }

    // Verifica se um documento foi carregado e faça o upload
    if (isset($_FILES['documento_aluno']) && $_FILES['documento_aluno']['error'] == 0) {
        list($uploadOk, $uploadMessage) = uploadDocumento($_FILES['documento_aluno']);
        if (!$uploadOk) {
            showErrorAndRedirect($uploadMessage);
        }
        $documento_aluno = $uploadMessage; // Caminho do documento carregado
    } else {
        $documento_aluno = null; // Caso o upload não tenha sido feito
    }

    // Se o CPF não estiver cadastrado, procede com o cadastro
    // Inicia uma transação
    $conn->begin_transaction();

    try {
        // Insere os dados na tabela 'alunos'
        $sql_aluno = "INSERT INTO alunos (nome, rg, cpf, data_nascimento, telefone, renach, ladv, vencimento_processo, rua, bairro, numero, observacao, foto, documento)
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_aluno = $conn->prepare($sql_aluno);
        if (!$stmt_aluno) {
            throw new Exception("Erro na preparação da consulta de alunos: " . $conn->error);
        }
        $stmt_aluno->bind_param('ssssssssssssss', $nome_aluno, $rg_aluno, $cpf_aluno, $data_nascimento_aluno, $telefone_aluno, $renach_aluno, $ladv_aluno, $vencimento_processo_aluno, $rua_aluno, $bairro_aluno, $numero_aluno, $observacao_aluno, $foto_aluno, $documento_aluno);
        $stmt_aluno->execute();

        // Insere os dados de login na tabela 'login_aluno'
        if (!empty($email_aluno) && !empty($senha_aluno)) {
            $senha_hash = hash('sha256', $senha_aluno);
            $sql_login = "INSERT INTO login_aluno (nome_aluno, email, senha_hash, cpf_aluno, status_cadastro) VALUES (?, ?, ?, ?, 'Cadastrado')";
            $stmt_login = $conn->prepare($sql_login);
            if (!$stmt_login) {
                throw new Exception("Erro na preparação da consulta de login: " . $conn->error);
            }
            $stmt_login->bind_param('ssss', $nome_aluno, $email_aluno, $senha_hash, $cpf_aluno);
            $stmt_login->execute();

            // Envia o e-mail de boas-vindas
            if (!enviarEmailBoasVindas($nome_aluno, $email_aluno)) {
                throw new Exception("Erro ao enviar o e-mail de boas-vindas.");
            }

            // Envia notificação ao administrador
            $email_admin = "lucasscorrea396@gmail.com";
            $assunto_admin = "Novo aluno cadastrado";
            $mensagem_admin = "O aluno $nome_aluno foi cadastrado com sucesso.\r\nemail: $email_aluno";
            mail($email_admin, $assunto_admin, $mensagem_admin);
        }

        // Confirma a transação
        $conn->commit();

        // Redireciona para a página de sucesso
        echo "<script>alert('Aluno cadastrado com sucesso!'); window.location.href='cadastroAluno.php';</script>";

    } catch (Exception $e) {
        // Reverte a transação em caso de erro
        $conn->rollback();
        showErrorAndRedirect("Erro ao cadastrar o aluno: " . $e->getMessage());
    }
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
