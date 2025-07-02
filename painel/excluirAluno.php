<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador, presidente ou suporte
function verificarAcesso() {
    if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel'];
        if($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true;
        }
    }
    header("Location: loader.php");
    exit(); // Encerra o script após o redirecionamento
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

// Verifica se o ID do aluno foi passado pela URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Inicia uma transação
    $conn->begin_transaction();

    try {
        // Remove registros relacionados na tabela `servicos_aluno`
        $stmt = $conn->prepare("DELETE FROM servicos_aluno WHERE nome_aluno IN (SELECT nome FROM alunos WHERE id = ?)");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Remove registros relacionados na tabela `login_aluno`
        $stmt = $conn->prepare("DELETE FROM login_aluno WHERE nome_aluno IN (SELECT nome FROM alunos WHERE id = ?)");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Remove registros relacionados na tabela `fichas`
        $stmt = $conn->prepare("DELETE FROM fichas WHERE nome IN (SELECT nome FROM alunos WHERE id = ?)");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Remove registros relacionados na tabela `info_parcelas`
        $stmt = $conn->prepare("DELETE FROM info_parcelas WHERE nome_aluno IN (SELECT nome FROM alunos WHERE id = ?)");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Remove registros relacionados na tabela `alunos_turmas`
        $stmt = $conn->prepare("DELETE FROM alunos_turmas WHERE id_aluno = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Remove o aluno da tabela principal `alunos`
        $stmt = $conn->prepare("DELETE FROM alunos WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();

        // Se tudo foi bem-sucedido, comita a transação
        $conn->commit();

        echo "<script>alert('Aluno excluído com sucesso!'); window.location.href='alunos.php';</script>";
    } catch (Exception $e) {
        // Em caso de erro, desfaz a transação
        $conn->rollback();
        echo "Erro ao excluir aluno e registros relacionados: " . $e->getMessage();
    }
} else {
    echo "ID do aluno não especificado.";
}

// Fecha a conexão
$conn->close();
?>
