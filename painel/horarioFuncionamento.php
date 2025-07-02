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

require "conexao.php";

try {

    // Verifica se a sessão do usuário está ativa
    if (!isset($_SESSION['id_usuario'])) {
        throw new Exception("Usuário não autenticado. Faça login novamente.");
    }

    // Obtém o ID do usuário na sessão
    $id_usuario = $_SESSION['id_usuario'];

    // Consulta para obter o nome e o e-mail do usuário
    $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // Obtém os dados do usuário
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nome_usuario = htmlspecialchars($row['nome']);
        $email_usuario = htmlspecialchars($row['email']);
    } else {
        throw new Exception("Nenhum usuário encontrado com o ID fornecido.");
    }
} catch (PDOException $e) {
    // Exibe mensagens de erro de banco de dados
    die("Erro de conexão ou consulta: " . $e->getMessage());
} catch (Exception $e) {
    // Exibe mensagens de erro gerais
    die("Erro: " . $e->getMessage());
} finally {
    // Fecha explicitamente a conexão (opcional com PDO)
    $conn = null;
}

include "conexao.php";

// Verificar exclusão de horários
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);
    $query = "DELETE FROM horarios WHERE id = :delete_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':delete_id', $delete_id, PDO::PARAM_INT);

    try {
        $stmt->execute();
        echo "<script>alert('Horário excluído com sucesso!'); window.location.href='horarioFuncionamento.php';</script>";
    } catch (PDOException $e) {
        echo "Erro ao deletar horário: " . $e->getMessage();
    }
}

// Adicionar ou atualizar horários
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['horarios'])) {
    foreach ($_POST['horarios'] as $id => $horario) {
        $id = intval($id);
        $dia_inicio = intval($horario['dia_inicio']);
        $dia_fim = intval($horario['dia_fim']);
        $hora_inicio_1 = $horario['hora_inicio_1'];
        $hora_fim_1 = $horario['hora_fim_1'];
        $hora_inicio_2 = isset($horario['hora_inicio_2']) ? $horario['hora_inicio_2'] : null;
        $hora_fim_2 = isset($horario['hora_fim_2']) ? $horario['hora_fim_2'] : null;

        if ($id) {
            // Atualizar horário existente
            $query = "UPDATE horarios SET 
                        dia_inicio = :dia_inicio,
                        dia_fim = :dia_fim,
                        hora_inicio_1 = :hora_inicio_1,
                        hora_fim_1 = :hora_fim_1,
                        hora_inicio_2 = :hora_inicio_2,
                        hora_fim_2 = :hora_fim_2
                      WHERE id = :id";
            $stmt = $conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        } else {
            // Adicionar novo horário
            $query = "INSERT INTO horarios (dia_inicio, dia_fim, hora_inicio_1, hora_fim_1, hora_inicio_2, hora_fim_2) VALUES (
                        :dia_inicio, :dia_fim, :hora_inicio_1, :hora_fim_1, :hora_inicio_2, :hora_fim_2)";
            $stmt = $conn->prepare($query);
        }

        $stmt->bindParam(':dia_inicio', $dia_inicio, PDO::PARAM_INT);
        $stmt->bindParam(':dia_fim', $dia_fim, PDO::PARAM_INT);
        $stmt->bindParam(':hora_inicio_1', $hora_inicio_1, PDO::PARAM_STR);
        $stmt->bindParam(':hora_fim_1', $hora_fim_1, PDO::PARAM_STR);
        $stmt->bindParam(':hora_inicio_2', $hora_inicio_2, PDO::PARAM_STR);
        $stmt->bindParam(':hora_fim_2', $hora_fim_2, PDO::PARAM_STR);

        try {
            $stmt->execute();
        } catch (PDOException $e) {
            echo "Erro ao inserir ou atualizar horário: " . $e->getMessage();
        }
    }

    echo "<script>alert('Dados inseridos com sucesso.'); window.location.href='horarioFuncionamento.php';</script>";
}

// Buscar horários do banco de dados
try {
    $query = "SELECT * FROM horarios";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $horarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Erro ao buscar horários: " . $e->getMessage();
}

// Fechar conexão (opcional em PDO, mas recomendado explicitamente)
$conn = null;

require "conexao.php";

$id = 1; 
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);

