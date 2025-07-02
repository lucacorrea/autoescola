<?php

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

// Verificar o ID do aluno passado na URL
if (isset($_GET['id'])) {
    $id_aluno = $_GET['id'];
} else {
    echo "ID do aluno não encontrado.";
    exit;
}

// Consulta para buscar o RG do aluno com base no ID
$sql_rg_aluno = "SELECT rg FROM alunos WHERE id = :id";
$stmt_rg_aluno = $conn->prepare($sql_rg_aluno);
$stmt_rg_aluno->bindParam(':id', $id_aluno, PDO::PARAM_INT);
$stmt_rg_aluno->execute();
$rg_aluno = $stmt_rg_aluno->fetchColumn();

if ($rg_aluno === false) {
    echo "RG do aluno não encontrado.";
    exit;
}

// Consulta para buscar fichas associadas ao RG do aluno
$sql_fichas = "SELECT f.*, a.rg 
               FROM fichas f 
               JOIN alunos a ON a.rg = f.rg 
               WHERE a.rg = :rg";
$stmt_fichas = $conn->prepare($sql_fichas);
$stmt_fichas->bindParam(':rg', $rg_aluno, PDO::PARAM_STR);
$stmt_fichas->execute();
$fichas = $stmt_fichas->fetchAll(PDO::FETCH_ASSOC);


