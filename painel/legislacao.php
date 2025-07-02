<?php

// SESSION
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
// END SESSION


// SESSION USER
    require "conexao.php";

    $id_usuario = $_SESSION['id_usuario'];

    $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";

    try {
        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $nome_usuario = $row['nome'];
            $email_usuario = $row['email'];
        } else {
            echo "Nenhum resultado encontrado.";
        }
    } catch (PDOException $e) {
        echo "Erro na consulta: " . $e->getMessage();
    }

    $conn = null;
// END SESSION USER


// SESSION IMAGE EMPRESSA
    include "conexao.php";

    $id = 1; 

    $sql = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

    $logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";
// END SESSION IMAGE EMPRESA


// SESSION LISTAGEM DAS TURMAS
    include "conexao.php";

    $sql = "SELECT * FROM turmas WHERE 1=1";
    $params = [];


    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $search = $_GET['search'];
        $sql .= " AND (local LIKE :search OR instrutor LIKE :search OR data_inicio LIKE :search OR data_fim LIKE :search OR horario_inicio LIKE :search OR horario_fim LIKE :search)";
        $params[':search'] = "%$search%";
    }

    if (isset($_GET['filtrar']) && $_GET['filtrar'] !== 'todos') {
        $filtro = $_GET['filtrar'];
        
        switch ($filtro) {
            case 'por_matutino':
                $sql .= " AND turno = :turno";
                $params[':turno'] = 'Matutino';
                break;
            case 'por_vespertino':
                $sql .= " AND turno = :turno";
                $params[':turno'] = 'Vespertino';
                break;
            case 'por_noturno':
                $sql .= " AND turno = :turno";
                $params[':turno'] = 'Noturno';
                break;
        }
    }

    try {
        $stmt = $conn->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Erro ao executar a consulta: " . $e->getMessage();
    }
// END SESSION LISTAGEM DAS TURMAS

?>