$logoImage = $associacao['logo_image'] ?? '';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Empresa</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
 
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
                <a href="./home.php" class="menu-item"><i class="fas fa-home"></i> Início</a>
                <a href="./feriadosCadastrados.php" class="menu-item"><i class="fas fa-calendar-alt"></i> Feriados</a>
                <a href="./legislacao.php" class="menu-item"><i class="fas fa-book-open"></i> Legislação</a>
                <a href="./alunos.php" class="menu-item"><i class="fas fa-users"></i> Alunos</a>
                <a href="./instrutores.php" class="menu-item"><i class="fas fa-chalkboard-teacher"></i> Instrutores/Placa</a>
                <a href="./configuracoes.php" class="menu-item"><i class="fas fa-cog"></i> Configurações</a>
                <a href="./relatorio.php" class="menu-item"><i class="fas fa-donate"></i> Financeiro</a>
                <a href="./empresa.php" class="menu-item active"><i class="fas fa-building"></i> Empresa</a>
            </div>
        </div>

        <div class="conteudo">
            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page"><b><i class="fas fa-building"></i>&nbsp; CONFIGURAÇÕES DA EMPRESA</b></h1>
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

            <div class="conteudo-inner">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="menus-config">
                                <a href="empresa.php" class="btn btn-white btn-sm"><i class="fas fa-info-circle"></i> Sobre a empresa</a>
                                <a href="enderecoEmpresa.php" class="btn btn-white btn-sm"><i class="fas fa-map-marked-alt"></i> Endereço físico</a>
                                <a href="horarioFuncionamento.php" class="btn btn-white btn-sm active"><i class="fas fa-clock"></i> Horário de funcionamento</a>
                                <a href="listarcategorias.php" class="btn btn-white btn-sm "><i class="fas fa-car"></i> Categorias</a>
                            </div>
                        </div>

                        <div class="col-12 mt-5" id="horario">
                            <p class="title-categoria mb-0 mb-4"><i class="fas fa-building"></i>&nbsp;<b> Configure o horário em que sua Empresa estará disponível para atendimento.</b></p>

                            <form id="form-horarios" method="post" action="" >
                                <div id="horario-container">
                                    <?php foreach ($horarios as $row): ?>
                                        <div class="row horario mb-4" data-id="<?= htmlspecialchars($row['id']) ?>">
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <p class="title-categoria mb-0"><b>De:</b></p>
                                                    <select class="form-control input-sobre" name="horarios[<?= htmlspecialchars($row['id']) ?>][dia_inicio]">
                                                        <option value="-1">...</option>
                                                        <option value="0" <?= $row['dia_inicio'] == 0 ? 'selected' : '' ?>>Domingo</option>
                                                        <option value="1" <?= $row['dia_inicio'] == 1 ? 'selected' : '' ?>>Segunda</option>
                                                        <option value="2" <?= $row['dia_inicio'] == 2 ? 'selected' : '' ?>>Terça</option>
                                                        <option value="3" <?= $row['dia_inicio'] == 3 ? 'selected' : '' ?>>Quarta</option>
                                                        <option value="4" <?= $row['dia_inicio'] == 4 ? 'selected' : '' ?>>Quinta</option>
                                                        <option value="5" <?= $row['dia_inicio'] == 5 ? 'selected' : '' ?>>Sexta</option>
                                                        <option value="6" <?= $row['dia_inicio'] == 6 ? 'selected' : '' ?>>Sábado</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-2">
                                                <div class="form-group">
                                                    <p class="title-categoria mb-0"><b>até:</b></p>
                                                    <select class="form-control input-sobre" name="horarios[<?= htmlspecialchars($row['id']) ?>][dia_fim]">
                                                        <option value="-1">...</option>
                                                        <option value="0" <?= $row['dia_fim'] == 0 ? 'selected' : '' ?>>Domingo</option>
                                                        <option value="1" <?= $row['dia_fim'] == 1 ? 'selected' : '' ?>>Segunda</option>
                                                        <option value="2" <?= $row['dia_fim'] == 2 ? 'selected' : '' ?>>Terça</option>
                                                        <option value="3" <?= $row['dia_fim'] == 3 ? 'selected' : '' ?>>Quarta</option>
                                                        <option value="4" <?= $row['dia_fim'] == 4 ? 'selected' : '' ?>>Quinta</option>
                                                        <option value="5" <?= $row['dia_fim'] == 5 ? 'selected' : '' ?>>Sexta</option>
                                                        <option value="6" <?= $row['dia_fim'] == 6 ? 'selected' : '' ?>>Sábado</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-7">
                                                <div class="row">
                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <p class="title-categoria mb-0"><b>das:</b></p>
                                                            <input type="time" class="form-control input-sobre" name="horarios[<?= htmlspecialchars($row['id']) ?>][hora_inicio_1]" value="<?= htmlspecialchars($row['hora_inicio_1']) ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <p class="title-categoria mb-0"><b>até as:</b></p>
                                                            <input type="time" class="form-control input-sobre" name="horarios[<?= htmlspecialchars($row['id']) ?>][hora_fim_1]" value="<?= htmlspecialchars($row['hora_fim_1']) ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <p class="title-categoria mb-0"><b>e das:</b></p>
                                                            <input type="time" class="form-control input-sobre" name="horarios[<?= htmlspecialchars($row['id']) ?>][hora_inicio_2]" value="<?= htmlspecialchars($row['hora_inicio_2']) ?>" />
                                                        </div>
                                                    </div>
                                                    <div class="col-3">
                                                        <div class="form-group">
                                                            <p class="title-categoria mb-0"><b>até as:</b></p>
                                                            <input type="time" class="form-control input-sobre" name="horarios[<?= htmlspecialchars($row['id']) ?>][hora_fim_2]" value="<?= htmlspecialchars($row['hora_fim_2']) ?>" />
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-1 mt-4">
                                                <a href="#" class="btn btn-white btn-sm active" onclick="removeHorario(this, <?= htmlspecialchars($row['id']) ?>)">
                                                    <i class="fas fa-trash-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                                <div class="card card-select mt-5">
                                <div class="infos-produto-opcional">
                                        <button type="button" class="mb-0 color-primary novoHorario" onclick="addHorario()">
                                        <i class="fas fa-plus-circle"></i>&nbsp; Adicionar novo horário
                                    </button>
                                    </div>
                                </div>
                                <div class="row mt-0">
                                    <div class="col-12 text-right">
                                        <button type="submit" class="btn btn-sm mt-4 btn-white active">
                                             <i class="fas fa-check"></i>&nbsp;Salvar Alterações
                                        </button>
                                    </div>
                                </div>
                            </form>

                            <!-- Formulário de exclusão -->
                            <form id="form-delete" method="post" action="">
                                <input type="hidden" name="delete_id" id="delete_id">
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script src="../js/jquery.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/main.js"></script>
    <script>
        function removeHorario(button, id) {
            const row = button.closest('.row.horario');
            if (id.toString().startsWith('new_')) {
                row.remove();
            } else {
                document.getElementById('delete_id').value = id;
                document.getElementById('form-delete').submit();
            }
        }

        function addHorario() {
            const id = 'new_' + Date.now();
            const container = document.getElementById('horario-container');
            const row = document.createElement('div');
            row.classList.add('row', 'horario', 'mb-4');
            row.setAttribute('data-id', id);

            row.innerHTML = `
                <div class="col-2">
                    <div class="form-group">
                        <p class="title-categoria mb-0"><b>De:</b></p>
                        <select class="form-control input-sobre" name="horarios[${id}][dia_inicio]">
                            <option value="-1">...</option>
                            <option value="0">Domingo</option>
                            <option value="1">Segunda</option>
                            <option value="2">Terça</option>
                            <option value="3">Quarta</option>
                            <option value="4">Quinta</option>
                            <option value="5">Sexta</option>
                            <option value="6">Sábado</option>
                        </select>
                    </div>
                </div>
                <div class="col-2">
                    <div class="form-group">
                        <p class="title-categoria mb-0"><b>até:</b></p>
                        <select class="form-control input-sobre" name="horarios[${id}][dia_fim]">
                            <option value="-1">...</option>
                            <option value="0">Domingo</option>
                            <option value="1">Segunda</option>
                            <option value="2">Terça</option>
                            <option value="3">Quarta</option>
                            <option value="4">Quinta</option>
                            <option value="5">Sexta</option>
                            <option value="6">Sábado</option>
                        </select>
                    </div>
                </div>
                <div class="col-7">
                <div class="row">
                <div class="col-3">
                    <div class="form-group">
                        <p class="title-categoria mb-0"><b>de:</b></p>
                        <input type="time" class="form-control input-sobre" name="horarios[${id}][hora_inicio_1]" />
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <p class="title-categoria mb-0"><b>até as:</b></p>
                        <input type="time" class="form-control input-sobre" name="horarios[${id}][hora_fim_1]" />
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <p class="title-categoria mb-0"><b>e das:</b></p>
                        <input type="time" class="form-control input-sobre" name="horarios[${id}][hora_inicio_2]" />
                    </div>
                </div>
                <div class="col-3">
                    <div class="form-group">
                        <p class="title-categoria mb-0"><b>até as:</b></p>
                        <input type="time" class="form-control input-sobre" name="horarios[${id}][hora_fim_2]" />
                    </div>
                </div>
                </div>
                </div>
                <div class="col-1 d-flex align-items-center mt-3">
                    <a href="#" class="btn btn-white btn-sm active" onclick="removeHorario(this, '${id}')">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </div>
            `;

            container.appendChild(row);
        }
    </script>
</body>
</html>
