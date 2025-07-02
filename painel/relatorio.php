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

//------------------SESSION IMAGE EMPRESA-----------
include 'conexao.php'; // Inclui a conexão correta

$id = 1;

$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

$associacao = $stmt->fetch(PDO::FETCH_ASSOC);
$logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";

//------------------END SESSION IMAGE EMPRESA-------


//------------------SESSION USER--------------------
$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    $nome_usuario = $usuario["nome"];
    $email_usuario = $usuario["email"];
} else {
    echo "Nenhum resultado encontrado.";
}

//------------------END SESSION USER----------------


// Recuperar filtros do formulário
$data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';
$categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';

// Construir a query com base nos filtros
$query = "SELECT * FROM relatorios WHERE 1=1";
$params = [];

if ($data_inicio) {
    $query .= " AND data_preco >= :data_inicio";
    $params[':data_inicio'] = $data_inicio;
}
if ($data_fim) {
    $query .= " AND data_preco <= :data_fim";
    $params[':data_fim'] = $data_fim;
}
if ($categoria) {
    $query .= " AND categoria_preco = :categoria";
    $params[':categoria'] = $categoria;
}

$stmt = $conn->prepare($query);

// Executar a query com os parâmetros bindados
$stmt->execute($params);

$servicos = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calcular os totais
$total_valor = 0;
$total_pagos = 0;
$total_pendentes = 0;

foreach ($servicos as $servico) {
    $total_valor += (float)$servico['preco'];  // Garantir que 'preco' seja tratado como float
    if ($servico['preco'] != 'SIM') {
        $total_pagos++;
    } else {
        $total_pendentes++;
    }
}

// Recuperar dados para o gráfico
$sql = "SELECT 
            MONTH(r.data_preco) AS mes,
            YEAR(r.data_preco) AS ano,
            COUNT(r.id) AS quantidade_pedidos,
            SUM(r.preco) AS preco_total,
            r.categoria_preco
        FROM relatorios r
        WHERE 1=1";

// Adicionando condições de filtro dinamicamente
$params = [];
if ($data_inicio) {
    $sql .= " AND r.data_preco >= :data_inicio";
    $params[':data_inicio'] = $data_inicio;
}
if ($data_fim) {
    $sql .= " AND r.data_preco <= :data_fim";
    $params[':data_fim'] = $data_fim;
}
if ($categoria) {
    $sql .= " AND r.categoria_preco = :categoria";
    $params[':categoria'] = $categoria;
}

$sql .= " GROUP BY YEAR(r.data_preco), MONTH(r.data_preco), r.categoria_preco
          ORDER BY ano, mes";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Inicialização das variáveis para o gráfico
$meses = array_fill(0, 12, 0); // Inicializa com 0 para cada mês
$nomesMeses = ["Jan", "Fev", "Mar", "Abr", "Mai", "Jun", "Jul", "Ago", "Set", "Out", "Nov", "Dez"];
$valores = array_fill(0, 12, 0);
$pedidos = array_fill(0, 12, ['quantidade' => 0, 'valor' => 0]);

// Processamento dos dados retornados
foreach ($dados as $linha) {
    $mesIndex = (int)$linha['mes'] - 1;
    if ($mesIndex >= 0 && $mesIndex < 12) {
        $valores[$mesIndex] += $linha['preco_total'];
        $pedidos[$mesIndex]['quantidade'] += $linha['quantidade_pedidos'];
        $pedidos[$mesIndex]['valor'] += $linha['preco_total'];
    }
}

// Conversão para JSON para uso no frontend
$mesesJson = json_encode($nomesMeses);
$valoresJson = json_encode($valores);
$pedidosJson = json_encode($pedidos);

