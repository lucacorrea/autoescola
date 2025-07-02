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

include "conexao.php";

$id = 1; 
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);


$logoImage = $associacao['logo_image'];

include "conexao.php";

// Obtenha o ID da ficha (passado via GET)
$id_ficha = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_ficha === 0) {
    die('ID da ficha inválido.');
}

try {
    // Obter o ID do aluno
    $query_aluno = "SELECT a.id FROM alunos a INNER JOIN fichas f ON a.rg = f.rg WHERE f.id = :id_ficha";
    $stmt_aluno = $conn->prepare($query_aluno);
    $stmt_aluno->bindParam(':id_ficha', $id_ficha, PDO::PARAM_INT);
    $stmt_aluno->execute();
    $id_aluno = $stmt_aluno->fetchColumn();

    if (!$id_aluno) {
        die('Aluno não encontrado para a ficha fornecida.');
    }

    // Obter os detalhes da ficha
    $query_ficha = "SELECT * FROM fichas WHERE id = :id_ficha";
    $stmt_ficha = $conn->prepare($query_ficha);
    $stmt_ficha->bindParam(':id_ficha', $id_ficha, PDO::PARAM_INT);
    $stmt_ficha->execute();
    $ficha = $stmt_ficha->fetch(PDO::FETCH_ASSOC);

    if (!$ficha) {
        die('Ficha não encontrada.');
    }

    // Formatar as datas no formato ISO para campos de data
    $ficha['ladv'] = date('Y-m-d', strtotime($ficha['ladv']));
    $ficha['vencimento_processo'] = date('Y-m-d', strtotime($ficha['vencimento_processo']));
    $ficha['data_ficha'] = date('Y-m-d', strtotime($ficha['data_ficha']));


} catch (PDOException $e) {
    die("Erro ao executar a consulta: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Editar Ficha</title>
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
                                <b><i class="fas fa-calendar-alt"></i>&nbsp; EDITAR FICHA</b>
                            </h1>
                            <div class="container-right">
                                <a href="ficha.php?id=<?php echo $id_aluno; ?>" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Voltar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main class="main-container">
                <div class="main-title">
                </div>
                <div class="conteudo-inner">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 mt-0 cadastro">
                                <div class="col-12 mt-3 tab-item" id="categoria">
                                    <div class="col-12" id="categorias" style="zoom: 93%;">
                                        <form action="updateFichaAluno.php" method="POST" style="zoom: 93%;">
                                            <input type="hidden" name="id_ficha" value="<?= $id_ficha ?>" />
                                            <div class="container-group mb-5">
                                                <div class="col-12 mb-4 card card-form aluno">
                                                    <div class="row">
                                                        <!-- Campos do formulário -->
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Status:</b></p>
                                                                <input type="text" name="status" class="form-control mb-2" value="<?= htmlspecialchars($ficha['status']) ?>" />
                                                            </div>
                                                        </div>
                                                
                                                        <div class="col-4 mb-2">
                                                        </div>

                                                        <div class="col-4 mb-2">
                                                        </div>

                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Nome:</b></p>
                                                                <input type="text" name="nome" class="form-control mb-2" value="<?= htmlspecialchars($ficha['nome']) ?>" oninput="this.value = this.value.toUpperCase()" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>RG:</b></p>
                                                                <input type="text" name="rg" class="form-control mb-2" value="<?= htmlspecialchars($ficha['rg']) ?>" oninput="this.value = this.value.toUpperCase()" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>CPF:</b></p>
                                                                <input type="text" name="cpf" class="form-control mb-2" value="<?= htmlspecialchars($ficha['cpf']) ?>" oninput="this.value = this.value.toUpperCase()" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>L.A.D.V:</b></p>
                                                                <input type="date" name="ladv" class="form-control mb-2" value="<?= htmlspecialchars($ficha['ladv']) ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Vencimento Processo:</b></p>
                                                                <input type="date" name="vencimento_processo" class="form-control mb-2" value="<?= htmlspecialchars($ficha['vencimento_processo']) ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Categoria:</b></p>
                                                                <select name="categoria" class="form-control">
                                                                    <option selected value="">Selecione o Serviço:</option>
                                                                    <option value="A" <?php echo htmlspecialchars($ficha['categoria']) == 'A' ? 'selected' : ''; ?>>A</option>
                                                                    <option value="B" <?php echo htmlspecialchars($ficha['categoria']) == 'B' ? 'selected' : ''; ?>>B</option>
                                                                    <option value="AB" <?php echo htmlspecialchars($ficha['categoria']) == 'AB' ? 'selected' : ''; ?>>AB</option>
                                                                    <option value="A/AB" <?php echo htmlspecialchars($ficha['categoria']) == 'A/AB' ? 'selected' : ''; ?>>A/AB</option>
                                                                    <option value="B/AB" <?php echo htmlspecialchars($ficha['categoria']) == 'B/AB' ? 'selected' : ''; ?>>B/AB</option>
                                                                    <option value="D" <?php echo htmlspecialchars($ficha['categoria']) == 'D' ? 'selected' : ''; ?>>D</option>
                                                                    <option value="A/D" <?php echo htmlspecialchars($ficha['categoria']) == 'A/D' ? 'selected' : ''; ?>>A/D</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Instrutor:</b></p>
                                                                <input type="text" name="instrutor" class="form-control mb-2" value="<?= htmlspecialchars($ficha['instrutor']) ?>" oninput="this.value = this.value.toUpperCase()" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Placa:</b></p>
                                                                <input type="text" name="placa" class="form-control mb-2" value="<?= htmlspecialchars($ficha['placa']) ?>" oninput="this.value = this.value.toUpperCase()" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Registro:</b></p>
                                                                <input type="text" name="registro" class="form-control mb-2" value="<?= htmlspecialchars($ficha['registro']) ?>" oninput="this.value = this.value.toUpperCase()" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Início:</b></p>
                                                                <input type="time" name="horario_inicio" class="form-control mb-2" value="<?= htmlspecialchars($ficha['horario_inicio']) ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Fim:</b></p>
                                                                <input type="time" name="horario_fim" class="form-control mb-2" value="<?= htmlspecialchars($ficha['horario_fim']) ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-4 mb-2">
                                                            <div class="form-group container-cep mb-2">
                                                                <p class="title-categoria mb-1"><b>Data da Ficha:</b></p>
                                                                <input type="date" name="data_ficha" class="form-control mb-2" value="<?= htmlspecialchars($ficha['data_ficha']) ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="col-12 text-right">
                                                            <button type="submit" class="btn btn-yellow btn-sm mt-4 btn-proximo" style="float:right;">
                                                                <i class="fas fa-check"></i> &nbsp; Atualizar
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
                    </div>
                </div>
            </main>
        </div>
</section>
<script src="../js/bootstrap.bundle.min.js"></script>
</body>
</html>
