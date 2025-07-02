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
$servername = "localhost";
$username = "root"; // Substitua com seu usuário do banco de dados
$password = ""; // Substitua com sua senha do banco de dados
$dbname = "autoescola"; // Substitua com o nome do seu banco de dados

// Criação da conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificação da conexão
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Inicializa variáveis com valores padrão
$cep = $telefone = $endereco = $bairro = $numero = $cidade = $complemento = $uf = "";
$id = ""; // Inicializa a variável ID

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebe os dados do formulário
    $cep = $_POST['cep'];
    $telefone = $_POST['telefone'];
    $endereco = $_POST['endereco'];
    $bairro = $_POST['bairro'];
    $numero = $_POST['numero'];
    $cidade = $_POST['cidade'];
    $complemento = $_POST['complemento'];
    $uf = $_POST['uf'];
    
    // Atualiza ou insere os dados
    if (!empty($_POST['id'])) {
        // Atualiza os dados existentes
        $id = $_POST['id'];
        $sql = "UPDATE enderecos SET cep=?, telefone=?, endereco=?, bairro=?, numero=?, cidade=?, complemento=?, uf=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        
        $stmt->bind_param("ssssssssi", $cep, $telefone, $endereco, $bairro, $numero, $cidade, $complemento, $uf, $id);
        $stmt->execute();
        $stmt->close();
        
        // Redireciona após atualização
        echo "<script>alert('Dados inseridos com sucesso!');</script>";
        echo "<script>window.location.href = 'enderecoEmpresa.php';</script>";
    } else {
        // Insere novos dados
        $sql = "INSERT INTO enderecos (cep, telefone, endereco, bairro, numero, cidade, complemento, uf) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssss", $cep, $telefone, $endereco, $bairro, $numero, $cidade, $complemento, $uf);
        $stmt->execute();
        $stmt->close();
        
        // Redireciona após inserção
        echo "<script>alert('Dados inseridos com sucesso!');</script>";
        echo "<script>window.location.href = 'enderecoEmpresa.php';</script>";
    }
}

// Fechamento da conexão
$conn->close();
?>
