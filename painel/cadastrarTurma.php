<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador ou presidente
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é admin ou presidente
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        // Verificar se o nível de usuário é admin ou presidente
        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
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
include 'conexao.php'; // Certifique-se de que está usando PDO

// ID da associação
$id_associacao = 1; // ID da associação (fixo)
$query = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $id_associacao, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);

$logoImage = $associacao['logo_image'] ?? ""; // Verifica se o campo logo_image existe e não é vazio

// Buscar instrutores
$query_instrutores = "SELECT id, nome_instrutor FROM instrutores";
$result_instrutores = $conn->query($query_instrutores);

// Verifique se a consulta para instrutores retornou resultados
if (!$result_instrutores) {
    die('Erro ao buscar instrutores.');
}

// Obtendo o nome e o email do usuário da sessão usando uma consulta SQL
$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // Output dos dados do usuário
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $nome_usuario = $row["nome"];
        $email_usuario = $row["email"];
    }
} else {
    echo "Nenhum resultado encontrado.";
}

// Obtém a data atual
$data_atual = date('Y-m-d');

// Variáveis para pesquisa e filtragem
$search = $_GET['search'] ?? '';
$filtrar = $_GET['filtrar'] ?? 'todos';
$ordem = $_GET['ordem'] ?? '';

// Monta a consulta SQL com JOINs
$sql = "
SELECT DISTINCT alunos.*, servicos_aluno.servico, login_aluno.email, servicos_aluno.data_pagamento
FROM alunos 
LEFT JOIN servicos_aluno ON alunos.nome = servicos_aluno.nome_aluno 
LEFT JOIN login_aluno ON alunos.nome = login_aluno.nome_aluno 
WHERE 1=1
";

// Adiciona a cláusula de pesquisa se houver um termo de pesquisa
if (!empty($search)) {
    $search = "%$search%";
    $sql .= " AND (
        alunos.nome LIKE :search 
        OR alunos.cpf LIKE :search 
        OR alunos.data_nascimento LIKE :search
        OR alunos.rg LIKE :search
        OR alunos.telefone LIKE :search
        OR alunos.renach LIKE :search
        OR alunos.ladv LIKE :search
        OR alunos.vencimento_processo LIKE :search
        OR alunos.rua LIKE :search
        OR alunos.bairro LIKE :search
        OR alunos.numero LIKE :search
        OR alunos.observacao LIKE :search
        OR servicos_aluno.servico LIKE :search
        OR servicos_aluno.data_pagamento LIKE :search
        OR servicos_aluno.categoria LIKE :search
        OR login_aluno.email LIKE :search
    )";
}

// Adiciona a cláusula de filtro se aplicável
if ($filtrar === 'por_nome') {
    $sql .= " ORDER BY alunos.nome";
} elseif ($filtrar === 'por_categoria') {
    $sql .= " AND servicos_aluno.servico IS NOT NULL ORDER BY servicos_aluno.servico";
} elseif ($filtrar === 'por_parcelas') {
    // Filtra alunos com serviços parcelados
    $sql .= " AND EXISTS (
        SELECT 1 
        FROM servicos_aluno 
        WHERE nome_aluno = alunos.nome 
        AND numero_parcelas > 1
    )";
}

// Adiciona a cláusula de ordenação se especificada
if ($ordem === 'alfabetica') {
    $sql .= " ORDER BY alunos.nome";
}

// Prepara a consulta
$stmt = $conn->prepare($sql);

// Bind de parâmetros se houver pesquisa
if (!empty($search)) {
    $stmt->bindParam(':search', $search, PDO::PARAM_STR);
}

// Executa a consulta
$stmt->execute();

// Prepara a lista de IDs de alunos com pagamentos pendentes
$alunos_pendentes = [];
$pendentes_count = 0;

// Verifica se a consulta retornou resultados
if ($stmt->rowCount() > 0) {
    // Itera pelos resultados para identificar alunos com pagamentos pendentes
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $sql_pendentes = "
        SELECT 1 
        FROM servicos_aluno
        WHERE nome_aluno = :nome_aluno
        AND data_pagamento <= :data_atual
        AND data_pagamento IS NOT NULL
        LIMIT 1
        ";

        // Prepara e executa a consulta para verificar pagamentos pendentes
        $stmt_pendentes = $conn->prepare($sql_pendentes);
        $stmt_pendentes->bindParam(':nome_aluno', $row['nome'], PDO::PARAM_STR);
        $stmt_pendentes->bindParam(':data_atual', $data_atual, PDO::PARAM_STR);
        $stmt_pendentes->execute();

        if ($stmt_pendentes->rowCount() > 0) {
            $alunos_pendentes[$row['id']] = $row['nome'];
            $pendentes_count++;
        }
    }
}

// Fecha a conexão com o banco de dados
$conn = null;
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="./img/SIS-UICAM.png" type="image/x-icon">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Legislação</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css">
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
  
</head>

