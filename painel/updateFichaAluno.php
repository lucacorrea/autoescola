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

include "conexao.php";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Coleta os dados do formulário
    $id_ficha = isset($_POST['id_ficha']) ? intval($_POST['id_ficha']) : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $nome = isset($_POST['nome']) ? $_POST['nome'] : '';
    $rg = isset($_POST['rg']) ? $_POST['rg'] : '';
    $cpf = isset($_POST['cpf']) ? $_POST['cpf'] : '';
    $ladv = isset($_POST['ladv']) ? $_POST['ladv'] : '';
    $vencimento_processo = isset($_POST['vencimento_processo']) ? $_POST['vencimento_processo'] : '';
    $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
    $instrutor = isset($_POST['instrutor']) ? $_POST['instrutor'] : '';
    $placa = isset($_POST['placa']) ? $_POST['placa'] : '';
    $registro = isset($_POST['registro']) ? $_POST['registro'] : '';
    $horario_inicio = isset($_POST['horario_inicio']) ? $_POST['horario_inicio'] : '';
    $horario_fim = isset($_POST['horario_fim']) ? $_POST['horario_fim'] : '';
    $data_ficha = isset($_POST['data_ficha']) ? $_POST['data_ficha'] : '';

    // Verifica se a data é um domingo
    if (date('w', strtotime($data_ficha)) == 0) { // 0 representa domingo
        echo "<script>alert('A data selecionada é um domingo. Não é possível atualizar a ficha.'); window.location.href='editarFicha.php?id={$id_ficha}';</script>";
        exit();
    }

    try {
        include 'conexao.php';

        // Verifica se a data é um feriado
        $query_feriado = "SELECT COUNT(*) AS count FROM feriados WHERE data = ?";
        $stmt = $conn->prepare($query_feriado);
        $stmt->execute([$data_ficha]);
        $feriado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($feriado['count'] > 0) {
            echo "<script>alert('A data informada é um feriado. Não é possível atualizar a ficha.'); window.location.href='editarFicha.php?id={$id_ficha}';</script>";
            exit();
        }

        // Verifica se já existe uma ficha com o mesmo instrutor, placa, horário e data
        $query_verifica = "SELECT id FROM fichas WHERE instrutor = ? AND placa = ? AND horario_inicio = ? AND horario_fim = ? AND data_ficha = ? AND id != ?";
        $stmt = $conn->prepare($query_verifica);
        $stmt->execute([$instrutor, $placa, $horario_inicio, $horario_fim, $data_ficha, $id_ficha]);
        $verifica = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($verifica) {
            echo "<script>alert('Já existe uma ficha cadastrada com o mesmo instrutor, placa, horário e data.'); window.location.href='editarFicha.php?id={$id_ficha}';</script>";
            exit();
        }

        // Verifica se já existe um conflito de horário com outro aluno
        $query_conflito = "
        SELECT id FROM fichas 
        WHERE instrutor = ? 
        AND placa = ? 
        AND (
            (horario_inicio < ? AND horario_fim > ?) OR
            (horario_inicio < ? AND horario_fim > ?)
        ) 
        AND data_ficha = ? 
        AND id != ?";
        $stmt = $conn->prepare($query_conflito);
        $stmt->execute([$instrutor, $placa, $horario_fim, $horario_inicio, $horario_inicio, $horario_fim, $data_ficha, $id_ficha]);
        $conflito = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($conflito) {
            echo "<script>alert('Conflito de horário: já existe uma ficha cadastrada com o mesmo instrutor, placa e horários sobrepostos.'); window.location.href='editarFicha.php?id={$id_ficha}';</script>";
            exit();
        }

        // Atualiza a ficha no banco de dados
        $query = "UPDATE fichas SET status = ?, nome = ?, rg = ?, cpf = ?, ladv = ?, vencimento_processo = ?, categoria = ?, instrutor = ?, placa = ?, registro = ?, horario_inicio = ?, horario_fim = ?, data_ficha = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        if ($stmt->execute([$status, $nome, $rg, $cpf, $ladv, $vencimento_processo, $categoria, $instrutor, $placa, $registro, $horario_inicio, $horario_fim, $data_ficha, $id_ficha])) {
            // Obter o ID do aluno baseado no RG da ficha
            $query_aluno = "SELECT id FROM alunos WHERE rg = ?";
            $stmt_aluno = $conn->prepare($query_aluno);
            $stmt_aluno->execute([$rg]);
            $id_aluno = $stmt_aluno->fetchColumn();

            if ($id_aluno) {
                echo "<script>alert('Ficha atualizada com sucesso!'); window.location.href = 'ficha.php?id={$id_aluno}';</script>";
            } else {
                echo "<script>alert('ID do aluno não encontrado para o RG fornecido.'); window.location.href = 'editarFicha.php?id={$id_ficha}';</script>";
            }
        } else {
            echo "<script>alert('Erro ao atualizar a ficha.'); window.location.href = 'editarFicha.php?id={$id_ficha}';</script>";
        }
    } catch (PDOException $e) {
        echo "<script>alert('Erro: " . $e->getMessage() . "'); window.location.href='editarFicha.php?id={$id_ficha}';</script>";
    }
}
?>

