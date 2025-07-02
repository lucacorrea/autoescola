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


// Inclui a conexão com o banco de dados
include 'conexao.php';

// Obtendo o ID do usuário da sessão
session_start();
$id_usuario = $_SESSION['id_usuario'] ?? null;

if (!$id_usuario) {
    die("Usuário não autenticado.");
}

try {
    // Consulta para obter o nome e o email do usuário
    $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    // Verifica se encontrou resultados
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario) {
        $nome_usuario = $usuario['nome'];
        $email_usuario = $usuario['email'];
    } else {
        echo "Nenhum resultado encontrado.";
    }
} catch (PDOException $e) {
    echo "Erro ao executar a consulta: " . $e->getMessage();
}

// Você pode usar $nome_usuario e $email_usuario no restante do código


// Verificar se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Corrigir o nome do campo para nome_instrutor
    $nome_instrutor = !empty($_POST['nome_instrutor']) ? $_POST['nome_instrutor'] : null;
    $placa = !empty($_POST['placa']) ? $_POST['placa'] : null;

    $placaCadastrada = false;

        // Inserir dados no banco de dados, se a placa não estiver cadastrada
        if ($nome_instrutor && !$placaCadastrada) {
            try {
                // Inserir o instrutor na tabela instrutores
                $queryInserirInstrutor = "INSERT INTO instrutores (nome_instrutor, placa_instrutor) VALUES (:nome_instrutor, :placa)";
                $stmtInserirInstrutor = $conn->prepare($queryInserirInstrutor);
                $stmtInserirInstrutor->bindParam(':nome_instrutor', $nome_instrutor, PDO::PARAM_STR);
                $stmtInserirInstrutor->bindParam(':placa', $placa, PDO::PARAM_STR);
                $stmtInserirInstrutor->execute();
        
                // Inserir a placa na tabela placas
                $queryInserirPlaca = "INSERT INTO placas (placa) VALUES (:placa)";
                $stmtInserirPlaca = $conn->prepare($queryInserirPlaca);
                $stmtInserirPlaca->bindParam(':placa', $placa, PDO::PARAM_STR);
                $stmtInserirPlaca->execute();
        
                // Mensagem de sucesso
                $mensagem = 'Instrutor e placa cadastrados com sucesso!';
                echo "<script>alert('$mensagem'); window.location.href='cadastrarInstrutorPlaca.php';</script>";
            } catch (PDOException $e) {
                die("Erro ao inserir dados: " . $e->getMessage());
            }
        }
    }
        
?>