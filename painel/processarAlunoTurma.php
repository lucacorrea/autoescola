<?php
// Iniciar a sessão
session_start();

// Função para verificar se o usuário está logado como administrador ou presidente
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true; // O usuário tem permissão para acessar
        }
    }
    
    header("Location: loader.php");
    exit();
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

// Incluir o arquivo de conexão
include 'conexao.php'; // Certifique-se de incluir corretamente o arquivo de conexão

try {
    // Use a variável $conn que é a conexão PDO
    $conn->beginTransaction(); // Inicia a transação
    // Outras operações aqui
    $conn->commit(); // Para confirmar as alterações
} catch (Exception $e) {
    $conn->rollBack(); // Se algo der errado, reverte a transação
    echo "Erro: " . $e->getMessage();
}

// Função para verificar conflitos de horários
function verificarConflitoHorario($conn, $id_aluno, $id_turma) {
    // Obter o horário e dados da turma a ser inserida
    $sqlHorarioTurma = "SELECT horario_inicio, horario_fim, data_inicio, data_fim, local, instrutor FROM turmas WHERE id = :id_turma";
    $stmtHorarioTurma = $conn->prepare($sqlHorarioTurma);
    $stmtHorarioTurma->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
    $stmtHorarioTurma->execute();
    $horarioTurma = $stmtHorarioTurma->fetch(PDO::FETCH_ASSOC);

    if (!$horarioTurma) {
        throw new Exception("Horário da turma não encontrado.");
    }

    return $horarioTurma;
}

// Verificar e processar o formulário
if (isset($_POST['id_turma']) && !empty($_POST['aluno_turma']) && is_array($_POST['aluno_turma'])) {
    $id_turma = $_POST['id_turma'];
    $alunosSelecionados = $_POST['aluno_turma'];

    if (!filter_var($id_turma, FILTER_VALIDATE_INT)) {
        die("ID da turma inválido.");
    }

    // Validar os IDs dos alunos
    foreach ($alunosSelecionados as $id_aluno) {
        if (!filter_var($id_aluno, FILTER_VALIDATE_INT)) {
            die("ID do aluno inválido: $id_aluno.");
        }
    }

    try {
        $conn->beginTransaction(); // Iniciar a transação com $conn

        $sql = "INSERT INTO alunos_turmas (id_aluno, nome_aluno, id_turma) VALUES (:id_aluno, :nome_aluno, :id_turma)";
        $stmt = $conn->prepare($sql);

        $sqlVerifica = "SELECT 1 FROM alunos_turmas WHERE id_aluno = :id_aluno AND id_turma = :id_turma";
        $stmtVerifica = $conn->prepare($sqlVerifica);

        // Inserir cada aluno na turma
        foreach ($alunosSelecionados as $id_aluno) {
            $sqlNome = "SELECT nome FROM alunos WHERE id = :id_aluno";
            $stmtNome = $conn->prepare($sqlNome);
            $stmtNome->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
            $stmtNome->execute();
            $nomeAluno = $stmtNome->fetchColumn();
            
            if ($nomeAluno !== false) {
                // Verificar se o aluno já está cadastrado na turma
                $stmtVerifica->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
                $stmtVerifica->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
                $stmtVerifica->execute();
                
                if ($stmtVerifica->rowCount() > 0) {
                    echo "<script>alert('O aluno $nomeAluno já está cadastrado nesta turma.'); window.location.href='inserirAlunos.php?id_turma=$id_turma';</script>";
                    $conn->rollBack(); // Reverter a transação em caso de erro
                    exit();
                }

                // Verificar conflito de horários
                if (verificarConflitoHorario($conn, $id_aluno, $id_turma)) {
                    echo "<script>alert('Conflito de horários: O aluno $nomeAluno está matriculado em outra turma com horário em conflito e com o mesmo período.'); window.location.href='inserirAlunos.php?id_turma=$id_turma';</script>";
                    $conn->rollBack(); // Reverter a transação em caso de erro
                    exit();
                }

                // Inserir aluno na tabela alunos_turmas
                $stmt->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
                $stmt->bindParam(':nome_aluno', $nomeAluno, PDO::PARAM_STR);
                $stmt->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
                $stmt->execute();
            } else {
                throw new Exception("Nome do aluno não encontrado para ID: $id_aluno.");
            }
        }

        // Confirmar transação
        $conn->commit(); // Finaliza a transação
        echo "<script>alert('Alunos inseridos com sucesso.'); window.location.href='legislacao.php';</script>";

    } catch (PDOException $e) {
        $conn->rollBack(); // Reverter em caso de erro no PDO
        die("Erro ao inserir alunos na turma: " . $e->getMessage());
    } catch (Exception $e) {
        $conn->rollBack(); // Reverter em caso de erro geral
        die($e->getMessage());
    }
} else {
    die("Nenhum aluno foi selecionado ou ID da turma não foi fornecido.");
}
?>
