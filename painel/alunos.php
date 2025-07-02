<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


//-------------------SESSION------------------------

    session_start();

    function verificarAcesso() {
        if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
            $nivel_usuario = $_SESSION['nivel'];

            if($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
                return true;
            }
        }
        
        header("Location: loader.php");
        exit();
    }

    verificarAcesso();

//-------------------END SESSION--------------------


//------------------SESSION IMAGE EMPRESA-----------

    include 'conexao.php';

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


//------------------GET ALUNOS----------------------

    include "conexao.php";

    $data_atual = date('Y-m-d');


        //------------------GET PESQUISA--------------------

        $search = $_GET['search'] ?? '';
        $filtrar = $_GET['filtrar'] ?? 'todos';
        $ordem = $_GET['ordem'] ?? '';


        $sql = "
            SELECT 
                alunos.id, 
                alunos.nome, 
                alunos.cpf, 
                alunos.data_nascimento, 
                alunos.rg, 
                alunos.telefone, 
                alunos.renach, 
                alunos.ladv, 
                alunos.vencimento_processo, 
                alunos.rua, 
                alunos.bairro, 
                alunos.numero, 
                alunos.observacao,
                MAX(servicos_aluno.categoria) AS categoria, 
                MAX(login_aluno.email) AS email, 
                MAX(servicos_aluno.data_pagamento) AS data_pagamento
            FROM alunos 
            LEFT JOIN servicos_aluno ON alunos.nome = servicos_aluno.nome_aluno 
            LEFT JOIN login_aluno ON alunos.nome = login_aluno.nome_aluno 
            WHERE 1=1
        ";
        
        if (!empty($search)) {
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
                OR servicos_aluno.categoria LIKE :search
                OR servicos_aluno.data_pagamento LIKE :search
                OR login_aluno.email LIKE :search
            )";
        }
        
        if ($filtrar === 'por_nome') {
            $sql .= " GROUP BY alunos.id ORDER BY alunos.nome";
        } elseif ($filtrar === 'por_categoria') {
            $sql .= " AND servicos_aluno.categoria IS NOT NULL 
                    GROUP BY alunos.id ORDER BY MAX(servicos_aluno.categoria)";
        } elseif ($filtrar === 'por_parcelas') {
            $sql .= " AND EXISTS (
                SELECT 1 
                FROM servicos_aluno 
                WHERE servicos_aluno.nome_aluno = alunos.nome 
                AND servicos_aluno.forma_pagamento = 'Parcelado'
            )
            GROUP BY alunos.id";
        } else {
            $sql .= " GROUP BY alunos.id";
        }
        
        if ($ordem === 'alfabetica') {
            $sql .= " ORDER BY alunos.nome";
        }
        
        $stmt = $conn->prepare($sql);
        
        if (!empty($search)) {
            $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
        }
        
        $stmt->execute();
        
        $alunos_pendentes = [];
        $pendentes_count = 0;


        //------------------END GET PESQUISA----------------


        //-----------------GET SERVICÇOS--------------------
        $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($resultados) {
            foreach ($resultados as $row) {
                $sql_pendentes = "
                SELECT 1 
                FROM servicos_aluno
                WHERE nome_aluno = :nome_aluno 
                AND data_pagamento <= :data_atual
                AND data_pagamento IS NOT NULL
                LIMIT 1
                ";

                $stmt_pendentes = $conn->prepare($sql_pendentes);
                $stmt_pendentes->execute([
                    ':nome_aluno' => $row['nome'],
                    ':data_atual' => $data_atual
                ]);

                if ($stmt_pendentes->fetch()) {
                    $alunos_pendentes[$row['id']] = $row['nome'];
                    $pendentes_count++;
                }
            }
        //-----------------END GET SERVIÇOS-----------------
        }

