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

// Configurações de conexão com o banco de dados
$host = 'localhost'; // Ou o endereço do seu servidor de banco de dados
$db = 'autoescola';
$user = 'root';
$pass = '';

// Conectar ao banco de dados
$conn = new mysqli($host, $user, $pass, $db);

// Verificar a conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Processar os dados do formulário
$cep = $_POST['cep'];
$telefone = $_POST['telefone'];
$endereco = $_POST['endereco'];
$bairro = $_POST['bairro'];
$numero = $_POST['numero'];
$cidade = $_POST['cidade'];
$complemento = $_POST['complemento'];
$uf = $_POST['uf'];

// Inserir os dados na tabela de endereço
$sql_endereco = "INSERT INTO enderecos (cep, telefone, endereco, bairro, numero, cidade, complemento, uf) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt_endereco = $conn->prepare($sql_endereco);
$stmt_endereco->bind_param('ssssssss', $cep, $telefone, $endereco, $bairro, $numero, $cidade, $complemento, $uf);

if ($stmt_endereco->execute()) {
    // Redirecionar para uma página de sucesso ou qualquer outra página
    echo "<script>alert('Dados inseridos com sucesso.'); window.location.href='enderecoEmpresa.php';</script>";
} else {
    echo "Erro ao inserir endereço: " . $stmt_endereco->error;
}

// Fechar a declaração e a conexão
$stmt_endereco->close();
$conn->close();
?>
