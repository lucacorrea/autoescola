<?php
session_start(); // Inicia a sessão
// Inclui o arquivo de conexão e funções
include './conexao.php'; // Ajuste o caminho conforme necessário

// Chama a função para verificar o acesso do usuário
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'conexao.php'; // Inclui a conexão com o banco de dados

    // Obtém os valores do formulário
    $feriado = $_POST['feriado'];
    $descricaoFeriado = $_POST['descricaoFeriado'];

    try {
        // Verifica se o feriado já está cadastrado
        $sql_verifica = "SELECT id FROM feriados WHERE data = :feriado";
        $stmt_verifica = $conn->prepare($sql_verifica);
        $stmt_verifica->bindParam(':feriado', $feriado);
        $stmt_verifica->execute();

        if ($stmt_verifica->rowCount() > 0) {
            // Se o feriado já está cadastrado, exibe uma mensagem de erro
            echo "<script>alert('Este feriado já está cadastrado.'); window.history.back();</script>";
        } else {
            // Insere os dados no banco de dados
            $sql = "INSERT INTO feriados (data, descricao) VALUES (:feriado, :descricao)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':feriado', $feriado);
            $stmt->bindParam(':descricao', $descricaoFeriado);

            if ($stmt->execute()) {
                // Redireciona para a página de sucesso
                echo "<script>alert('Feriado cadastrado com sucesso.'); window.location.href='cadastrarFeriado.php';</script>";
            } else {
                echo "Erro ao cadastrar o feriado.";
            }
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}
?>