//------------------END GET ALUNOS------------------

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <title>Painel - Alunos</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css" />
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/painel.css" />
        <link rel="stylesheet" href="./css/dashboard.css">
        <link rel="stylesheet" href="./css/modal.css">
        <link rel="stylesheet" href="./css/pesquisa.css">
        <!-- End custom CSS -->
    
    </head>

    <body>

        <!-- Section -->        
        <section class="bg-menu">

            <!-- menu-left -->
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

                    <a href="./alunos.php" class="menu-item active">
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
            <!-- End menu-left -->

            <!-- conteudos -->
            <div class="conteudo">

                <div class="menu-top">

                    <div class="container">

                        <div class="row">

                            <div class="col-12 d-flex align-items-center mt-4">

                                <h1 class="title-page">
                                    <b>
                                        <i class="fas fa-users"></i>&nbsp; PAINEL - ALUNOS
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

                
                <?php if ($pendentes_count > 0) : ?>
                    <div class="card-alerta mt-4" onclick="toggleTabelaPendentes()">
                        <strong>Notificação:</strong> Há <?php echo $pendentes_count; ?> aluno(s) com pagamento pendente.
                    </div>
                <?php endif; ?>

                <!-- main -->
                <main class="main-container">

                    <!-- conteudo-inner -->
                    <div class="conteudo-inner">
                        <div class="container">
                            <div class="row">

                                <div class="col-12 mt-0 cadastro">
                                    <div class="menus-config mb-4 mt-3">
                                        <a href="alunos.php" class="btn btn-white btn-sm active">
                                            <i class="fas fa-users"></i> Alunos Cadastrados
                                        </a>
                                        <a href="cadastroAluno.php" class="btn btn-white btn-sm">
                                            <i class="fas fa-user-plus"></i> Cadastrar Aluno
                                        </a>
                                        <a href="recibosAlunos.php" class="btn btn-white btn-sm">
                                            <i class="fas fa-receipt"></i> Recibos Alunos
                                        </a>
                                    </div>

                                    <div class="col-12" id="categorias">
                                        <div class="container-group mb-5">
                                            <div class="accordion" id="categoriasMenu">
                                                <div class="row">

                                                    <div class="col-md-12 mb-2">
                                                        <div class="card-search">
                                                            <div class="card-body">
                                                                <form method="GET" class="row g-3 align-items-center">

                                                                    <div class="col-md-6">
                                                                        <div class="container-cep">
                                                                            <input type="text" class="form-control" name="search" placeholder="Buscar aluno" />
                                                                            <div class="search">
                                                                                <button type="submit" class="btn btn-search active">
                                                                                    <i class="fa fa-search"></i>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="col-md-5 filtrar-input text-end">
                                                                        <div class="d-flex align-items-center justify-content-end">
                                                                            <select name="filtrar" id="filtrar" class="form-select me-2" onchange="this.form.submit()">
                                                                                <option value="todos">Selecionar Todos</option>
                                                                                <option value="por_nome">Por Nome</option>
                                                                                <option value="por_categoria">Por Categoria</option>
                                                                                <option value="por_parcelas">Por Alunos com parcelas</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>

                                                <!-- table -->
                                                <div class="mt-3 card-table">

                                                    <div class="card-drag" id="headingOne">
                                                        <div class="infos">
                                                            <a href="#" class="name-table mb-0" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                <span class="me-2"><i class="fas fa-users"></i></span>
                                                                <b>Lista de Alunos</b>
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                                        <div class="card-body">

                                                            <table class="table">
                                                                <thead>
                                                                    <tr>
                                                                        <th scope="col">Nome</th>
                                                                        <th scope="col">CPF</th>
                                                                        <th scope="col">L.A.D.V</th>
                                                                        <th scope="col">Categoria</th>
                                                                        <th scope="col">Venc. do Processo</th>
                                                                        <th scope="col">Ações</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>

                                                                    <?php
                                                                    
                                                                        if (!empty($resultados)) {
                                                                            foreach ($resultados as $row) {
                                                                                $classe_pendente = array_key_exists($row['id'], $alunos_pendentes) ? 'pendente' : '';

                                                                                $sql_categorias = "
                                                                                    SELECT categoria 
                                                                                    FROM servicos_aluno 
                                                                                    WHERE nome_aluno = :nome_aluno
                                                                                ";
                                                                                $stmt_categorias = $conn->prepare($sql_categorias);
                                                                                $stmt_categorias->execute([':nome_aluno' => $row['nome']]);
                                                                                $categorias = $stmt_categorias->fetchAll(PDO::FETCH_COLUMN);
                                                                                $categorias_str = implode(', ', $categorias);

                                                                                echo "<tr>";
                                                                                echo "<td class='$classe_pendente'>" . htmlspecialchars($row['nome']) . "</td>";
                                                                                echo "<td class='$classe_pendente'>" . htmlspecialchars($row['cpf']) . "</td>";

                                                                                if (!empty($row['ladv']) && $row['ladv'] !== '0000-00-00') {
                                                                                    $ladv = date('d/m/Y', strtotime($row['ladv']));
                                                                                } else {
                                                                                    $ladv = '00/00/0000';
                                                                                }
                                                                                echo "<td class='$classe_pendente'>" . htmlspecialchars($ladv) . "</td>";

                                                                                echo "<td class='$classe_pendente'>" . htmlspecialchars($categorias_str) . "</td>";
                                                                                if (!empty($row['vencimento_processo']) && $row['vencimento_processo'] !== '0000-00-00') {
                                                                                    $vencimento_processo = date('d/m/Y', strtotime($row['vencimento_processo']));
                                                                                } else {
                                                                                    $vencimento_processo = '00/00/0000';
                                                                                }
                                                                                echo "<td class='$classe_pendente'>" . htmlspecialchars($vencimento_processo) . "</td>";

                                                                                echo "<td>";
                                                                                echo "<div class='actions'>";
                                                                                echo "<div class='icon-action'>";
                                                                                echo "<div class='tooltip'>Editar</div>";
                                                                                echo '<a href="editarAluno.php?id=' . $row["id"] . '"><i class="fas fa-pencil-alt"></i></a>';
                                                                                echo "</div>";
                                                                                echo "<div class='icon-action'>";
                                                                                echo "<div class='tooltip'>Excluir</div>";
                                                                                echo '<button style="border: none !important;" class="icon-action" onclick="openModal(' . $row["id"] . ')"><i class="fas fa-trash-alt"></i></button>';
                                                                                echo "</div>";
                                                                                echo "<div class='icon-action'>";
                                                                                echo "<div class='tooltip'>Criar Ficha</div>";
                                                                                echo '<a href="criarFicha.php?id=' . $row["id"] . '"><i class="fas fa-calendar-check"></i></a>';
                                                                                echo "</div>";
                                                                                echo "<div class='icon-action'>";
                                                                                echo "<div class='tooltip'>Contrato</div>";
                                                                                echo '<a href="contrato.php?id=' . $row["id"] . '"><i class="fas fa-file-alt"></i></a>';
                                                                                echo "</div>";
                                                                                echo "<div class='icon-action'>";
                                                                                echo "<div class='tooltip'>Visualizar</div>";
                                                                                echo '<a href="dadosAluno.php?id=' . $row["id"] . '" class="icon-action"><i class="fas fa-eye"></i></a>';
                                                                                echo "</div>";

                                                                                $alunoId = $row["id"];

                                                                                $sql_cadastrado = "
                                                                                    SELECT 1 
                                                                                    FROM servicos_aluno 
                                                                                    WHERE nome_aluno = :nome_aluno 
                                                                                    LIMIT 1
                                                                                ";
                                                                                $stmt_cadastrado = $conn->prepare($sql_cadastrado);
                                                                                $stmt_cadastrado->execute([':nome_aluno' => $row['nome']]);
                                                                                $alunoCadastrado = $stmt_cadastrado->fetch();

                                                                                $sql_pagamento_parcelado = "
                                                                                    SELECT 1 
                                                                                    FROM servicos_aluno 
                                                                                    WHERE nome_aluno = :nome_aluno 
                                                                                    AND numero_parcelas > 0 
                                                                                    LIMIT 1
                                                                                ";
                                                                                $stmt_pagamento_parcelado = $conn->prepare($sql_pagamento_parcelado);
                                                                                $stmt_pagamento_parcelado->execute([':nome_aluno' => $row['nome']]);
                                                                                $pagamentoParcelado = $stmt_pagamento_parcelado->fetch();

                                                                                if ($pagamentoParcelado) {
                                                                                    echo "<div class='icon-action'>";
                                                                                    echo "<div class='tooltip'>Renovar</div>";
                                                                                    echo '<a href="renovarParcelas.php?id=' . $alunoId . '" class="icon-action"><i class="fas fa-sync-alt"></i></a>';
                                                                                    echo "</div>";
                                                                                }

                                                                                if ($alunoCadastrado) {
                                                                                    echo '<a href="cadastrarServicoAluno.php?id=' . $alunoId . '" class="icon-action hidden"><i class="fas fa-cog"></i></a>';
                                                                                } else {
                                                                                    echo "<div class='icon-action'>";
                                                                                    echo "<div class='tooltip'>Serviços</div>";
                                                                                    echo '<a href="cadastrarServicoAluno.php?id=' . $alunoId . '" class="icon-action"><i class="fas fa-cog"></i></a>';
                                                                                    echo "</div>";
                                                                                }

                                                                                echo "</div>";
                                                                                echo "</td>";
                                                                                echo "</tr>";

                                                                                echo '<div id="confirmDeleteHorarioModal' . $row["id"] . '" class="modal-horarios" style="display: none;">';
                                                                                echo '<div class="modal-horarios-content">';
                                                                                echo '<div class="modal-horarios-header">';
                                                                                echo '<span class="close" onclick="closeModal(' . $row["id"] . ')">&times;</span>';
                                                                                echo '<h2 class="title-modal"><i class="fas fa-trash-alt"></i> Excluir Aluno</h2>';
                                                                                echo '</div>';
                                                                                echo '<p class="p mt-5">Tem certeza que deseja excluir este Aluno?</p>';
                                                                                echo '<button class="excluir excluir-secondary" onclick="closeModal(' . $row["id"] . ')">';
                                                                                echo '<span class="text">Não</span>';
                                                                                echo '</button>';
                                                                                echo '<a href="excluirAluno.php?id=' . $row["id"] . '"  class="excluir">Sim</a>';
                                                                                echo '</div>';
                                                                                echo '</div>';
                                                                            }
                                                                        } else {
                                                                            echo "<tr><td colspan='6'>Nenhum aluno encontrado.</td></tr>";
                                                                        }

                                                                    ?>

                                                                </tbody>
                                                            </table>

                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- End table -->

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End conteudo-inner -->

                </main>
                <!-- End main -->

            </div>
            <!-- End conteudo -->

        </section>
        <!-- End Section -->
        
        <!-- Scripts -->
        <script src="../js/bootstrap.bundle.min.js"></script>
        <script src="../js/fontawesome.js"></script>
        <script src="../js/main.js"></script>
        <script src="./js/dashboard.js"></script>
        <script src="./js/modal.js"></script>
        <script>

            window.onload = function() {
                uppercaseBElements();
            };

            function openModal(id) {
                console.log('Abrindo modal com ID:', id);
                var modal = document.getElementById('confirmDeleteHorarioModal' + id);
                console.log('Modal:', modal);
                if (modal) {
                    modal.style.display = 'block';
                    adjustTableOpacity(0); // Define a opacidade para 50%
                } else {
                    console.error('Modal não encontrada.');
                }
            }

            function closeModal(id) {
                console.log('Fechando modal com ID:', id);
                var modal = document.getElementById('confirmDeleteHorarioModal' + id);
                if (modal) {
                    modal.style.display = 'none';
                    adjustTableOpacity(1); // Retorna a opacidade ao normal (100%)
                } else {
                    console.error('Modal não encontrada.');
                }
            }

            function adjustTableOpacity(opacity) {
                var thElements = document.querySelectorAll('.table-responsive thead th');
                thElements.forEach(function(th) {
                    th.style.opacity = opacity;
                });
            }

            function redirecionarSeRecarregar() {
                if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
                    window.location.href = "alunos.php";
                }
            }


            window.onload = redirecionarSeRecarregar;


        </script>
        <!-- End Scripts -->

    </body>

</html>

