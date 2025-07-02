<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador, presidente ou suporte
function verificarAcesso() {
    if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel']; // Obtém o nível do usuário

        // Verificar se o nível de usuário é admin, presidente ou suporte
        if(in_array($nivel_usuario, ['admin', 'presidente', 'suporte'])) {
            return true;
        }
    }
    
    // Se o usuário não estiver logado com acesso adequado, redireciona para a página de login
    header("Location: loader.php");
    exit(); // Encerra o script após o redirecionamento
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();


// Incluir a conexão com o banco de dados
include 'conexao.php';

// Verifica se há uma pesquisa armazenada no GET ou na sessão
$pesquisa = $_GET['pesquisa'] ?? $_SESSION['pesquisa'] ?? '';
$_SESSION['pesquisa'] = $pesquisa; // Armazena a pesquisa na sessão

// Consulta para buscar alunos, com ou sem filtro
$query = !empty($pesquisa) 
    ? "SELECT * FROM alunos WHERE nome LIKE :pesquisa OR cpf LIKE :pesquisa"
    : "SELECT * FROM alunos";

// Prepara a consulta
$stmt = $conn->prepare($query);

if (!empty($pesquisa)) {
    $stmt->bindValue(':pesquisa', "%$pesquisa%");
}

// Executa a consulta
$stmt->execute();

// Obtém os resultados
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Verificar se o ID da turma foi passado via GET
if (isset($_GET['id_turma'])) {
    $id_turma = $_GET['id_turma']; // Recebe o ID da turma via URL

    // Obter logo da associação (exemplo de uso do ID)
    $sql = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_turma, PDO::PARAM_INT);
    $stmt->execute();
    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($associacao) {
        $logoImage = $associacao['logo_image'] ?? '';
    } else {
        // Definir um valor padrão ou tratar o erro se necessário
        $logoImage = '';
    }
} else {
    echo "<script>alert('ID do Aluno não foi fornecido.'); window.location.href='insirirAlunos.php';</script>";

}
$conn = null;
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Legislação</title>

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
                                <div class="container-dados"></div>

                                <a href="legislacao.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>


            <section class="conteudo-inner" style="margin-top: 15px;">
                <div class="container">
                    <div class="row">
                        <div class="row">
                            <div class="col-md-12 mb-0">
                                <div class="card-search">
                                    <div class="card-body">
                                        <form method="GET" class="row g-3 align-items-center">
                                            <input type="hidden" name="id_turma" value="<?php echo htmlspecialchars($id_turma); ?>">
                                            <div class="col-md-4">
                                                <div class="container-cep">
                                                    <input class="form-control" type="text" name="pesquisa" value="<?php echo htmlspecialchars($pesquisa); ?>" placeholder="Pesquisar alunos...">
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
                                <form id="formAssociado" action="processarAlunoTurma.php" method="POST" style="zoom: 120%;">
                                    <input type="hidden" name="id_turma" value="<?php echo htmlspecialchars($id_turma); ?>">
                                    <div class="container-group mb-5">
                                        <div class="col-12 mb-4 card card-form socio">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="card-table table-responsive">
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
                                                                        if (!empty($result)) {
                                                                            foreach ($result as $row) {
                                                                                echo "<tr>";
                                                                                echo "<td><input name='aluno_turma[]' class='checkbox' type='checkbox' value='{$row['id']}' data-nome='" . htmlspecialchars($row['nome']) . "' data-cpf='" . htmlspecialchars($row['cpf']) . "'></td>";
                                                                                echo "<td>" . htmlspecialchars($row['nome']) . "</td>";
                                                                                echo "<td>" . htmlspecialchars($row['cpf']) . "</td>";
                                                                                echo "</tr>";
                                                                            }
                                                                        } else {
                                                                            echo "<tr><td colspan='3'>Nenhum aluno encontrado.</td></tr>";
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
                                                        <div class="card-drag" id="headingTwo">
                                                            <div class="infos">
                                                                <p class="name-table mb-3">
                                                                    <span class="me-2"><i class="fas fa-user"></i></span>
                                                                    <b>Alunos Selecionados</b>
                                                                </p>
                                                            </div>
                                                        </div>
                                                        <div id="collapseTwo">
                                                            <div class="card-body">
                                                                <table class="table">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Nome</th>
                                                                            <th>CPF</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody id="alunoSelecionado"></tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 mb-0">
                                                <button type="submit" class="btn btn-yellow btn-sm mt-4 col-2 btn-proximo">
                                                    <i class="fas fa-save"></i>&nbsp; Salvar Alunos
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
    <script src="./js/painel.js"></script>
    <script src="./js/pesquisa.js"></script>
    <script>
        $(document).ready(function() {

            // Atualizar a lista de alunos selecionados quando os checkboxes forem marcados/desmarcados
            $('.checkbox').on('change', function() {
                let aluno = $(this).data('nome');
                let cpf = $(this).data('cpf');
                let alunoId = $(this).val();
                
                // Recupera o array de IDs selecionados do localStorage
                let selecionados = JSON.parse(localStorage.getItem('alunosSelecionados')) || [];
                
                if ($(this).is(':checked')) {
                    // Adiciona o ID ao array e atualiza o localStorage
                    selecionados.push(alunoId);
                    localStorage.setItem('alunosSelecionados', JSON.stringify(selecionados));
                    $('#alunoSelecionado').append(`<tr data-id="${alunoId}"><td>${aluno}</td><td>${cpf}</td></tr>`);
                } else {
                    // Remove o ID do array e atualiza o localStorage
                    selecionados = selecionados.filter(id => id !== alunoId);
                    localStorage.setItem('alunosSelecionados', JSON.stringify(selecionados));
                    $(`#alunoSelecionado tr[data-id="${alunoId}"]`).remove();
                }
            });

        // Carregar o estado dos checkboxes quando a página é carregada
        function carregarEstadoCheckboxes() {
            // Recupera o array de IDs selecionados do localStorage
            let selecionados = JSON.parse(localStorage.getItem('alunosSelecionados')) || [];
            
            // Marca os checkboxes que estão no array de IDs selecionados
            $('.checkbox').each(function() {
                let alunoId = $(this).val();
                if (selecionados.includes(alunoId)) {
                    $(this).prop('checked', true);
                    let aluno = $(this).data('nome');
                    let cpf = $(this).data('cpf');
                    $('#alunoSelecionado').append(`<tr data-id="${alunoId}"><td>${aluno}</td><td>${cpf}</td></tr>`);
                }
            });
        }

        carregarEstadoCheckboxes();
        });


    function redirecionarSeRecarregar() {
        // Verifica se a página foi recarregada
        if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
            // Obtém os parâmetros da URL
            const urlParams = new URLSearchParams(window.location.search);
            const idTurma = urlParams.get('id_turma');

            // Verifica se o ID da turma está presente
            if (idTurma) {
                // Remove o valor do parâmetro de pesquisa
                urlParams.set('pesquisa', '');

                // Redireciona para a mesma página com o ID da turma e pesquisa esvaziada
                window.location.href = window.location.pathname + "?id_turma=" + encodeURIComponent(idTurma) + "&" + urlParams.toString();
            } else {
                console.error("ID da turma não foi fornecido.");
            }
        }
    }

    window.onload = redirecionarSeRecarregar;

    </script>
</body>
</html>