<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <title>Painel - Legislação</title>

        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css" />
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/painel.css" />
        <link rel="stylesheet" href="./css/pesquisa.css">
        <link rel="stylesheet" href="./css/modal.css">
    </head>

    <body>

        <!-- Section -->
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

            <!-- Conteudo -->
            <div class="conteudo">

                <div class="menu-top">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center mt-4">
                                <h1 class="title-page"><b><i class="fas fa-book-open"></i>&nbsp; PAINEL - LEGISLAÇÃO</b></h1>
                                <div class="container-right">
                                    <div class="container-dados">
                                        <p><?php echo $nome_usuario; ?></p>
                                        <?php if ($email_usuario) { ?>
                                        <span><?php echo $email_usuario; ?></span>
                                        <?php } ?>
                                    </div>
                                    <a href="logout.php" class="btn btn-white btn-sm"><i class="fas fa-sign-out-alt"></i>&nbsp; Sair</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Main -->
                <main class="main-container">
                    <div class="conteudo-inner">
                        <div class="container">
                            <div class="row">

                                <div class="col-12 mt-0 cadastro">
                                    <div class="menus-config mb-5 mt-5" style="margin-top: -27px;">
                                        <a href="./legislacao.php" class="btn btn-white btn-sm active"><i class="fas fa-users"></i> Turmas Cadastradas</a>
                                        <a href="./cadastrarTurma.php" class="btn btn-white btn-sm"><i class="fas fa-plus-circle"></i> Cadastrar Turma</a>
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
                                                                            <input type="text" class="form-control" name="search" placeholder="Buscar Turma"/>
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
                                                                                <option value="por_matutino">Por Turno Matutino</option>
                                                                                <option value="por_vespertino">Por Turno Vespertino</option>
                                                                                <option value="por_noturno">Por Turno Noturno</option>
                                                                            </select>
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
                                                                <span class="me-2"><i class="fas fa-users"></i></span>
                                                                <b>Lista de Turmas</b>
                                                            </a>
                                                        </div>
                                                    </div>

                                                    <div id="collapseOne" class="collapse show" data-parent="#categoriasMenu">
                                                        <div class="lista-produtos" id="listaProdutos-one">
                                                            <table class="table mt-3 data-table"  id="myTable">
                                                                <thead>
                                                                <tr>
                                                                    <th scope="col">Local</th>
                                                                    <th scope="col">Instrutor</th>
                                                                    <th scope="col">Data Início</th>
                                                                    <th scope="col">Data Final</th>
                                                                    <th scope="col">Horário Início</th>
                                                                    <th scope="col">Horário Final</th>
                                                                    <th class="col">Turno</th>
                                                                    <th class="col">Ações</th>
                                                                </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php if (!empty($result)): ?>
                                                                        <?php foreach ($result as $row): ?>
                                                                            <tr>
                                                                                <td><?php echo htmlspecialchars($row['local']); ?></td>
                                                                                <td><?php echo htmlspecialchars($row['instrutor']); ?></td>
                                                                                <td><?php echo date('d/m/Y', strtotime($row['data_inicio'])); ?></td>
                                                                                <td><?php echo date('d/m/Y', strtotime($row['data_fim'])); ?></td>
                                                                                <td><?php echo date('H:i', strtotime($row['horario_inicio'])); ?></td>
                                                                                <td><?php echo date('H:i', strtotime($row['horario_fim'])); ?></td>
                                                                                <td><?php echo htmlspecialchars($row['turno']); ?></td>
                                                                                <td>
                                                                                    <div class="actions">
                                                                                        <div class="icon-action">
                                                                                            <div class="tooltip">
                                                                                                Editar
                                                                                            </div>
                                                                                            <a href="editarTurma.php?id=<?php echo htmlspecialchars($row['id']); ?>"><i class="fas fa-pencil-alt"></i></a>
                                                                                        </div>
                                                                                        <div class="icon-action">
                                                                                            <div class="tooltip">
                                                                                                Excluir
                                                                                            </div>
                                                                                            <button onclick="openModal(<?php echo htmlspecialchars($row['id']); ?>)" class="icon-action" style="border: none;"><i class="fas fa-trash-alt"></i></button>
                                                                                        </div>
                                                                                        <div class="icon-action">
                                                                                            <div class="tooltip alunos" style="width: 120px !important;
                                                                                            ">
                                                                                                Inserir Alunos
                                                                                            </div>
                                                                                            <a href="inserirAlunosAdicionais.php?id=<?php echo htmlspecialchars($row['id']); ?>"><i class="fas fa-user"></i></a>
                                                                                        </div>
                                                                                        <div class="icon-action">
                                                                                            <div class="tooltip">
                                                                                                Ver Alunos
                                                                                            </div>
                                                                                            <a href="alunosTurma.php?id=<?php echo htmlspecialchars($row['id']); ?>"><i class="fas fa-users"></i></a>
                                                                                        </div>
                                                                                        <div class="icon-action">
                                                                                            <div class="tooltip">
                                                                                                Dados
                                                                                            </div>
                                                                                            <a href="dadosTurma.php?id=<?php echo htmlspecialchars($row['id']); ?>"><i class="fas fa-eye"></i></a>
                                                                                        </div>
                                                                                    </div>
                                                                                </td>
                                                                            </tr>
                                                                        <?php endforeach; ?>
                                                                    <?php else: ?>
                                                                        <tr>
                                                                            <td colspan="8">Nenhuma turma encontrada.</td>
                                                                        </tr>
                                                                    <?php endif; ?>
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
                <div id="confirmDeleteHorarioModal" class="modal-horarios" style="display: none;">

                    <div class="modal-horarios-content">

                        <div class="modal-horarios-header">
                            <span class="close" onclick="closeModal()">&times;</span>
                            <h2 class="title-modal"><i class="fas fa-trash-alt"></i> Excluir Turma</h2>
                        </div>

                        <p class="p mt-5">Tem certeza que deseja excluir esta Turma?</p>
                        <button class="excluir excluir-secondary" onclick="closeModal()">
                            <span class="text">Não</span>
                        </button>
                        <a href="#" id="confirmDeleteLink" class="excluir">Sim</a>

                    </div>

                </div>
                <!-- End Modal -->

            </div>
            <!-- End conteudo -->

        </section>
        <!-- End Section -->


        <!-- Scripts -->
        <script>

            function openModal(turmaId) {
                var modal = document.getElementById('confirmDeleteHorarioModal');
                var confirmDeleteLink = document.getElementById('confirmDeleteLink');
                confirmDeleteLink.href = 'excluirTurma.php?id=' + turmaId;  // Define o link com o ID da turma
                modal.style.display = 'block';
            }

            function closeModal() {
                var modal = document.getElementById('confirmDeleteHorarioModal');
                modal.style.display = 'none';
            }

            function redirecionarSeRecarregar() {
                // Verifica se a página foi recarregada
                if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
                window.location.href = "legislacao.php";
                }
            }


            window.onload = redirecionarSeRecarregar;

        </script>
        <!-- End Scripts -->

    </body>

</html>
