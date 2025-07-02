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

// Obtenha o CPF do aluno passado via GET
$cpf_aluno = isset($_GET['cpf']) ? $_GET['cpf'] : '';

// Inicializar variável de categoria
$categoria = '';

try {
    // Preparar a consulta para obter informações do aluno e seus serviços
    $query = "SELECT a.id, a.nome, a.rg, a.cpf, a.ladv, a.vencimento_processo, sa.servico, sa.categoria
              FROM alunos a
              LEFT JOIN servicos_aluno sa ON a.id = sa.id
              WHERE a.cpf = :cpf_aluno";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':cpf_aluno', $cpf_aluno, PDO::PARAM_STR);
    $stmt->execute();
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verifique se o aluno foi encontrado
    if (!$aluno) {
        die('Aluno não encontrado.');
    }

    // Obter o ID do aluno para uso posterior
    $id_aluno = $aluno['id'];

    // Formatar as datas no formato ISO para exibição ou manipulação
    $aluno['vencimento_processo'] = date('Y-m-d', strtotime($aluno['vencimento_processo']));
    $aluno['ladv'] = date('Y-m-d', strtotime($aluno['ladv']));

    // Buscar lista de instrutores
    $query_instrutores = "SELECT id, nome_instrutor FROM instrutores";
    $result_instrutores = $conn->query($query_instrutores);
    $instrutores = $result_instrutores->fetchAll(PDO::FETCH_ASSOC);

    // Buscar lista de placas associadas aos instrutores
    $query_placas = "SELECT id, placa_instrutor FROM instrutores";
    $result_placas = $conn->query($query_placas);
    $placas = $result_placas->fetchAll(PDO::FETCH_ASSOC);

    // Buscar lista de categorias disponíveis
    $query_categorias = "SELECT DISTINCT categoria FROM servicos_aluno";
    $result_categorias = $conn->query($query_categorias);
    $categorias = $result_categorias->fetchAll(PDO::FETCH_ASSOC);

    // Identificar o aluno atual em relação aos dados armazenados na sessão
    $current_id_aluno = $id_aluno; // ID do aluno sendo editado
    $stored_id_aluno = $_SESSION['form_data']['id_aluno'] ?? null; // ID armazenado na sessão

    // Verificar se o aluno atual é o mesmo que o armazenado
    $isSameAluno = $current_id_aluno == $stored_id_aluno;

} catch (PDOException $e) {
    die("Erro no banco de dados: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
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
</head>
<body>
<section class="bg-menu">
        <div class="conteudo" style="margin-left: -240px;">
            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page">
                                <b><i class="fas fa-user"></i>&nbsp; CADASTRAR FICHA - ALUNO</b>
                            </h1>
                            <div class="container-right">
                                <a href="alunos.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="main-container">
                <div class="main-title">
                    <h2></h2>
                </div>
                <div class="conteudo-inner">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 mt-0 cadastro">
                            <a href="ficha.php?cpf=<?php echo $cpf_aluno; ?>" class="btn btn-white btn-sm">
                                <i class="fas fa-calendar-alt"></i> Ver ficha
                            </a>
                            &nbsp;&nbsp;
                            <a href="criarFicha.php?cpf=<?php echo $cpf_aluno; ?>" class="btn btn-white btn-sm active">
                                <i class="fas fa-plus"></i> Cadastrar Ficha
                            </a>

                            </div>    
                            <div class="col-12 mt-5 tab-item" id="categoria">
                                <div class="col-12" id="categorias" style="zoom: 93%;">
                                    <form action="processarFichaAluno.php" method="POST" style="zoom: 93%;">
                                    <input type="hidden" name="id_aluno" value="<?= $id_aluno ?>" />
                                        <div class="container-group mb-5">
                                            <div class="col-12 mb-4 card card-form aluno">
                                                <div class="row">
                                                <div class="col-4 mb-2">
                                                        <div class="form-group container-cep mb-2">
                                                            <p class="title-categoria mb-1"><b>Nome do Aluno:</b></p>
                                                            <input type="text" name="nome" class="form-control mb-2" value="<?= htmlspecialchars($aluno['nome']) ?>" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>
                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep mb-2">
                                                            <p class="title-categoria mb-1"><b>RG:</b></p>
                                                            <input type="text" name="rg" class="form-control mb-2" value="<?= htmlspecialchars($aluno['rg']) ?>" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>
                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep mb-2">
                                                            <p class="title-categoria mb-1"><b>CPF:</b></p>
                                                            <input type="text" name="cpf" class="form-control mb-2" value="<?= htmlspecialchars($aluno['cpf']) ?>" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>
                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep mb-2">
                                                            <p class="title-categoria mb-1"><b>L.A.D.V:</b></p>
                                                            <input type="date" name="ladv" class="form-control mb-2" value="<?= (!empty($aluno['ladv']) && $aluno['ladv'] !== '0000-00-00' && $aluno['ladv'] !== null && $aluno['ladv'] !== '1970-01-01') ? htmlspecialchars($aluno['ladv']) : '' ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep mb-2">
                                                            <p class="title-categoria mb-1"><b>Vencimento Processo:</b></p>
                                                            <input type="date" name="vencimento_processo" class="form-control mb-2" value="<?= htmlspecialchars($aluno['vencimento_processo']) ?>" />
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep mb-2">
                                                            <p class="title-categoria mb-1"><b>Categoria:</b></p>
                                                            <select name="categoria" class="form-select form-control" aria-label="Default select example" id="pague">
                                                                <option value="">Selecione a categoria</option>
                                                                <option value="A" <?= $isSameAluno && $_SESSION['form_data']['categoria'] == 'A' ? 'selected' : ''; ?>>A</option>
                                                                <option value="B" <?= $isSameAluno && $_SESSION['form_data']['categoria'] == 'B' ? 'selected' : ''; ?>>B</option>
                                                                <option value="AB" <?= $isSameAluno && $_SESSION['form_data']['categoria'] == 'AB' ? 'selected' : ''; ?>>AB</option>
                                                                <option value="A/AB" <?= $isSameAluno && $_SESSION['form_data']['categoria'] == 'A/AB' ? 'selected' : ''; ?>>A/AB</option>
                                                                <option value="B/AB" <?= $isSameAluno && $_SESSION['form_data']['categoria'] == 'B/AB' ? 'selected' : ''; ?>>B/AB</option>
                                                                <option value="D" <?= $isSameAluno && $_SESSION['form_data']['categoria'] == 'D' ? 'selected' : ''; ?>>D</option>
                                                                <option value="A/D" <?= $isSameAluno && $_SESSION['form_data']['categoria'] == 'A/D' ? 'selected' : ''; ?>>A/D</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep mb-2">
                                                            <p class="title-categoria mb-1"><b>Placa:</b></p>
                                                            <select id="placa-select" name="placa" class="form-control" placeholder="Placa">
                                                                <option value="">Selecione a placa</option>
                                                                <?php foreach ($placas as $placa): ?>
                                                                    <option value="<?= htmlspecialchars($placa['placa_instrutor']) ?>" 
                                                                        <?= $isSameAluno && isset($_SESSION['form_data']['placa']) && $_SESSION['form_data']['placa'] == $placa['placa_instrutor'] ? 'selected' : ''; ?>>
                                                                        <?= htmlspecialchars($placa['placa_instrutor']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>

                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep mb-2">
                                                            <p class="title-categoria mb-1"><b>Instrutor:</b></p>
                                                            <select id="instrutor-select" name="instrutor" class="form-control" placeholder="Instrutor">
                                                                <option value="">Selecione o instrutor</option>
                                                                <?php foreach ($instrutores as $instrutor): ?>
                                                                    <option value="<?= htmlspecialchars($instrutor['nome_instrutor']) ?>" 
                                                                        <?= $isSameAluno && isset($_SESSION['form_data']['instrutor']) && $_SESSION['form_data']['instrutor'] == $instrutor['nome_instrutor'] ? 'selected' : ''; ?>>
                                                                        <?= htmlspecialchars($instrutor['nome_instrutor']) ?>
                                                                    </option>
                                                                <?php endforeach; ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">      
                                                        <div class="form-group container-cep mb-2">
                                                            <p class="title-categoria mb-1"><b>Registro:</b></p>
                                                            <input type="text" name="registro" class="form-control mb-2" 
                                                                oninput="this.value = this.value.toUpperCase()" 
                                                                value="<?= $isSameAluno ? ($_SESSION['form_data']['registro'] ?? '') : ''; ?>" 
                                                                placeholder="Registro"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Início:</b></p>
                                                            <input type="time" name="horario_inicio" class="form-control mb-2" 
                                                                value="<?= $isSameAluno ? ($_SESSION['form_data']['horario_inicio'] ?? '00:00') : '00:00'; ?>" 
                                                                placeholder="Horário Início"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Fim:</b></p>
                                                            <input type="time" name="horario_fim" class="form-control mb-2" 
                                                                value="<?= $isSameAluno ? ($_SESSION['form_data']['horario_fim'] ?? '00:00') : '00:00'; ?>" 
                                                                placeholder="Horário Fim"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Data:</b></p>
                                                            <input type="date" name="data_ficha" class="form-control mb-2" 
                                                                oninput="this.value = this.value.toUpperCase()" 
                                                                value="<?= $isSameAluno ? ($_SESSION['form_data']['data_ficha'] ?? '') : ''; ?>" 
                                                                placeholder="Data Ficha"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 text-right">
                                                        <button type="submit" class="btn btn-yellow btn-sm mt-4 btn-proximo" style="float:right;">
                                                            <i class="fas fa-check"></i> &nbsp; Finalizar
                                                        </button>
                                                    </div>
                                                </div>
                                                </div>
                                                <input type="hidden" name="id_aluno" value="<?= $id_aluno ?>" />
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </section>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    document.getElementById('placa-select').addEventListener('change', function() {
        var placa = this.value;
        if (placa) {
            fetch('buscarInsturtorPlaca.php?type=placa&value=' + encodeURIComponent(placa))
                .then(response => response.json())
                .then(data => {
                    var instrutorSelect = document.getElementById('instrutor-select');
                    instrutorSelect.value = data.instrutor;
                })
                .catch(error => console.error('Erro:', error));
        }
    });

    document.getElementById('instrutor-select').addEventListener('change', function() {
        var instrutor = this.value;
        if (instrutor) {
            fetch('buscarInsturtorPlaca.php?type=instrutor&value=' + encodeURIComponent(instrutor))
                .then(response => response.json())
                .then(data => {
                    var placaSelect = document.getElementById('placa-select');
                    placaSelect.value = data.placa;
                })
                .catch(error => console.error('Erro:', error));
        }
    });
</script>

</body>
</html>