?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Financeiro</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
                                    <i class="fas fa-donate"></i>&nbsp; PAINEL - RELATÓRIO FINANCEIRO
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

            <div class="conteudo-inner">
                <div class="container">
                    <div class="row">

                        <div class="col-12">

                            <div class="menus-config">
                                <a href="relatorio.php" class="btn btn-white btn-sm active">
                                    <i class="fas fa-dollar-sign"></i> Faturamento
                                </a>
                                <a href="fluxoSaida.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-receipt"></i> Saída de caixa
                                </a>
                            </div>

                        </div>

                        <div class="col-12 mt-5" id="faturamento">

                            <p class="title-categoria mb-4">
                                <b>Acompanhe seus faturamentos por período.</b>
                            </p>

                            <form method="GET" action="">
                                <div class="row">
                                    <div class="col-3">
                                        <div class="form-group">
                                            <p class="title-categoria mb-0"><b>Data início:</b></p>
                                            <input type="date" name="data_inicio" class="form-control" value="<?php echo htmlspecialchars($data_inicio); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <p class="title-categoria mb-0"><b>Data fim:</b></p>
                                            <input type="date" name="data_fim" class="form-control" value="<?php echo htmlspecialchars($data_fim); ?>" />
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <div class="form-group">
                                            <p class="title-categoria mb-0"><b>Categoria:</b></p>
                                            <select name="categoria" class="form-control" onchange="this.form.submit()">
                                                <option value="">Todas</option>
                                                <option value="A" <?php if ($categoria == 'A') echo 'selected'; ?>>A</option>
                                                <option value="B" <?php if ($categoria == 'B') echo 'selected'; ?>>B</option>
                                                <option value="AB" <?php if ($categoria == 'AB') echo 'selected'; ?>>AB</option>
                                                <option value="A/AB" <?php if ($categoria == 'A/AB') echo 'selected'; ?>>A/AB</option>
                                                <option value="B/AB" <?php if ($categoria == 'B/AB') echo 'selected'; ?>>B/AB</option>
                                                <option value="D" <?php if ($categoria == 'D') echo 'selected'; ?>>D</option>
                                                <option value="A/D" <?php if ($categoria == 'A/D') echo 'selected'; ?>>A/D</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-3">
                                        <button type="submit" class="btn btn-yellow btn-sm mt-4">
                                            <i class="fas fa-search"></i>&nbsp; Filtrar Dados
                                        </button>
                                    </div>
                                </div>
                            </form>



                            <div class="row mt-5">

                            <div class="col-3">
                            <div class="card card-address cursor-default mb-3">
                                <div class="img-icon-details">
                                    <i class="fas fa-dollar-sign"></i>
                                </div>
                                <div class="infos">
                                    <p class="text mb-0"><b>Total:</b></p>
                                    <p class="value-card mb-0" style="font-size: 18px;"><b>R$ <?php echo number_format($total_valor, 2, ',', '.'); ?></b></p>
                                </div>
                            </div>
                        
                            <div class="card card-address cursor-default mb-3">
                                <div class="img-icon-details">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="infos">
                                    <p class="text mb-0"><b>Pagos:</b></p>
                                    <p class="value-card mb-0"><b><?php echo $total_pagos; ?></b></p>
                                </div>
                            </div>
                            </div>
                                <div class="col-9">
                                    <div class="card mb-4"> 
                                        <canvas id="graficoFaturamento"></canvas>
                                    </div>
                                </div>

                            </div>
                            
                        </div>

                    </div>
                </div>       
            </div>

        </div>

    </section>

    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="../js/chart.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
    relatorio.event.init();
});

var relatorio = {};

        var GRAFICO;
        var LINHAS = <?php echo $mesesJson; ?>;
        var VALORES = <?php echo $valoresJson; ?>;
        var PEDIDOS = <?php echo $pedidosJson; ?>;

        relatorio.event = {
            init: () => {
                relatorio.method.iniciarGrafico();
                relatorio.method.listarPedidos();
                relatorio.method.exibirRelatorio();  // Chama a função para exibir o relatório
            }
        }