?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Alunos</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/modal.css">
</head>
<body>
    <section class="bg-menu">
        <div class="conteudo" style="margin-left: -240px;">
            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page">
                                <b><i class="fas fa-user"></i>&nbsp; FICHA DE FREQUÊNCIA - ALUNO</b>
                            </h1>
                            <div class="container-right">
                                <div class="container-dados">
                                    
                                </div>
                                <a href="alunos.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main -->
            <main class="main-container">
                <div class="main-title">
                    <h2></h2>
                </div>
                <div class="conteudo-inner">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 mt-0 cadastro">
                                <a href="ficha.php?id=<?php echo $id_aluno; ?>" class="btn btn-white btn-sm active">
                                    <i class="fas fa-calendar-alt"></i> Ver ficha
                                </a>
                                &nbsp;&nbsp;
                                <a href="criarFicha.php?id=<?php echo $id_aluno; ?>" class="btn btn-white btn-sm">
                                    <i class="fas fa-plus"></i> Cadastrar Ficha
                                </a>
                                &nbsp;&nbsp;
                                <a href="vizualizarFicha.php?id=<?php echo $id_aluno; ?>" class="btn btn-white btn-sm">
                                    <i class="fas fa-eye"></i> Vizualizar Ficha
                                </a>
                            </div>    
                            <div class="col-12" id="categorias">
                                <div class="container-group mb-5">
                                    <div class="accordion" id="categoriasMenu">
                                        <div class="mt-5 card-table">
                                            <div class="card-drag" id="headingOne">
                                                <div class="infos">
                                                    <a href="#" class="name-table mb-0" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        <span class="me-2"><i class="fas fa-chalkboard-teacher"></i></span>
                                                        <b>Lista de Fichas</b>
                                                    </a>
                                                </div>
                                                <div class="infos"> 
                                                </div>
                                                <div class="infos">
                                                </div>
                                                <div class="infos">
                                                </div>
                                                
                                                <div class="infos">
                                                <a href="imprimirFicha.php?id=<?php echo $id_aluno; ?>" class="name-table mb-0 btn btn-sm btn-white active" >
                                                    <span class="me-2"><i class="fas fa-save"></i></span>
                                                    <b>Imprimir Ficha</b>
                                                </a>

                                                </div>
                                            </div>

                                            <div id="collapseOne" class="collapse show" data-parent="#categoriasMenu">
                                                <div class="lista-produtos" id="listaProdutos-one">
                                                    <table class="table mt-3 data-table">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Nome</th>
                                                                <th scope="col">Instrutor</th>
                                                                <th scope="col">Placa</th>
                                                                <th scope="col">Início</th>
                                                                <th class="col">Fim</th>
                                                                <th class="col">Data</th>
                                                                <th class="col">Status</th>
                                                                <th class="col">Ações</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                        <?php
                                                            if (!empty($fichas)) {
                                                                foreach ($fichas as $ficha) {
                                                                    echo "<tr>";
                                                                    echo "<td>" . htmlspecialchars($ficha['nome']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($ficha['instrutor']) . "</td>";
                                                                    echo "<td>" . htmlspecialchars($ficha['placa']) . "</td>";
                                                                    echo "<td>" . date('H:i', strtotime($ficha['horario_inicio'])) . "</td>";
                                                                    echo "<td>" . date('H:i', strtotime($ficha['horario_fim'])) . "</td>";
                                                                    echo "<td>" . (new DateTime($ficha['data_ficha']))->format('d/m/Y') . "</td>";
                                                                    echo "<td>" . htmlspecialchars($ficha['status']) . "</td>";
                                                                    echo "<td>";
                                                                    echo "<div class='actions'>";
                                                                    if ($ficha['status'] == 'Em Andamento') {
                                                                        echo "<a href='#' onclick='openModalFinalizarFicha({$ficha['id']})' class='icon-action'><i class='fas fa-check'></i></a>";
                                                                    }
                                                                    echo "<a href='editarFicha.php?id={$ficha['id']}' class='icon-action'><i class='fas fa-pencil-alt'></i></a>";
                                                                    echo "<a href='#' class='icon-action' onclick='openModal({$ficha['id']})'><i class='fas fa-trash-alt'></i></a>";
                                                                    echo "</div>";
                                                                    echo "</td>";
                                                                    echo "</tr>";
                                                                }
                                                            } else {
                                                                echo "<tr><td colspan='8'>Nenhuma ficha encontrada para o RG.</td></tr>";
                                                            }
                                                            ?>
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
            </main>

            
        

             <!-- Modal Excluir Ficha -->
             <div id="confirmExcluirFicha" class="modal-horarios" style="display: none;">
                <div class="modal-horarios-content">
                    <div class="modal-horarios-header">
                        <span class="close" onclick="closeModal()">&times;</span>
                        <h2 class="title-modal"><i class="fas fa-trash-alt"></i> Excluir Ficha</h2>
                    </div>
                    <p class="p mt-5">Tem certeza que deseja excluir esta ficha?</p>
                    <button class="excluir excluir-secondary" onclick="closeModal()">
                        <span class="text">Não</span>
                    </button>
                    <a id="confirmExcluirFichaLink" href="#" class="excluir">Sim</a>
                </div>
            </div>

               <!-- Modal Finalizar Ficha -->
               <div id="confirmFinalizarFicha" class="modal-horarios" style="display: none;">
                <div class="modal-horarios-content">
                    <div class="modal-horarios-header">
                        <span class="close" onclick="closeModalFinalizarFicha()">&times;</span>
                        <h2 class="title-modal"><i class="fas fa-check"></i> finalizar Ficha</h2>
                    </div>
                    <p class="p mt-5">Tem certeza que deseja finalizar esta ficha?</p>
                    <button class="excluir excluir-secondary" onclick="closeModalFinalizarFicha()">
                        <span class="text">Não</span>
                    </button>
                    <a id="confirmFinalizarFichaLink" href="#" class="excluir">Sim</a>
                </div>
            </div>

            <script src="../js/bootstrap.bundle.min.js"></script>
            <script src="../js/jquery.min.js"></script>
            <script src="../js/custom.js"></script>
            <script>
                
            
            function openModal(id) {
                document.getElementById('confirmExcluirFichaLink').href = 'excluirFicha.php?id=' + id;
                document.getElementById('confirmExcluirFicha').style.display = 'block';
            }

            function closeModal() {
                document.getElementById('confirmExcluirFicha').style.display = 'none';
            }

            function openModalFinalizarFicha(id) {
                document.getElementById('confirmFinalizarFichaLink').href = 'finalizarFicha.php?id=' + id;
                document.getElementById('confirmFinalizarFicha').style.display = 'block';
            }

            function closeModalFinalizarFicha() {
                document.getElementById('confirmFinalizarFicha').style.display = 'none';
            }

            </script>
        </body>
    </html>

