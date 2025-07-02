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

// Incluir a conexão com o banco de dados
include 'conexao.php';

// Supondo que você tenha o ID da turma disponível
$id_turma = $_GET['id'] ?? null; // Ou outra forma de obter o ID da turma

if (!$id_turma) {
    die("ID da turma não foi fornecido.");
}

// Preparar a consulta para pegar o horário de início, fim, local e instrutor
$sql = "SELECT horario_inicio, horario_fim, local, instrutor FROM turmas WHERE id = :id_turma";
$stmt = $conn->prepare($sql);

// Associar o parâmetro
$stmt->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);

// Executar a consulta
$stmt->execute();

// Verificar se a consulta retornou resultados
if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $hora_inicio = $row['horario_inicio'];
    $hora_fim = $row['horario_fim'];
    $local = $row['local'];
    $instrutor = $row['instrutor'];
} else {
    $hora_inicio = "Não disponível";
    $hora_fim = "Não disponível";
    $local = "Não disponível";
    $instrutor = "Não disponível";
}

// Função para verificar conflitos de horário
function verificarConflitoHorario($conn, $id_aluno, $id_turma) {
    // Obter o horário e dados da nova turma
    $sqlHorarioTurma = "SELECT horario_inicio, horario_fim, data_inicio, data_fim FROM turmas WHERE id = :id_turma";
    $stmtHorarioTurma = $conn->prepare($sqlHorarioTurma);
    $stmtHorarioTurma->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
    $stmtHorarioTurma->execute();
    $horarioTurma = $stmtHorarioTurma->fetch(PDO::FETCH_ASSOC);

    if (!$horarioTurma) {
        throw new Exception("Horário da turma não encontrado.");
    }

    // Verificar conflitos de horário com outras turmas
    $sqlContagemConflitos = "
        SELECT COUNT(*)
        FROM alunos_turmas at
        JOIN turmas t ON at.id_turma = t.id
        WHERE at.id_aluno = :id_aluno
        AND (
            (t.data_inicio <= :data_fim AND t.data_fim >= :data_inicio) 
            AND 
            (t.horario_inicio < :horario_fim AND t.horario_fim > :horario_inicio)
        )";

    $stmtContagemConflitos = $conn->prepare($sqlContagemConflitos);
    $stmtContagemConflitos->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
    $stmtContagemConflitos->bindParam(':data_inicio', $horarioTurma['data_inicio'], PDO::PARAM_STR);
    $stmtContagemConflitos->bindParam(':data_fim', $horarioTurma['data_fim'], PDO::PARAM_STR);
    $stmtContagemConflitos->bindParam(':horario_inicio', $horarioTurma['horario_inicio'], PDO::PARAM_STR);
    $stmtContagemConflitos->bindParam(':horario_fim', $horarioTurma['horario_fim'], PDO::PARAM_STR);
    $stmtContagemConflitos->execute();
    $contagemConflitos = $stmtContagemConflitos->fetchColumn();

    return $contagemConflitos;
}

// Função para obter o nome do aluno pelo id_aluno
function obterNomeAluno($conn, $id_aluno) {
    $stmt = $conn->prepare("SELECT nome FROM alunos WHERE id = :id_aluno");
    $stmt->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchColumn(); // Retorna o nome do aluno
}

// Verifica se a pesquisa foi enviada
$pesquisa = $_GET['pesquisa'] ?? '';

// Consulta para buscar alunos, com ou sem filtro
$query = "SELECT * FROM alunos";
if (!empty($pesquisa)) {
    $query .= " WHERE nome LIKE :pesquisa OR cpf LIKE :pesquisa";
}

$stmt = $conn->prepare($query);

if (!empty($pesquisa)) {
    $stmt->bindValue(':pesquisa', "%$pesquisa%");
}

$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['aluno_turma']) && is_array($_POST['aluno_turma'])) {
        $alunosSelecionados = $_POST['aluno_turma'];

        foreach ($alunosSelecionados as $id_aluno) {
            // Verificar se o aluno já está na turma
            $sqlVerificaTurma = "SELECT COUNT(*) FROM alunos_turmas WHERE id_aluno = :id_aluno AND id_turma = :id_turma";
            $stmtVerificaTurma = $conn->prepare($sqlVerificaTurma);
            $stmtVerificaTurma->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
            $stmtVerificaTurma->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
            $stmtVerificaTurma->execute();
            $jaNaTurma = $stmtVerificaTurma->fetchColumn();

            if ($jaNaTurma > 0) {
                echo "<script>alert('O aluno já está cadastrado nesta turma.'); window.location.href='inserirAlunosAdicionais.php?id=$id_turma';</script>";
                continue;
            }

            // Verificar se há conflito de horários com outras turmas
            $contagemConflitos = verificarConflitoHorario($conn, $id_aluno, $id_turma);

            if ($contagemConflitos > 0) {
                // Obter o nome do aluno a partir do ID
                $nome_aluno = obterNomeAluno($conn, $id_aluno);

                // Exibir alerta com o nome do aluno em vez do ID
                echo "<script>
                        alert('Conflito de horários: $nome_aluno já está cadastrado em outra turma no mesmo período.');
                        window.location.href='inserirAlunosAdicionais.php?id=$id_turma';
                    </script>";
                continue;
            }

            // Inserir aluno na turma
            $nomeAluno = obterNomeAluno($conn, $id_aluno);
            if ($nomeAluno) {
                $sql = "INSERT INTO alunos_turmas (id_aluno, nome_aluno, id_turma) VALUES (:id_aluno, :nome_aluno, :id_turma)";
                $stmt = $conn->prepare($sql);
                $stmt->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
                $stmt->bindParam(':nome_aluno', $nomeAluno, PDO::PARAM_STR);
                $stmt->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
                $stmt->execute();
            }
        }

        echo "<script>alert('Alunos Inseridos com Sucesso.'); window.location.href='inserirAlunosAdicionais.php?id=$id_turma';</script>";
    }
}

