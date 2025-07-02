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



include 'conexao.php'; // Inclui o arquivo de conexão com o banco de dados

try {
    // Obtém o ID do usuário da sessão
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    if (!$id_usuario) {
        throw new Exception("ID do usuário não encontrado na sessão.");
    }

    // Consulta para obter nome e email do usuário
    $queryUsuario = "SELECT nome, email FROM usuarios WHERE id = :id";
    $stmtUsuario = $conn->prepare($queryUsuario);
    $stmtUsuario->bindParam(':id', $id_usuario, PDO::PARAM_INT);
    $stmtUsuario->execute();

    $usuario = $stmtUsuario->fetch(PDO::FETCH_ASSOC);
    if (!$usuario) {
        throw new Exception("Usuário não encontrado.");
    }

    $nome_usuario = $usuario['nome'];
    $email_usuario = $usuario['email'];

    // ID da associação (fixo)
    $id_associacao = 1;

    // Consulta para obter o logo da associação
    $queryAssociacao = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmtAssociacao = $conn->prepare($queryAssociacao);
    $stmtAssociacao->bindParam(':id', $id_associacao, PDO::PARAM_INT);
    $stmtAssociacao->execute();

    $associacao = $stmtAssociacao->fetch(PDO::FETCH_ASSOC);
    $logoImage = $associacao['logo_image'] ?? "";

} catch (Exception $e) {
    // Trate os erros e exiba mensagens amigáveis
    echo "Erro: " . $e->getMessage();
    exit;
}

// Agora você pode usar $nome_usuario, $email_usuario e $logoImage no seu código



// Inicializa a variável de pesquisa
$search = isset($_GET['search']) ? $_GET['search'] : '';

try {
    // Monta a consulta SQL
    $query = "SELECT * FROM instrutores";
    if ($search) {
        $query .= " WHERE nome_instrutor LIKE :search OR placa_instrutor LIKE :search";
    }

    // Prepara a consulta
    $stmt = $conn->prepare($query);

    // Vincula o parâmetro de pesquisa, se necessário
    if ($search) {
        $search_param = '%' . $search . '%';
        $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
    }

    // Executa a consulta
    $stmt->execute();

    // Obtém os resultados
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

   
} catch (PDOException $e) {
    echo "Erro ao executar a consulta: " . $e->getMessage();
}

require "conexao.php";

