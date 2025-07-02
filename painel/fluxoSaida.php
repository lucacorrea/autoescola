<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como presidente
function verificarAcesso() {
    if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é presidente
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        // Verificar se o nível de usuário é presidente
        if($nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            // O usuário é presidente, então ele tem permissão para acessar esta parte do sistema
            return true;
        } elseif($nivel_usuario == 'admin' ) {
            // Se o usuário é administrador, mas não presidente, ele não tem permissão
            // Redirecionar para outra página ou exibir uma mensagem de erro
            header("Location: paginaProtegida.php");
            exit(); // Encerra o script após o redirecionamento
        }
        
        
    }
    
    // Se não estiver logado como presidente, redirecione-o para a página de login
    header("Location: loader.php");
    exit(); // Encerra o script após o redirecionamento
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

include "conexao.php";

// ID da associação
$id = 1; 

// Consulta para pegar a logo_image
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// Verifica se encontrou algum dado
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o campo 'logo_image' está definido e não é vazio
$logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";

include "conexao.php"; // Incluindo a conexão PDO

// Obtendo o nome e o email do usuário da sessão usando uma consulta SQL com PDO
$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    // Output dos dados do usuário
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $nome_usuario = $usuario["nome"];
    $email_usuario = $usuario["email"];
} else {
    echo "Nenhum resultado encontrado.";
}

// Inicializa variáveis para armazenar dados do pagamento
$totalPago = 0;

// Verifica se o formulário foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Verifica se o valor do pagamento foi enviado
    if (isset($_POST['valor']) && !empty($_POST['valor'])) {
        $valorPagamento = floatval(str_replace(',', '.', $_POST['valor'])); // Converte o valor para float
    } else {
        echo "<script>alert('Erro: O campo valor não foi preenchido.'); window.location.href='fluxoSaida.php';</script>";
        exit;
    }

    $descricao = $_POST['descricao'];
    $data = $_POST['data'];

    // Consulta para obter o saldo total disponível nos relatórios
    $sqlSaldoTotal = "SELECT SUM(valor_saida) as saldo_total FROM relatorios WHERE valor_saida > 0";
    $stmtSaldoTotal = $conn->prepare($sqlSaldoTotal);
    $stmtSaldoTotal->execute();
    $saldoTotal = 0;

    if ($stmtSaldoTotal->rowCount() > 0) {
        $rowSaldoTotal = $stmtSaldoTotal->fetch(PDO::FETCH_ASSOC);
        $saldoTotal = $rowSaldoTotal['saldo_total'];
    }

    // Verifica se o saldo total é suficiente para cobrir o valor do pagamento
    if ($saldoTotal < $valorPagamento) {
        echo "<script>alert('Erro: O valor do pagamento é maior do que o saldo total disponível.'); window.location.href='fluxoSaida.php';</script>";
        exit;
    }

    // Insere o valor total do pagamento na tabela_saida
    $stmt = $conn->prepare("INSERT INTO tabela_saida (descricao, valor_saida, data_saida) VALUES (?, ?, ?)");
    $stmt->bindParam(1, $descricao, PDO::PARAM_STR);
    $stmt->bindParam(2, $valorPagamento, PDO::PARAM_STR);
    $stmt->bindParam(3, $data, PDO::PARAM_STR);

    if ($stmt->execute()) {
        // Consulta para obter todos os relatórios com saldo positivo
        $sqlTotal = "SELECT id, valor_saida FROM relatorios WHERE valor_saida > 0 ORDER BY id ASC";
        $stmtTotal = $conn->prepare($sqlTotal);
        $stmtTotal->execute();

        // Verifica se há relatórios disponíveis
        if ($stmtTotal->rowCount() > 0) {
            while ($rowTotal = $stmtTotal->fetch(PDO::FETCH_ASSOC)) {
                $idRelatorio = $rowTotal['id'];
                $totalAtual = $rowTotal['valor_saida'];

                // Se o valor atual do relatório cobre o pagamento restante
                if ($totalAtual >= $valorPagamento) {
                    // Atualiza o valor do relatório
                    $stmtUpdate = $conn->prepare("UPDATE relatorios SET valor_saida = valor_saida - ? WHERE id = ?");
                    $stmtUpdate->bindParam(1, $valorPagamento, PDO::PARAM_STR);
                    $stmtUpdate->bindParam(2, $idRelatorio, PDO::PARAM_INT);
                    $stmtUpdate->execute();

                    $totalPago += $valorPagamento;
                    $valorPagamento = 0; // O pagamento foi completado
                    break; // Saímos do loop porque o pagamento foi completado
                } else {
                    // Caso o saldo do relatório não seja suficiente, subtraímos o que temos
                    $stmtUpdate = $conn->prepare("UPDATE relatorios SET valor_saida = 0 WHERE id = ?");
                    $stmtUpdate->bindParam(1, $idRelatorio, PDO::PARAM_INT);
                    $stmtUpdate->execute();

                    // Subtrai o valor retirado deste relatório do valor restante a ser pago
                    $totalPago += $totalAtual;
                    $valorPagamento -= $totalAtual; // Atualiza o valor restante do pagamento
                }

                // Verifica se o pagamento foi completamente realizado
                if ($valorPagamento <= 0) {
                    break;
                }
            }

            // Mensagem final com o total pago
            echo "<script>alert('Pagamento total de R$ $totalPago efetuado com sucesso!'); window.location.href='fluxoSaida.php';</script>";
        } else {
            echo "<script>alert('Erro: Não há relatórios disponíveis.'); window.location.href='fluxoSaida.php';</script>";
        }
    } else {
        echo "Erro ao inserir os dados: " . $stmt->errorInfo();
    }
}