// Consultas para exibir alunos disponíveis e já cadastrados na turma
$sqlAlunosDisponiveis = "SELECT a.id, a.nome, a.cpf
                         FROM alunos a
                         WHERE NOT EXISTS (
                             SELECT 1
                             FROM alunos_turmas at
                             WHERE at.id_aluno = a.id
                             AND at.id_turma = :id_turma
                             )";
if (!empty($pesquisa)) {
    $sqlAlunosDisponiveis .= " AND (a.nome LIKE :pesquisa OR a.cpf LIKE :pesquisa)";
}

$stmtAlunosDisponiveis = $conn->prepare($sqlAlunosDisponiveis);
$stmtAlunosDisponiveis->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);

if (!empty($pesquisa)) {
    $stmtAlunosDisponiveis->bindValue(':pesquisa', "%$pesquisa%");
}

$stmtAlunosDisponiveis->execute();
$alunosDisponiveis = $stmtAlunosDisponiveis->fetchAll(PDO::FETCH_ASSOC);

// Buscar alunos já cadastrados na turma
$sqlAlunosSelecionados = "SELECT at.id_aluno, at.nome_aluno, a.cpf
                            FROM alunos_turmas at
                            JOIN alunos a ON at.id_aluno = a.id
                            WHERE at.id_turma = :id_turma
                            ORDER BY at.nome_aluno ASC;
                            ";
$stmtAlunosSelecionados = $conn->prepare($sqlAlunosSelecionados);
$stmtAlunosSelecionados->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
$stmtAlunosSelecionados->execute();
$alunosSelecionados = $stmtAlunosSelecionados->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Inserir Alunos Adicionais</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css">
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/pesquisa.css" />
</head>

