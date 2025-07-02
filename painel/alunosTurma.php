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

    // Pegar o ID da associação e da turma
    $id_associacao = 1; // Defina o ID da associação como apropriado
    $id_turma = isset($_GET['id']) ? $_GET['id'] : null;

    if (!$id_turma) {
        echo "ID da turma não foi fornecido.";
        exit;
    }

    // Consulta para pegar o nome da associação
    $sql_associacao = "SELECT nome_associacao, logo_image FROM associacoes WHERE id = :id_associacao";
    $stmt_associacao = $conn->prepare($sql_associacao);
    $stmt_associacao->bindParam(':id_associacao', $id_associacao, PDO::PARAM_INT);
    $stmt_associacao->execute();
    $associacao = $stmt_associacao->fetch(PDO::FETCH_ASSOC);

    $nome_associacao = $associacao['nome_associacao'] ?? ''; // Nome padrão se não for encontrado
    $logoImage = $associacao['logo_image'] ?? '';

    // Consulta SQL para buscar os alunos da turma em ordem alfabética
    $stmt = $conn->prepare('
        SELECT alunos_turmas.nome_aluno, alunos_turmas.id_aluno, alunos.cpf, alunos.renach,
        turmas.local, turmas.instrutor, turmas.data_inicio, turmas.data_fim, turmas.horario_inicio, turmas.horario_fim, turmas.turno 
        FROM alunos_turmas 
        JOIN turmas ON alunos_turmas.id_turma = turmas.id 
        JOIN alunos ON alunos_turmas.id_aluno = alunos.id
        WHERE turmas.id = :id_turma
        ORDER BY alunos_turmas.nome_aluno ASC
    ');
    $stmt->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
    $stmt->execute();
    $alunos = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($alunos)) {
        echo "Nenhum aluno encontrado para esta turma.";
        exit;
    }

    // Formatar datas e horários
    $data_inicio = isset($alunos[0]['data_inicio']) ? DateTime::createFromFormat('Y-m-d', $alunos[0]['data_inicio']) : false;
    $data_fim = isset($alunos[0]['data_fim']) ? DateTime::createFromFormat('Y-m-d', $alunos[0]['data_fim']) : false;

    $data_inicio = $data_inicio ? $data_inicio->format('d/m/Y') : 'Data Inválida';
    $data_fim = $data_fim ? $data_fim->format('d/m/Y') : 'Data Inválida';

    $horario_inicio = isset($alunos[0]['horario_inicio']) ? substr($alunos[0]['horario_inicio'], 0, 5) : '00:00';
    $horario_fim = isset($alunos[0]['horario_fim']) ? substr($alunos[0]['horario_fim'], 0, 5) : '00:00';
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <title>Alunos da Turma</title>

        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css" />
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/painel.css" />
        <link rel="stylesheet" href="./css/alunosTurma.css">
    </head>
    <style>
    	@media print {
	    /* Para todas as .image-title */
	    .image-title {
	        margin-top: 40px !important; /* Mantém a margem normal nas primeiras páginas */
	    }
	
	    /* A partir da terceira ocorrência, adiciona margin-top */
	    .image-title:nth-of-type(n+3) {
	        margin-top: 80px !important; /* Ajuste o valor conforme necessário */
	    }
	}

    </style>
    <body>

        <section class="bg-menu">

            <div class="conteudo" style="margin-left: -240px;">

                <div class="menu-top">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center mt-4">
                                <h1 class="title-page">
                                    <b>
                                        <a href="#" id="btnImprimir" onclick="imprimirPagina()" class="btn btn-white btn-sm active">
                                            <i class="fas fa-save"></i>&nbsp; Imprimir Lista
                                        </a>
                                    </b>
                                </h1>
                                <div class="container-right">
                                    <div class="container-dados"></div>
                                    <a id="btnSair" href="legislacao.php" class="btn btn-white btn-sm">
                                        <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <main class="main-container">
                    
                    <div class="main-title">

                        <div class="container mt-0 tabela">

                            <?php
                            // Configuração do fuso horário
                            date_default_timezone_set('America/Sao_Paulo');
                            include "conexao.php";

                            // Buscar os feriados cadastrados no banco de dados
                            $sql_feriados = "SELECT data FROM feriados";
                            $stmt_feriados = $conn->prepare($sql_feriados);
                            $stmt_feriados->execute();
                            $feriados = $stmt_feriados->fetchAll(PDO::FETCH_COLUMN, 0);

                            // Formatar as datas dos feriados para o formato Y-m-d
                            $feriados = array_map(fn($date) => (new DateTime($date))->format('Y-m-d'), $feriados);

                            // Inicializa as datas com base no primeiro aluno
                            $data_inicio = new DateTime($alunos[0]['data_inicio']);
                            $data_fim = new DateTime($alunos[0]['data_fim']);
                            $datas_aulas = [];

                            // Gerar as 15 datas, excluindo finais de semana e feriados
                            while (count($datas_aulas) < 15 && $data_inicio <= $data_fim) {
                                $dia_semana = $data_inicio->format('N'); // 6 = sábado, 7 = domingo
                                $data_formatada = $data_inicio->format('Y-m-d');

                                // Verifica se o dia é útil (não é sábado ou domingo) e não é feriado
                                if ($dia_semana < 6 && !in_array($data_formatada, $feriados)) {
                                    $datas_aulas[] = $data_inicio->format('d/m/Y');
                                }

                                // Avança para o próximo dia
                                $data_inicio->modify('+1 day');
                            }

                            // Divide as datas em pares e cria tabelas separadas
                            $quantidade_datas = count($datas_aulas);
                            $i = 0;

                            while ($i < $quantidade_datas) : 
                        ?>

                        <!-- Cabeçalho e informações do curso - Repetido em cada tabela -->
                        <div class="image-title">
                            <?php if (!empty($logoImage)): ?>
                                <img src="uploads/<?= htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" alt="Logo">
                            <?php endif; ?>
                            <div>
                                <h3 class="mb-2"><?= htmlspecialchars($nome_associacao) ?></h3>
                                <h5>LISTA DE FREQUÊNCIA DO CURSO DE LEGISLAÇÃO</h5>
                            </div>
                        </div>

                        <table class="table table-bordered border" style="margin-top: -22px;">
                            <tr>
                                <td><b>Curso:</b> Legislação de Trânsito</td>
                                <td><b>Período:</b> <?= (new DateTime($alunos[0]['data_inicio']))->format('d/m/Y') ?> a <?= (new DateTime($alunos[0]['data_fim']))->format('d/m/Y') ?></td>
                                <td><b>Horário:</b> <?= $horario_inicio ?> às <?= $horario_fim ?></td>
                                <td colspan="2"><b>Turno:</b> <?= $alunos[0]['turno'] ?? '' ?></td>
                            </tr>
                        </table>

                        <table class="table table-bordered" style="margin-top: -17px;">
                            <thead>
                                <tr>
                                    <th colspan="2">NOME</th>
                                    <th class="text-center">CPF</th>
                                    <th class="text-center">RENACH</th>
                                    <th class="text-center assinatura" style="width: 350px;">DATA: <?= $datas_aulas[$i] ?? '' ?></th>
                                    <th class="text-center assinatura" style="width: 350px;">DATA: <?= $datas_aulas[$i + 1] ?? '' ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($alunos as $index => $aluno) : ?>
                                    <tr>
                                        <td class="text-center"> <?= $index + 1 ?> </td>
                                        <td class="nome-aluno"> <?= htmlspecialchars($aluno['nome_aluno']) ?> </td>
                                        <td class="text-center" style="width: 200px;"> <?= htmlspecialchars($aluno['cpf']) ?> </td>
                                        <td class="text-center" style="width: 150px;"> <?= htmlspecialchars($aluno['renach']) ?> </td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td colspan="2" style="height: 100px !important;">Obs:</td>
                                    <td colspan="2">Instrutor:</td>
                                    <td colspan="2">Diretor de Ensino:</td>
                                </tr>
                            </tbody>
                        </table>

                        <!-- Espaço para separar as tabelas e evitar que o conteúdo fique muito colado -->
                        <div style="page-break-before: always; margin-top: 60px;"></div>

                        <?php
                            // Avança 2 posições no array para a próxima tabela
                            $i += 2;
                            endwhile;
                        ?>
                    </div>

                </main>

            </div>

        </section>

        <!-- Custom JS -->
        <script>
            function imprimirPagina() {
                document.getElementById('btnImprimir').style.display = 'none';
                document.getElementById('btnSair').style.display = 'none';
                setTimeout(function() {
                    window.print();
                }, 100);
            }

            window.onafterprint = function() {
                document.getElementById('btnImprimir').style.display = 'inline-block';
                document.getElementById('btnSair').style.display = 'inline-block';
            };
        </script>

        <script src="../js/jquery.min.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
        <script src="./js/script.js"></script>
        <script src="./js/logout.js"></script>

    </body>
</html>