<body>

    <div class="container-mensagens" id="container-mensagens">
    </div>

    <div class="loader-full animated fadeIn hidden">
        <img src="../img/loader.png" width="100" class="animated pulse infinite" />
    </div>

    <section class="bg-menu">

        <div class="menu-left">

            <div class="logo">
            <?php if (!empty($logoImage)): ?>
                <img class="logo-admin" src="uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" width="100" alt="Logo">
            <?php else: ?>
                
            <?php endif; ?>
            </div>

            <div class="menus">
                <a href="./home.php" class="menu-item">
                    <i class="fas fa-home"></i> Início
                </a>

                <a href="./feriadosCadastrados.php" class="menu-item">
                    <i class="fas fa-calendar-alt"></i> Feriados
                </a>

                <a href="./legislacao.php" class="menu-item active">
                    <i class="fas fa-book-open"></i> Legislação
                </a>


                <a href="./alunos.php" class="menu-item">
                    <i class="fas fa-users"></i> Alunos
                </a>

                <a href="./instrutores.php" class="menu-item">
                    <i class="fas fa-chalkboard-teacher"></i> Instrutores/Placa
                </a>

                <a href="./configuracoes.php" class="menu-item">
                    <i class="fas fa-cog"></i> Configurações
                </a>

                <a href="./relatorio.php" class="menu-item">
                    <i class="fas fa-donate"></i> Financeiro
                </a>


                <a href="./empresa.php" class="menu-item">
                    <i class="fas fa-building"></i> Empresa
                </a>

            </div>
        </div>

        <div class="conteudo">
            <section class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page fas fa-book-open"><b>&nbsp; PAINEL - LEGISLAÇÃO</b></h1>
                            <div class="container-right">
                                <div class="container-dados">
                                <p><?php echo $nome_usuario; ?></p>
                                    <?php if ($email_usuario) { ?>
                                    <span><?php echo $email_usuario; ?></span>
                                    <?php } ?>
                                </div>

                                <a href="logout.php" class="btn btn-white btn-sm" onclick="return confirm('Tem certeza que deseja sair?')">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="conteudo-inner">
                <div class="container">
                    <div class="row">
                        <div class="col-12 mt-0">
                            <div class="menus-config mb-2">
                                <a href="./legislacao.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-users"></i> Turmas Cadastradas
                                </a>
                                <a href="./cadastrarTurma.php" class="btn btn-white btn-sm active">
                                    <i class="fas fa-plus-circle"></i> Cadastrar Turma
                                </a>
                            
                            </div>
                        </div>


                        <div class="col-12 mt-5 tab-item" id="categoria">
                            <div class="col-12" id="categorias">
                            <form id="formAssociado" action="processarTurma.php" method="POST" style="zoom: 95%;">
                                <div class="container-group mb-5">
                                    <!-- Parte 1 -->
                                    <div class="col-12 mb-4 card card-form socio">
                                        <div class="row">
                                            <div class="col-4 mb-2">
                                                <div class="form-group container-cep">
                                                    <p class="title-categoria mb-2"><b>Local:</b></p>
                                                    <input type="text" name="local" class="form-control mb-2" oninput="this.value = this.value.toUpperCase()" />
                                                </div>
                                            </div>

                                            <div class="col-4 mb-2">
                                            </div>

                                            <div class="col-4 mb-2">
                                            </div>

                                            <div class="col-4 mb-2">
                                                <div class="form-group container-cep mb-4">
                                                    <p class="title-categoria mb-2"><b>Instrutor:</b></p>
                                                    <select name="instrutor" class="form-control">
                                                        <option value="">Selecione o instrutor</option>
                                                        <?php while ($instrutor = $result_instrutores->fetch(PDO::FETCH_ASSOC)): ?>
                                                            <option value="<?= htmlspecialchars($instrutor['nome_instrutor']) ?>">
                                                                <?= htmlspecialchars($instrutor['nome_instrutor']) ?>
                                                            </option>
                                                        <?php endwhile; ?>
                                                    </select>
                                                </div>
                                            </div>


                                            <div class="col-4 mb-2">
                                                <div class="form-group container-cep">
                                                    <p class="title-categoria mb-2"><b>Data Inicial:</b></p>
                                                    <input type="date" name="data_inicio" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="col-4 mb-3">
                                                <div class="form-group container-cep">
                                                    <p class="title-categoria mb-1"><b>Data Final:</b></p>
                                                    <input type="date" name="data_fim" class="form-control" />
                                                </div>
                                            </div>

                                            <div class="col-4 mb-3">
                                                <div class="form-group container-cep">
                                                    <p class="title-categoria mb-1"><b>Horário Inicial:</b></p>
                                                    <input type="time" name="horario_inicio" class="form-control" oninput="this.value = this.value.toUpperCase()" />
                                                </div>
                                            </div>

                                            <div class="col-4 mb-3">
                                                <div class="form-group container-cep">
                                                    <p class="title-categoria mb-1"><b>Horário Final:</b></p>
                                                    <input type="time" name="horario_fim" class="form-control" oninput="this.value = this.value.toUpperCase()" />
                                                </div>
                                            </div>
                                            <div class="col-4 mb-3">
                                                <div class="form-group container-cep mb-4">
                                                    <p class="title-categoria mb-1"><b>Turno:</b></p>
                                                    <select class="form-control" id="turno" name="turno">
                                                        <option value="">Selecione o turno</option>
                                                        <option value="Matutino">Matutino</option>
                                                        <option value="Vespertino">Vespertino</option>
                                                        <option value="Noturno">Noturno</option>
                                                    </select>

                                                </div>
                                            </div>
                                                <div class="col-12 text-right">
                                                <button type="submit" class="btn btn-yellow next btn-sm btn-proximo mt-4" style="float:right;">
                                                    Finalizar &nbsp;<i class="fas fa-check"></i>
                                                </button>
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

    <script src="../js/jquery-3.6.0.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/jquery.mask.min.js"></script>
    <script src="../js/sweetalert2.all.min.js"></script>
    <script src="./js/visualizarSocios.js"></script>
    <script src="./js/ajax.js"></script>

</body>

</html>