relatorio.method = {

    iniciarGrafico: () => {
        const ctx = document.getElementById('graficoFaturamento').getContext("2d");

        GRAFICO = new Chart(ctx, {
            type: "line",
            data: {
                labels: LINHAS,
                datasets: [
                    {
                        label: "Faturamento",
                        data: VALORES,
                        borderWidth: 6,
                        fill: true,
                        backgroundColor: '#f8f8bb',
                        borderColor: '#ffbf00',
                        pointBackgroundColor: '#ffbf00',
       
                        pointRadius: 5,
                        pointHoverRadius: 5,
                        pointHitDetectionRadius: 35,
                        pointBorderWidth: 2.5,
                    },
                ],
            },
            options: {
                legend: { display: false },
                tooltips: {
                    enabled: false,
                    custom: function (tooltipModel) {
                        var tooltipEl = document.getElementById('chartjs-tooltip');
                        if (!tooltipEl) {
                            tooltipEl = document.createElement('div');
                            tooltipEl.id = 'chartjs-tooltip';
                            tooltipEl.innerHTML = '<table></table>';
                            document.body.appendChild(tooltipEl);
                        }
                        if (tooltipModel.opacity === 0) {
                            tooltipEl.style.opacity = 0;
                            return;
                        }
                        tooltipEl.classList.remove('above', 'below', 'no-transform');
                        if (tooltipModel.yAlign) {
                            tooltipEl.classList.add(tooltipModel.yAlign);
                        } else {
                            tooltipEl.classList.add('no-transform');
                        }

                        function getBody(bodyItem) {
                            return bodyItem.lines;
                        }

                        if (tooltipModel.body) {
                            var titleLines = tooltipModel.title || [];
                            var bodyLines = tooltipModel.body.map(getBody);
                            var innerHtml = '<thead>';

                            titleLines.forEach(function (title) {
                                innerHtml += '<tr><th>' + title + '</th></tr>';
                            });
                            innerHtml += '</thead><tbody>';

                            bodyLines.forEach(function (body, i) {
                                let valor = body[0].split(':')[1].trim();
                                let texto = body[0].split(':')[0].trim();

                                let formatado = texto + ': <b>R$ ' + valor + '</b>';

                                innerHtml += '<tr><td>' + formatado + '</td></tr>';
                                
                            });
                            innerHtml += '</tbody>';

                            var tableRoot = tooltipEl.querySelector('table');
                            tableRoot.innerHTML = innerHtml;
                        }

                        var position = this._chart.canvas.getBoundingClientRect();

                        tooltipEl.style.opacity = 1;
                        tooltipEl.style.position = 'absolute';
                        tooltipEl.style.left = position.left + window.pageXOffset + tooltipModel.caretX + 'px';
                        tooltipEl.style.top = position.top + window.pageYOffset + tooltipModel.caretY + 'px';
                        tooltipEl.style.fontFamily = tooltipModel._bodyFontFamily;
                        tooltipEl.style.fontSize = tooltipModel.bodyFontSize + 'px';
                        tooltipEl.style.fontStyle = tooltipModel._bodyFontStyle;
                        tooltipEl.style.padding = tooltipModel.yPadding + 'px ' + tooltipModel.xPadding + 'px';
                        tooltipEl.style.pointerEvents = 'none';
                    }
                },
                scales: {
                    yAxes: [
                        {
                            ticks: {
                                beginAtZero: false,
                                fontColor: '#999999',
                                fontSize: 10,
                                callback: (value, index, values) => {
                                    if (parseInt(value) >= 1000) {
                                        return 'R$' + value.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
                                    } else {
                                        return 'R$' + value;
                                    }
                                }
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            }
                        },
                    ],
                    xAxes: [
                        {
                            ticks: {
                                fontColor: '#999999',
                                fontSize: 10
                            },
                            gridLines: {
                                display: false,
                                drawBorder: false
                            }
                        }
                    ]
                },
            },
        });
    },

    exibirRelatorio: () => {
        const relatorioContainer = document.getElementById('relatorioContainer');
        let relatorioHTML = `<table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Mês</th>
                                        <th>Quantidade de Pedidos</th>
                                        <th>Valor Total</th>
                                    </tr>
                                </thead>
                                <tbody>`;

        PEDIDOS.forEach((pedido) => {
            relatorioHTML += `<tr>
                                <td>${pedido.mes}</td>
                                <td>${pedido.quantidade}</td>
                                <td>R$ ${pedido.valor}</td>
                              </tr>`;
        });

        relatorioHTML += `</tbody>
                        </table>`;

        relatorioContainer.innerHTML = relatorioHTML;
    }

}

function redirecionarSeRecarregar() {
    // Verifica se a página foi recarregada
    if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
        window.location.href = "relatorio.php";
    }
}

window.onload = redirecionarSeRecarregar;

    </script>

</body>
</html>