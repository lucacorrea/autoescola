<?php

    //----------SESSION---------------------------

        session_start();

        function verificarAcesso() {
            if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
                $nivel_usuario = $_SESSION['nivel'];
                if (in_array($nivel_usuario, ['admin', 'presidente', 'suporte'])) {
                    return true;
                }
            }
            header("Location: loader.php");
            exit();
        }

        verificarAcesso();

    //----------END SESSION-----------------------

    //----------SESSION USER----------------------

        include 'conexao.php';

        $id_usuario = $_SESSION['id_usuario'];
        $query = "SELECT nome, email FROM usuarios WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuario) {
            $nome_usuario = $usuario['nome'];
            $email_usuario = $usuario['email'];
        } else {
            die("Usuário não encontrado.");
        }

    //----------END SESSION USER------------------

    //----------SESSION IMAGE EMPRESA-------------

        $id_associacao = 1;
        $query = "SELECT logo_image FROM associacoes WHERE id = :id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id', $id_associacao, PDO::PARAM_INT);
        $stmt->execute();
        $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

        $logoImage = $associacao['logo_image'] ?? "";

    //----------END SESSION IMAGE EMPRESA---------

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <title>Página Inicial - Autoescola</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css" />
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/painel.css" />
        <link rel="stylesheet" href="./css/dashboard.css">
        <link rel="stylesheet" href="./css/notification.css">
        <!-- End custom CSS -->

    </head>

    <body>

        <div class="notification" id="notification">Há alunos fazendo aniversário! Clique aqui para parabenizá-los.</div>


        <!-- section -->
        <section class="bg-menu">

            <!-- menu-left -->
            <div class="menu-left">

                <!-- logo -->
                <div class="logo">

                    <?php if (!empty($logoImage)): ?>
                        <img class="logo-admin" src="uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" width="100" alt="Logo">
                    <?php else: ?>
                        
                    <?php endif; ?>

                </div>
                <!-- End logo -->

                <!-- menus -->
                <div class="menus">

                    <a href="./home.php" class="menu-item active">
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
                <!-- End menus -->

            </div>
            <!-- End menu-left -->

            <!-- conteudo -->
            <div class="conteudo">

                <!-- menu-top -->
                <div class="menu-top">

                    <div class="container">

                        <div class="row">

                            <div class="col-12 d-flex align-items-center mt-4">

                                <h1 class="title-page">

                                    <b>
                                        <i class="fas fa-home"></i>&nbsp; Seja Bem-Vindo(a)!
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
                <!-- End menu-top -->

                <!-- Main -->
                <main class="main-container">

                    <div class="main-title">

                        <h2></h2>

                    </div>

                    <!-- main-cards -->
                    <div class="main-cards">

                        <!-- card-1 -->
                        <a href="./instrutores.php" class="link-card">

                            <div class="card">

                                <div class="card-inner">
                                
                                    <h3 style='color:white'>INSTRUTORES</h3>
                                    <span class="fas fa-chalkboard-teacher card-icon-dash"></span>

                                </div>

                                <h1> 

                                    <?php

                                        include 'conexao.php';

                                        try {
                                            // Consulta para contar o total de instrutores
                                            $query = "SELECT COUNT(*) AS total_instrutores FROM instrutores";
                                            $stmt = $conn->query($query); // Executa a consulta
                                            
                                            // Obtém o resultado
                                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                            
                                            // Verifica se obteve um resultado
                                            if ($row) {
                                                echo "<span style='color:white'>" . $row["total_instrutores"] . "</span>";
                                            } else {
                                                echo "<b>0</b>";
                                            }
                                        } catch (PDOException $e) {
                                            echo "Erro: " . $e->getMessage();
                                        } finally {
                                            // Fecha a conexão
                                            $conn = null;
                                        }

                                    ?>

                                </h1>

                            </div>

                        </a>
                        <!-- End card-1 -->

                        <!-- card-2 -->
                        <a href="./instrutores.php" class="link-card">

                            <div class="card" style="background-color: #ff6d00 !important;">

                                <div class="card-inner">

                                    <h3 style='color:white'>PLACAS</h3>
                                    <span class="fas fa-car card-icon-dash"></span>

                                </div>

                                <h1>

                                    <?php
                                        include 'conexao.php';

                                        try {
                                            // Contar o total de placas únicas
                                            $query_placas = "SELECT COUNT(DISTINCT placa_instrutor) AS total_placas FROM instrutores";
                                            $stmt_placas = $conn->query($query_placas);
                                            $row_placas = $stmt_placas->fetch(PDO::FETCH_ASSOC);

                                            // Verifica se há resultado e armazena o total de placas
                                            $total_placas = $row_placas ? $row_placas["total_placas"] : 0;

                                            // Exibir resultados
                                            echo "<span style='color:white'>" . $total_placas . "</span>";
                                        } catch (PDOException $e) {
                                            echo "Erro: " . $e->getMessage();
                                        } finally {
                                            // Fecha a conexão
                                            $conn = null;
                                        }
                                    ?>

                                </h1>

                            </div>

                        </a>
                        <!-- End card-2 -->

                        <!-- card-3 -->
                        <a href="./alunos.php" class="link-card">

                            <div class="card" style="background-color: #2e7d32 !important;">

                                <div class="card-inner">

                                    <h3 style='color:white'>ALUNOS</h3>
                                    <span class="fas fa-users card-icon-dash"></span>

                                </div>

                                <h1>

                                    <?php

                                        include 'conexao.php'; // Inclui o arquivo de conexão

                                        try {
                                            // Consulta para contar o total de alunos
                                            $query = "SELECT COUNT(*) AS total_alunos FROM alunos";
                                            $stmt = $conn->query($query);

                                            // Verifica se há resultados
                                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $total_alunos = $row ? $row["total_alunos"] : 0;

                                            // Exibe o total de alunos
                                            echo "<span style='color:white'>" . $total_alunos . "</span>";
                                        } catch (PDOException $e) {
                                            // Exibe mensagem de erro em caso de exceção
                                            echo "Erro: " . $e->getMessage();
                                        } finally {
                                            // Fecha a conexão com o banco de dados
                                            $conn = null;
                                        }

                                    ?>

                                </h1>

                            </div>

                        </a>
                        <!-- End card-3 -->

                        <!-- card-4 -->
                        <a href="./legislacao.php" class="link-card">

                            <div class="card" style="background-color: #d50000 !important;">

                                <div class="card-inner">

                                    <h3 style='color:white'>TURMAS</h3>
                                    <span class="fas fa-book-open card-icon-dash"></span>

                                </div>

                                <h1>

                                    <?php

                                        include 'conexao.php'; // Inclui o arquivo de conexão

                                        try {
                                            // Consulta para contar o total de turmas
                                            $query = "SELECT COUNT(*) AS total_turmas FROM turmas";
                                            $stmt = $conn->query($query);

                                            // Obtém o resultado da consulta
                                            $row = $stmt->fetch(PDO::FETCH_ASSOC);
                                            $total_turmas = $row ? $row["total_turmas"] : 0;

                                            // Exibe o total de turmas
                                            echo "<span style='color:white'>" . $total_turmas . "</span>";
                                        } catch (PDOException $e) {
                                            // Trata erros de consulta
                                            echo "Erro: " . $e->getMessage();
                                        } finally {
                                            // Fecha a conexão com o banco de dados
                                            $conn = null;
                                        }

                                    ?>

                                </h1>

                            </div>
                            
                        </a>
                        <!-- End card-4 -->

                    </div>
                    <!-- End main-cards -->

                    <!-- charts -->
                    <div class="charts">

                        <div class="charts-card">

                            <h2 class="chart-title">Alunos em cada Categoria</h2>
                            <div id="bar-chart"></div>

                        </div>

                        <div class="charts-card">

                            <h2 class="chart-title">Total de Vencimento de Processos por Mês</h2>
                            <div id="processos-vencendo-no-mes"></div>
                            
                        </div>

                    </div>
                    <!-- End charts -->

                </main>
                <!-- End Main -->

            </div>
            <!-- End conteudo -->

        </section>
        <!-- End section -->    

        <!-- Scripts -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.35.5/apexcharts.min.js"></script>
        <script src="js/logout.js"></script>
        <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
        <script>

            //------------NOTIFICATION---------------

                const notification = document.getElementById('notification');

                    function mostrarNotificacao() {
                        notification.style.display = 'block';
                        notification.addEventListener('click', function() {
                        window.location.href = 'enviarMensagem.php'; 
                    });
                    setTimeout(() => {
                        notification.style.display = 'none';
                    }, 5000);
                }

                function obterDadosAlunos() {
                    return fetch('mensagemEmail.php')
                        .then(response => response.json())
                        .catch(error => {
                            console.error('Erro ao buscar dados:', error);
                        });
                }

                document.addEventListener('DOMContentLoaded', function() {
                    obterDadosAlunos().then(aniversariantes => {
                        if (aniversariantes && aniversariantes.length > 0) {
                            mostrarNotificacao();
                        } else {
                            console.log('Não há aniversariantes hoje.');
                        }
                    });
                });

            //------------END NOTIFICATION-----------

            //------------SIDEBAR TOGGLE-------------

                let sidebarOpen = false;
                    const sidebar = document.getElementById('sidebar');

                    function openSidebar() {
                    if (!sidebarOpen) {
                        sidebar.classList.add('sidebar-responsive');
                        sidebarOpen = true;
                    }
                    }

                    function closeSidebar() {
                    if (sidebarOpen) {
                        sidebar.classList.remove('sidebar-responsive');
                        sidebarOpen = false;
                    }
                }

            //------------END SIDEBAR TOGGLE---------

            //------------GET CATEGORIAS-------------

                <?php
                    include 'conexao.php';

                    $total_servicos = [];
                    $nomes_servicos = [];

                    try {
                        $query_servicos = "SELECT categoria, COUNT(*) AS total FROM servicos_aluno GROUP BY categoria";
                        $stmt_servicos = $conn->query($query_servicos);
                        $result_servicos = $stmt_servicos->fetchAll(PDO::FETCH_ASSOC);

                        foreach ($result_servicos as $row) {
                            $nomes_servicos[] = $row["categoria"];
                            $total_servicos[] = $row["total"];
                        }
                    } catch (PDOException $e) {
                        echo "Erro: " . $e->getMessage();
                    } finally {
                        $conn = null;
                    }

                    foreach ($nomes_servicos as $index => $nome) {
                        echo "Categoria: " . $nome . " - Total: " . $total_servicos[$index] . "<br>";
                    }
                ?>

            //------------END GET CATEGORIAS---------


            // -----------CHARTS --------------------

                <?php
                    // Conectar ao banco de dados e obter os dados
                    include 'conexao.php'; // Inclui o arquivo de conexão

                    $total_servicos = [];
                    $nomes_servicos = [];

                    try {
                        // Consulta para obter o total de cada serviço
                        $query_servicos = "SELECT categoria, COUNT(*) AS total FROM servicos_aluno GROUP BY categoria";
                        $stmt_servicos = $conn->query($query_servicos);
                        $result_servicos = $stmt_servicos->fetchAll(PDO::FETCH_ASSOC);

                        // Preenche os arrays com os nomes das categorias e os totais de serviços
                        foreach ($result_servicos as $row) {
                            $nomes_servicos[] = $row["categoria"];
                            $total_servicos[] = $row["total"];
                        }
                    } catch (PDOException $e) {
                        echo "Erro: " . $e->getMessage();
                    } finally {
                        // Fecha a conexão com o banco de dados
                        $conn = null;
                    }
                ?>

                var barChartData = [
                    {
                        data: <?php echo json_encode($total_servicos); ?>,
                        name: 'Total'
                    }
                ];

                var barChartOptions = {
                    series: barChartData,
                    chart: {
                        type: 'bar',
                        background: 'transparent',
                        height: 230,
                        toolbar: {
                            show: false,
                        },
                    },
                    colors: ['#2e7d32'],
                    plotOptions: {
                        bar: {
                            distributed: true,
                            borderRadius: 4,
                            horizontal: false,
                            columnWidth: '40%',
                        },
                    },
                    dataLabels: {
                        enabled: false,
                    },
                    fill: {
                        opacity: 1,
                    },
                    grid: {
                        borderColor: '#2e7d32',
                        yaxis: {
                            lines: {
                                show: true,
                            },
                        },
                        xaxis: {
                            lines: {
                                show: true,
                            },
                        },
                    },
                    legend: {
                        labels: {
                            colors: '#333',
                        },
                        show: true,
                        position: 'top',
                    },
                    stroke: {
                        colors: ['transparent'],
                        show: true,
                        width: 2,
                    },
                    tooltip: {
                        shared: true,
                        intersect: false,
                        theme: 'dark',
                    },
                    xaxis: {
                        categories: <?php echo json_encode($nomes_servicos); ?>,
                        title: {
                            style: {
                                color: '#000',
                            },
                        },
                        axisBorder: {
                            show: true,
                            color: '#ccc',
                        },
                        axisTicks: {
                            show: true,
                            color: '#ccc',
                        },
                        labels: {
                            style: {
                                colors: '#333',
                            },
                        },
                    },
                    yaxis: {
                        title: {
                            text: 'Contagem',
                            style: {
                                color: '#333',
                            },
                        },
                        axisBorder: {
                            color: '#ccc',
                            show: true,
                        },
                        axisTicks: {
                            color: '#ccc',
                            show: true,
                        },
                        labels: {
                            style: {
                                colors: '#333',
                            },
                        },
                    },
                };

                var barChart = new ApexCharts(
                    document.querySelector('#bar-chart'),
                    barChartOptions
                );
                barChart.render();

            //------------END CHART------------------
            

            //------------AREA CHART-----------------

                document.addEventListener("DOMContentLoaded", function () {
                    // Fazer requisição para buscar os dados do PHP
                    fetch('get_vencimento.php')  // Altere o caminho do arquivo PHP, se necessário
                        .then(response => response.json())
                        .then(dados => {
                            // Verificar se há dados
                            if (dados.length > 0) {
                                const meses = dados.map(p => p.mes);       // Obter os nomes dos meses em português
                                const totais = dados.map(p => p.total);    // Obter o total de processos por mês

                                // Configurações do gráfico
                                var options = {
                                    chart: {
                                        type: 'bar',
                                        height: 230,
                                        toolbar: {
                                            show: true,  // Ativar barra de ferramentas
                                            tools: {
                                                download: true,  // Ativar botão de download
                                            },
                                            offsetX: -10,  // Ajustar posição da barra de ferramentas
                                            offsetY: 0,
                                            style: {
                                                background: '#000',  // Cor de fundo da barra de ferramentas
                                                color: '#fff'  // Cor do ícone de download
                                            }
                                        }
                                    },
                                    series: [{
                                        name: 'Total de Processos',
                                        data: totais
                                    }],
                                    xaxis: {
                                        categories: meses,  // Nome dos meses como categorias
                                        title: {
                                            text: '',
                                            style: {
                                                color: '#000',  // Cor do texto do título dos meses
                                                fontSize: '14px',
                                                fontWeight: 'bold'
                                            }
                                        },
                                        labels: {
                                            style: {
                                                colors: '#000',  // Cor dos rótulos do eixo x
                                                fontSize: '12px'
                                            }
                                        }
                                    },
                                    yaxis: {
                                        title: {
                                            text: 'Total de Processos',
                                            style: {
                                                color: '#000',  // Cor do texto do título do eixo y
                                                fontSize: '14px',
                                                fontWeight: 'bold'
                                            }
                                        },
                                        labels: {
                                            style: {
                                                colors: '#000',  // Cor dos rótulos do eixo y
                                                fontSize: '12px'
                                            }
                                        }
                                    },
                                    plotOptions: {
                                        bar: {
                                            horizontal: false, // Barras verticais (da esquerda para a direita)
                                            columnWidth: '50%',
                                            dataLabels: {
                                                position: 'top'
                                            },
                                            colors: {
                                                ranges: [],
                                                backgroundBarOpacity: 1
                                            },
                                            borderRadius: 5,  // Bordas arredondadas das barras
                                        }
                                    },
                                    states: {
                                        hover: {
                                            filter: {
                                                type: 'lighten',  // Aplicar efeito de clareamento ao passar o mouse
                                                value: 0.2       // Nível de clareamento
                                            }
                                        }
                                    },
                                    title: {
                                        text: '',
                                        align: 'center',
                                        style: {
                                            color: '#000',  // Cor do texto do título do gráfico
                                            fontSize: '16px',
                                            fontWeight: 'bold'
                                        }
                                    },
                                    colors: ['#2e7d32'],  // Cor das barras
                                    dataLabels: {
                                        enabled: false  // Desabilitar valores sobre as barras
                                    },
                                    tooltip: {
                                        theme: 'dark'  // Tema escuro para o tooltip
                                    }
                                };

                                // Renderizar o gráfico
                                var chart = new ApexCharts(document.querySelector("#processos-vencendo-no-mes"), options);
                                chart.render();
                            } else {
                                document.querySelector("#processos-vencendo-no-mes").innerText = "Nenhum processo encontrado nos últimos meses.";
                            }
                        })
                        .catch(error => console.error('Erro ao buscar dados:', error));
                });

            //------------END AREA CHART-------------

        </script>
        <!-- End Scripts -->

    </body>
    
</html>