try {

    // Consulta SQL para buscar categorias com a imagem
    $sql = "SELECT id_categoria, nome, preco, parcelado, status, imagem FROM categorias";
    $stmt = $conn->query($sql);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados ou executar consulta: " . $e->getMessage());
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Instrutores/Placa</title>
    <!-- Seus estilos CSS -->
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/dashboard.css">
    <link rel="stylesheet" href="./css/modal.css">
    <link rel="stylesheet" href="./css/pesquisa.css">
</head>

<body>

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

    <a href="./legislacao.php" class="menu-item">
        <i class="fas fa-book-open"></i> Legislação
    </a>
    
    <a href="./alunos.php" class="menu-item">
        <i class="fas fa-users"></i> Alunos
    </a>

    
    <a href="./instrutores.php" class="menu-item active">
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

<div class="menu-top">
    <div class="container">
        <div class="row">
            <div class="col-12 d-flex align-items-center mt-4">

                <h1 class="title-page">
                    <b>
                        <i class="fas fa-chalkboard-teacher"></i>&nbsp; PAINEL - INSTRUTOR
                    </b>
                </h1>

                <div class="container-right">
                    <div class="container-dados">
                    <p><?php echo $nome_usuario; ?></p>
                    <?php if ($email_usuario) { ?>
                    <span><?php echo $email_usuario; ?></span>
                    <?php } ?>
                    </div>
                    <a href="logout.php" class="btn btn-white btn-sm">
                        <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                    </a>
                </div>

            </div>
        </div>
    </div>
</div>


    <!-- Main -->
  <!-- Main -->
  <main class="main-container">
        <div class="main-title">
            <h2></h2>
        </div>
        <div class="conteudo-inner">
            <div class="container">
                <div class="row">
                    <div class="col-12 mt-5 cadastro">
                        <div class="menus-config mb-5 mt-2">
                            <a href="instrutores.php" class="btn btn-white btn-sm active">
                                <i class="fas fa-chalkboard-teacher"></i> Instrutores Cadastrados
                            </a>
                            <a href="cadastrarInstrutorPlaca.php" class="btn btn-white btn-sm">
                                <i class="fas fa-plus"></i> Cadastrar Instru/Placa
                            </a>
                        </div>

                        <div class="col-12" id="categorias">
                            <div class="container-group mb-5">
                                <div class="accordion" id="categoriasMenu">
                                    <div class="row">
                                        <div class="col-md-12 mb-2">
                                            <div class="card-search">
                                                <div class="card-body">
                                                    <form method="GET" class="row g-3 align-items-center" action="instrutores.php">
                                                        <div class="col-md-5">
                                                            <div class="container-cep">
                                                                <input type="text" class="form-control" name="search" value="<?php echo htmlentities($search); ?>" placeholder="Pesquisar Instrutor" />
                                                                <div class="search">
                                                                    <button type="submit" class="btn btn-search active">
                                                                        <i class="fa fa-search"></i>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mt-3 card-table">
                                        <div class="card-drag" id="headingOne">
                                            <div class="infos">
                                                <a href="#" class="name-table mb-0" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                    <span class="me-2"><i class="fas fa-chalkboard-teacher"></i></span>
                                                    <b>Lista de Instrutores</b>
                                                </a>
                                            </div>
                                        </div>

                                        <div id="collapseOne" class="collapse show" data-parent="#categoriasMenu">
                                            <div class="lista-produtos" id="listaProdutos-one">
                                                <table class="table mt-3 data-table">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col" style="width: 700px;">Nome do Instrutor</th>
                                                            <th scope="col">Placa do Instrutor</th>
                                                            <th class="col">Ações</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php if (isset($mensagem)) { ?>
                                                            <tr>
                                                                <td colspan="3"><?php echo $mensagem; ?></td>
                                                            </tr>
                                                        <?php } else { ?>
                                                            <?php foreach ($result as $row): ?>
                                                                <tr>
                                                                    <td><?php echo htmlentities($row['nome_instrutor']); ?></td>
                                                                    <td><?php echo htmlentities($row['placa_instrutor']); ?></td>
                                                                    <td>
                                                                        <div class="actions">
                                                                            <a href="editarInstrutor.php?id=<?php echo $row['id']; ?>" class="icon-action">
                                                                                <i class="fas fa-pencil-alt"></i>
                                                                            </a>

                                                                            <a href="#" class="icon-action" onclick="openModal(<?php echo $row['id']; ?>)">
                                                                                <i class="fas fa-trash-alt"></i>
                                                                            </a>
                                                                        </div>
                                                                    </td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
    <!-- End Main -->

    <!-- Modal -->
    <div id="confirmDeleteInstrutorModal" class="modal-horarios" style="display: none;">
        <div class="modal-horarios-content">
            <div class="modal-horarios-header">
                <span class="close" onclick="closeModal()">&times;</span>
                <h2 class="title-modal"><i class="fas fa-trash-alt"></i> Excluir Instrutor</h2>
            </div>
            <p class="p mt-5">Tem certeza que deseja excluir este Instrutor?</p>
            <button class="excluir excluir-secondary" onclick="closeModal()">
                <span class="text">Não</span>
            </button>
            <a id="confirmDelete" href="#" class="excluir">Sim</a>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Custom JS -->
    <script src="../js/jquery.min.js"></script>
    <script src="../js/jquery-ui.js"></script>
    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
    <script src="./js/script.js"></script>
    <script src="./js/logout.js"></script>
    <script src="./js/modal.js"></script>
    <script>
        function openModal(id) {
            document.getElementById('confirmDelete').href = 'excluirInstrutor.php?id=' + id;
            document.getElementById('confirmDeleteInstrutorModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('confirmDeleteInstrutorModal').style.display = 'none';
        }

        function redirecionarSeRecarregar() {
    // Verifica se a página foi recarregada
    if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
        window.location.href = "instrutores.php";
    }
}

window.onload = redirecionarSeRecarregar;
    </script>
</body>
</html>