<body>
    <div class="container-mensagens" id="container-mensagens"></div>
    <div class="loader-full animated fadeIn hidden">
        <img src="../img/loader.png" width="100" class="animated pulse infinite" />
    </div>
    <section class="bg-menu">
        <div class="conteudo" style="margin-left: -240px;">
            <section class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page fas fa-users"><b>&nbsp; PAINEL - INSERIR ALUNOS</b></h1>
                            <div class="container-right">
                                <div class="container-dados">
                                    <p class="mb-0"><?php echo $local; ?></p>
                                    <?php if ($local) { ?>
                                    <span style="font-size: 14px;"><b>Instrutor: &nbsp;</b><?php echo $instrutor; ?></span>
                                    <?php } ?>
                                </div>
                                    <a href="legislacao.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Voltar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="conteudo-inner" style="margin-top: 20px;">
                <div class="container">
                    <div class="row">
                        <div class="row">
                            <div class="col-md-12 mb-0">
                                <div class="card-search">
                                    <div class="card-body">
                                    <form method="GET" class="row g-3 align-items-center">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_turma); ?>">
                                            <div class="col-md-4">
                                                <div class="container-cep">
                                                <input class="form-control" type="text" id="searchInput" name="pesquisa" placeholder="Pesquisar alunos..." value="<?php echo htmlspecialchars($pesquisa); ?>">
                                                    <button type="submit" id="pesquisar" class="btn btn-search active">
                                                        <i class="fa fa-search"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-12 mt-2 tab-item" id="categoria">
                            <div class="col-12" id="categorias">
                                <form id="formAssociado" action="" method="POST" style="zoom: 120%;">
                                    <input type="hidden" name="id_turma" value="<?php echo htmlspecialchars($id_turma); ?>">
                                    <div class="container-group mb-5">
                                        <div class="col-12 mb-4 card card-form socio">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-5 card-table table-responsive">
                                                        <div class="card-drag" id="headingOne">
                                                            <div class="infos">
                                                                <p class="name-table mb-3">
                                                                    <span class="me-2"><i class="fas fa-users"></i></span>
                                                                    <b>Lista de Alunos</b>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div id="collapseOne">
                                                            <div class="card-body" id="alunoForm">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th><i class="fas fa-check-square selec"></i></th>
                                                                            <th scope="col">Nome</th>
                                                                            <th scope="col">CPF</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                    <?php
                                                                        if (!empty($alunosDisponiveis)) {
                                                                            foreach ($alunosDisponiveis as $row) {
                                                                                echo "<tr>";
                                                                                echo "<td><input name='aluno_turma[]' type='checkbox' value='{$row['id']}' data-nome='{$row['nome']}' data-cpf='{$row['cpf']}'></td>";
                                                                                echo "<td>{$row['nome']}</td>";
                                                                                echo "<td>{$row['cpf']}</td>";
                                                                                echo "</tr>";
                                                                            }
                                                                        } else {
                                                                            echo "<tr><td colspan='3'>Nenhum aluno disponível</td></tr>";
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="col-md-6">
                                                    <div class="card-table table-responsive">
                                                        <div class="card-drag" id="headingOne">
                                                            <div class="infos">
                                                                <p class="name-table mb-3">
                                                                    <span class="me-2"><i class="fas fa-users"></i></span>
                                                                    <b>Alunos Selecionados</b>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div id="collapseOne">
                                                            <div class="card-body">
                                                                <table class="table" id="alunoSelecionado">
                                                                    <thead>
                                                                        <tr>
                                                                            <th scope="col">Nome</th>
                                                                            <th scope="col">CPF</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <?php
                                                                        if (!empty($alunosSelecionados)) {
                                                                            foreach ($alunosSelecionados as $row) {
                                                                                echo "<tr data-id='{$row['id_aluno']}'>";
                                                                                echo "<td>{$row['nome_aluno']}</td>";
                                                                                echo "<td>{$row['cpf']}</td>";
                                                                                echo "</tr>";
                                                                            }
                                                                        } else {
                                                                            echo "<tr
                                                                            ><td colspan='2'></td></tr>";
                                                                        }
                                                                        ?>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-md-12">
                                                    <button id="btnSubmit" type="submit" class="btn btn-yellow btn-sm mt-4 btn-proximo">
                                                        <i class="fas fa-plus"></i> Adicionar Alunos à Turma
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </div>
    </section>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="../js/fontawesome.js"></script>
    <script src="../js/validacao.js"></script>
    <script src="./js/painel.js"></script>

    <script>
        
        $(document).ready(function () {
            // Função para atualizar a lista de alunos selecionados e o localStorage
            function atualizarSelecionados() {
                let idAluno = $(this).val();
                let nomeAluno = $(this).data('nome');
                let cpfAluno = $(this).data('cpf');
                let alunoSelecionado = $('#alunoSelecionado');

                if ($(this).is(':checked')) {
                    // Adiciona o aluno na tabela apenas se ainda não estiver lá
                    if ($('#alunoSelecionado tr[data-id="' + idAluno + '"]').length === 0) {
                        alunoSelecionado.append('<tr data-id="' + idAluno + '"><td>' + nomeAluno + '</td><td>' + cpfAluno + '</td></tr>');
                    }
                } else {
                    // Remove o aluno da tabela se estiver lá
                    $('#alunoSelecionado tr[data-id="' + idAluno + '"]').remove();
                }

                // Atualiza o localStorage com os IDs dos alunos selecionados
                let alunosSelecionados = [];
                $('#alunoSelecionado tr').each(function () {
                    alunosSelecionados.push($(this).data('id'));
                });
                localStorage.setItem('alunosSelecionados', JSON.stringify(alunosSelecionados));
            }

            // Atualiza a lista e o localStorage quando os checkboxes são alterados
            $('input[name="aluno_turma[]"]').change(atualizarSelecionados);

            // Carrega a lista de alunos selecionados do localStorage quando a página é carregada
            let alunosSelecionados = JSON.parse(localStorage.getItem('alunosSelecionados')) || [];
            alunosSelecionados.forEach(function (idAluno) {
                $('input[name="aluno_turma[]"][value="' + idAluno + '"]').prop('checked', true);
            });

            // Atualiza a tabela de selecionados com base nos alunos que estão marcados
            $('input[name="aluno_turma[]"]:checked').each(function () {
                atualizarSelecionados.call(this);
            });

            // Função para atualizar a tabela com base na pesquisa
            $('#pesquisar').on('input', function () {
                let pesquisa = $(this).val().toLowerCase();

                // Filtra a tabela de alunos com base na pesquisa
                $('#tabelaAlunos tbody tr').each(function () {
                    let nomeAluno = $(this).find('td').first().text().toLowerCase();
                    $(this).toggle(nomeAluno.indexOf(pesquisa) > -1);
                });

                // Atualiza a tabela de selecionados com base nos checkboxes visíveis após a pesquisa
                $('input[name="aluno_turma[]"]:checked').each(function () {
                    atualizarSelecionados.call(this);
                });
            });
        });

        function redirecionarSeRecarregar() {
            // Verifica se a página foi recarregada
            if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
                window.location.href = "inserirAlunosAdicionais.php?id=<?php echo $id_turma; ?>";
            }
        }


        window.onload = redirecionarSeRecarregar;
    </script>
</body>
</html>