// Consulta para somar os valores de valor_saida
$sql = "SELECT SUM(valor_saida) as total FROM relatorios";
$stmt = $conn->prepare($sql);
$stmt->execute();
$total = 0;

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $total = $row['total'];
}

$sqlSaidas = "SELECT descricao, valor_saida, data_saida FROM tabela_saida ORDER BY data_saida DESC";

// Preparando a consulta
$stmt = $conn->prepare($sqlSaidas);

// Executando a consulta
$stmt->execute();


// Fecha a conexão
$conn = null;
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Saída de caixa</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/fluxoSaida.css">

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
    <a href="./alunos.php" class="menu-item ">
        <i class="fas fa-users"></i> Alunos
    </a>

    <a href="./instrutores.php" class="menu-item">
        <i class="fas fa-chalkboard-teacher"></i> Instrutores/Placas
    </a>

    <a href="./configuracoes.php" class="menu-item">
        <i class="fas fa-cog"></i> Configurações
    </a>

    <a href="./relatorio.php" class="menu-item active">
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
                        <i class="fas fa-receipt"></i>&nbsp; PAINEL - SAÍDA DE CAIXA
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


<main class="main-container">
    <div class="container mt-5">
        <div class="col-12">

            <div class="menus-config mb-5">
                <a href="relatorio.php" class="btn btn-white btn-sm">
                    <i class="fas fa-dollar-sign"></i> Faturamento
                </a>
                <a href="fluxoSaida.php" class="btn btn-white btn-sm active">
                    <i class="fas fa-receipt"></i> Saída de caixa
                </a>
            </div>

            </div>
        <div class="row">
            <div class="col-md-4">
                <form id="saidaForm" action="" method="POST">
                    <div class="form-group mb-2">
                        <label for="descricao">Descrição:</label>
                        <input type="text" name="descricao" class="form-control" id="descricao" required>
                    </div>
                    <div class="form-group mb-2">
                        <label for="valor">Valor:</label>
                        <input type="text" name="valor" class="form-control" id="valor" required>
                    </div>
                    <div class="form-group">
                        <label for="data">Data:</label>
                        <input type="date" name="data" class="form-control" id="data" required> <br>
                    </div>
                        
                    <button type="submit" class="btn btn-yellow btn-sm mt-4 btn-proximo" style="float:left; zoom: 85%;">
                        <i class="fas fa-check"></i> &nbsp; Registrar Saída
                    </button>
                </form>
            </div>

            <div class="col-md-8 tabela">
                <h4>Resumo de Saídas</h4>
                <table class="table table-responsive">
                    <thead>
                        <tr>
                            <th>Descrição</th>
                            <th>Valor</th>
                            <th>Data</th>
                        </tr>
                    </thead>
                    <tbody id="saidaTableBody">
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                                <td>R$ <?php echo number_format($row['valor_saida'], 2, ',', '.'); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['data_saida'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
            </div>
            <div class="text-right" style="margin-top: -20px;">
                <div class="total-box col-8" style="float: right;">
                    Total em caixa: <span id="totalSaidas">R$ <?php echo number_format($total, 2, ',', '.'); ?></span>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Custom JS -->
<script src="../js/jquery.min.js"></script>
<script src="../js/jquery-ui.js"></script>
<script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
<script src="../js/main.js"></script>
</body>
</html>
