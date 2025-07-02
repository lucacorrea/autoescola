<?php
// Iniciar a sessão
session_start(); 

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

// Função para verificar se já existe uma turma com os mesmos dados, exceto o instrutor
function verificarTurmaExistente($conn, $local, $data_inicio, $data_fim, $horario_inicio, $horario_fim, $turno) {
    $sql_check = "SELECT id FROM turmas 
                  WHERE local = :local 
                    AND data_inicio = :data_inicio 
                    AND data_fim = :data_fim 
                    AND horario_inicio = :horario_inicio 
                    AND horario_fim = :horario_fim 
                    AND turno = :turno";
    
    $stmt_check = $conn->prepare($sql_check);
    
    // Bind dos parâmetros usando PDO
    $stmt_check->bindParam(':local', $local);
    $stmt_check->bindParam(':data_inicio', $data_inicio);
    $stmt_check->bindParam(':data_fim', $data_fim);
    $stmt_check->bindParam(':horario_inicio', $horario_inicio);
    $stmt_check->bindParam(':horario_fim', $horario_fim);
    $stmt_check->bindParam(':turno', $turno);
    
    $stmt_check->execute();
    
    // Verifica se algum resultado foi retornado
    $exists = $stmt_check->rowCount() > 0;
    
    return $exists;
}


// Função para verificar se o horário da nova turma se sobrepõe a uma turma existente
function verificarSobreposicaoHorarios($conn, $local, $data_inicio, $data_fim, $horario_inicio, $horario_fim, $turno) {
    $sql_check = "SELECT id FROM turmas 
                  WHERE local = :local 
                    AND data_inicio <= :data_fim 
                    AND data_fim >= :data_inicio 
                    AND turno = :turno
                    AND (
                        (horario_inicio < :horario_fim AND horario_fim > :horario_inicio) OR
                        (horario_inicio < :horario_inicio AND horario_fim > :horario_fim) OR
                        (horario_inicio >= :horario_inicio AND horario_fim <= :horario_fim)
                    )";
    
    $stmt_check = $conn->prepare($sql_check);
    
    // Bind dos parâmetros usando PDO
    $stmt_check->bindParam(':local', $local);
    $stmt_check->bindParam(':data_fim', $data_fim);
    $stmt_check->bindParam(':data_inicio', $data_inicio);
    $stmt_check->bindParam(':turno', $turno);
    $stmt_check->bindParam(':horario_inicio', $horario_inicio);
    $stmt_check->bindParam(':horario_fim', $horario_fim);
    
    $stmt_check->execute();
    
    // Verifica se algum resultado foi retornado
    $sobreposicao = $stmt_check->rowCount() > 0;
    
    return $sobreposicao;
}



include "conexao.php";

// Processamento dos dados do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recupera os dados enviados pelo formulário
    $local = $_POST['local'] ?? '';
    $instrutor = $_POST['instrutor'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_fim = $_POST['data_fim'] ?? '';
    $horario_inicio = $_POST['horario_inicio'] ?? '';
    $horario_fim = $_POST['horario_fim'] ?? '';
    $turno = $_POST['turno'] ?? '';

    // Valida os campos obrigatórios
    if (empty($local) || empty($instrutor) || empty($data_inicio) || empty($data_fim) || empty($horario_inicio) || empty($horario_fim) || empty($turno)) {
        echo "<script>alert('Por favor, preencha todos os campos obrigatórios.'); window.location.href='cadastrarTurma.php';</script>";
        exit();
    }

    // Verifica se já existe uma turma com os mesmos dados, exceto o instrutor
    if (verificarTurmaExistente($conn, $local, $data_inicio, $data_fim, $horario_inicio, $horario_fim, $turno)) {
        // Se já existir uma turma com os mesmos dados
        echo "<script>alert('Já existe uma turma com esses dados.'); window.location.href='cadastrarTurma.php';</script>";
    } else if (verificarSobreposicaoHorarios($conn, $local, $data_inicio, $data_fim, $horario_inicio, $horario_fim, $turno)) {
        // Se houver sobreposição de horários
        echo "<script>alert('Há uma sobreposição de horários com outra turma existente.'); window.location.href='cadastrarTurma.php';</script>";
    } else {
        // Prepara a consulta SQL para inserção
        $sqlInserir = "INSERT INTO turmas (local, instrutor, data_inicio, data_fim, horario_inicio, horario_fim, turno) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmtInserir = $conn->prepare($sqlInserir);

        if ($stmtInserir) {
            // Usa PDO bindParam para passar os valores
            $stmtInserir->bindParam(1, $local);
            $stmtInserir->bindParam(2, $instrutor);
            $stmtInserir->bindParam(3, $data_inicio);
            $stmtInserir->bindParam(4, $data_fim);
            $stmtInserir->bindParam(5, $horario_inicio);
            $stmtInserir->bindParam(6, $horario_fim);
            $stmtInserir->bindParam(7, $turno);

            if ($stmtInserir->execute()) {
                // Recupera o ID da turma inserida
                $id_turma = $conn->lastInsertId();
                echo "<script>alert('Turma cadastrada com sucesso.'); window.location.href='inserirAlunos.php?id_turma=$id_turma';</script>";
            } else {
                echo "<script>alert('Erro ao cadastrar a turma.'); window.location.href='cadastrarTurma.php';</script>";
            }
        } else {
            echo "<script>alert('Erro ao preparar a consulta para inserção. Tente novamente.'); window.location.href='cadastrarTurma.php';</script>";
        }
    }
}

$conn = null; // Fechar a conexão PDO
?>
