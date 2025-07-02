<?php
session_start(); // Iniciar a sessão

// Função para verificar se o usuário está logado (admin ou presidente)
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel'];
        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true;
        }
    }
    header("Location: loader.php");
    exit();
}

verificarAcesso();

// Conectar ao banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$database = "autoescola";
$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Obter os dados do formulário
$nome = $_POST['nome'] ?? '';
$rg = $_POST['rg'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$ladv = isset($_POST['ladv']) ? date('Y-m-d', strtotime($_POST['ladv'])) : '';
$vencimento_processo = isset($_POST['vencimento_processo']) ? date('Y-m-d', strtotime($_POST['vencimento_processo'])) : '';
$categoria = $_POST['categoria'] ?? '';
$instrutor = $_POST['instrutor'] ?? '';
$placa = $_POST['placa'] ?? '';
$registro = $_POST['registro'] ?? '';
$horario_inicio = $_POST['horario_inicio'] ?? '';
$horario_fim = $_POST['horario_fim'] ?? '';
$data_ficha = isset($_POST['data_ficha']) ? date('Y-m-d', strtotime($_POST['data_ficha'])) : '';
$id_aluno = isset($_POST['id_aluno']) ? (int)$_POST['id_aluno'] : 0;

// Armazena os dados do formulário na sessão para reutilização
$_SESSION['form_data'] = [
    'nome' => $nome,
    'rg' => $rg,
    'cpf' => $cpf,
    'ladv' => $ladv,
    'vencimento_processo' => $vencimento_processo,
    'categoria' => $categoria,
    'instrutor' => $instrutor,
    'placa' => $placa,
    'registro' => $registro,
    'horario_inicio' => $horario_inicio,
    'horario_fim' => $horario_fim,
    'data_ficha' => $data_ficha,
    'id_aluno' => $id_aluno
];

// Chama a função de verificação de aulas apenas para as categorias permitidas
if (in_array($categoria, ['A', 'B', 'AB', 'D'])) {
    if (!verificarCategorias($conn, $categoria, $rg, $_POST['horario_inicio'], $_POST['horario_fim'])) {
        $conn->close();
        echo "<script>window.location.href='criarFicha.php?id={$id_aluno}';</script>";
        exit();
    }
}

function verificarCategorias($conn, $categoria, $rg, $horario_inicio, $horario_fim) {
    // Calcular a duração da nova aula em segundos
    $inicio = strtotime($horario_inicio);
    $fim = strtotime($horario_fim);
    $duracao_segundos = $fim - $inicio;

    if ($duracao_segundos <= 0) {
        echo "<script>alert('Horário de fim deve ser maior que o horário de início.');</script>";
        return false;
    }

    // Verificar quantas aulas já existem para essa categoria e RG
    $query = "SELECT COUNT(*) AS total_aulas FROM fichas WHERE categoria = ? AND rg = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Erro ao preparar a consulta: " . $conn->error . "');</script>";
        return false;
    }

    $stmt->bind_param('ss', $categoria, $rg);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    $total_aulas = $row['total_aulas'] ?? 0;

    // Considerar que cada aula conta como 2h DETRAN
    $total_horas_detran = $total_aulas * 2;

    // Se já atingiu ou ultrapassou as 20h DETRAN (10 aulas), não permite nova ficha
    if ($total_horas_detran >= 20) {
        echo "<script>alert('O aluno já completou as 20 horas exigidas para a categoria {$categoria}.');</script>";
        return false;
    }

    // Verificar se a nova aula excederia o limite (mais que 10 aulas)
    if ($total_aulas + 1 > 10) {
        echo "<script>alert('Esta aula excederia o limite de 10 aulas (20 horas DETRAN) para a categoria {$categoria}.');</script>";
        return false;
    }

    return true;
}


// Chama a função de verificação de aulas apenas para as categorias permitidas
if (in_array($categoria, ['A/AB', 'B/AB', 'A/D'])) {
    if (!verificarAulas($conn, $categoria, $rg, $_POST['horario_inicio'], $_POST['horario_fim'])) {
        $conn->close();
        echo "<script>window.location.href='criarFicha.php?id={$id_aluno}';</script>";
        exit();
    }
}

function verificarAulas($conn, $categoria, $rg, $horario_inicio, $horario_fim) {
    // Calcular a duração da nova aula em segundos
    $inicio = strtotime($horario_inicio);
    $fim = strtotime($horario_fim);
    $duracao_segundos = $fim - $inicio;

    if ($duracao_segundos <= 0) {
        echo "<script>alert('Horário de fim deve ser maior que o horário de início.');</script>";
        return false;
    }

    // Verificar quantas aulas já existem para essa categoria e RG
    $query = "SELECT COUNT(*) AS total_aulas FROM fichas WHERE categoria = ? AND rg = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Erro ao preparar a consulta: " . $conn->error . "');</script>";
        return false;
    }

    $stmt->bind_param('ss', $categoria, $rg);
    $stmt->execute();
    $result = $stmt->get_result();

    $row = $result->fetch_assoc();
    $total_aulas = $row['total_aulas'] ?? 0;

    // Cada aula vale 2h DETRAN
    $total_horas_detran = $total_aulas * 2;

    // Se já atingiu ou ultrapassou as 15h DETRAN (7.5 aulas), bloquear
    if ($total_horas_detran >= 15) {
        echo "<script>alert('O aluno já completou as 15 horas exigidas para a categoria {$categoria}.');</script>";
        return false;
    }

    // Se com a nova aula ultrapassar o limite (mais de 7.5 aulas)
    if ($total_aulas + 1 > 7) {
        echo "<script>alert('Esta aula excederia o limite de 15 horas (7 aulas de 2h) para a categoria {$categoria}.');</script>";
        return false;
    }

    return true;
}


// Verifica se a data selecionada é um domingo
if (date('w', strtotime($data_ficha)) == 0) { // 0 representa domingo
    $conn->close();
    echo "<script>alert('A data selecionada cai em um domingo. Não é possível cadastrar a ficha.'); window.location.href='criarFicha.php?id={$id_aluno}';</script>";
    exit();
}

// Verificar se a data_ficha é um feriado
$query_feriado = "SELECT COUNT(*) AS count FROM feriados WHERE data = ?";
$stmt = $conn->prepare($query_feriado);
$stmt->bind_param('s', $data_ficha);
$stmt->execute();
$result_feriado = $stmt->get_result();
$feriado = $result_feriado->fetch_assoc();
$stmt->close();

if ($feriado['count'] > 0) {
    $conn->close();
    echo "<script>alert('A data informada é um feriado. Não é possível cadastrar a ficha.'); window.location.href='criarFicha.php?id={$id_aluno}';</script>";
    exit();
}

// Verificar se já existe um registro com o mesmo instrutor, placa, data e intervalo de horário que se sobrepõe
$query_verifica = "SELECT id FROM fichas 
                   WHERE instrutor = ? 
                   AND placa = ? 
                   AND data_ficha = ?
                   AND (
                       (horario_inicio <= ? AND horario_fim > ?) OR
                       (horario_inicio < ? AND horario_fim >= ?) OR
                       (horario_inicio >= ? AND horario_fim <= ?)
                   )";

$stmt = $conn->prepare($query_verifica);
$stmt->bind_param('sssssssss', $instrutor, $placa, $data_ficha, $horario_inicio, $horario_inicio, $horario_fim, $horario_fim, $horario_inicio, $horario_fim);
$stmt->execute();
$result_verifica = $stmt->get_result();
$verifica = $result_verifica->fetch_assoc();
$stmt->close();

if ($verifica) {
    $conn->close();
    echo "<script>alert('Já existe uma ficha cadastrada com o mesmo instrutor, placa, horário e data que entra em conflito com este horário.'); window.location.href='criarFicha.php?id={$id_aluno}';</script>";
    exit();
}

// Verificar se já existe uma ficha para o mesmo CPF na mesma data com horário sobreposto (qualquer categoria, instrutor ou placa)
$query_cpf = "SELECT id FROM fichas 
              WHERE cpf = ?
              AND data_ficha = ?
              AND (
                  (horario_inicio <= ? AND horario_fim > ?) OR
                  (horario_inicio < ? AND horario_fim >= ?) OR
                  (horario_inicio >= ? AND horario_fim <= ?)
              )";

$stmt = $conn->prepare($query_cpf);
$stmt->bind_param('ssssssss', $cpf, $data_ficha, $horario_inicio, $horario_inicio, $horario_fim, $horario_fim, $horario_inicio, $horario_fim);
$stmt->execute();
$result_cpf = $stmt->get_result();
$ficha_existente = $result_cpf->fetch_assoc();
$stmt->close();

if ($ficha_existente) {
    $conn->close();
    echo "<script>alert('Este aluno já possui uma ficha cadastrada com data e horário que entra em conflito. Cadastro não permitido.'); window.location.href='criarFicha.php?id={$id_aluno}';</script>";
    exit();
}


// Verificar se já existe um registro com o mesmo instrutor, placa, horário e data
$query_verifica = "SELECT id FROM fichas WHERE instrutor = ? AND placa = ? AND horario_inicio = ? AND horario_fim = ? AND data_ficha = ?";
$stmt = $conn->prepare($query_verifica);
$stmt->bind_param('sssss', $instrutor, $placa, $horario_inicio, $horario_fim, $data_ficha);
$stmt->execute();
$result_verifica = $stmt->get_result();
$verifica = $result_verifica->fetch_assoc();
$stmt->close();

if ($verifica) {
    $conn->close();
    echo "<script>alert('Já existe uma ficha cadastrada com o mesmo instrutor, placa, horário e data.'); window.location.href='criarFicha.php?id={$id_aluno}';</script>";
    exit();
}

// Verificar se já existe um registro com a mesma placa, data_ficha, horário_inicio e horário_fim (independentemente do instrutor)
$query_verifica_placa = "SELECT id FROM fichas WHERE placa = ? AND horario_inicio = ? AND horario_fim = ? AND data_ficha = ?";
$stmt = $conn->prepare($query_verifica_placa);
$stmt->bind_param('ssss', $placa, $horario_inicio, $horario_fim, $data_ficha);
$stmt->execute();
$result_verifica_placa = $stmt->get_result();
$verifica_placa = $result_verifica_placa->fetch_assoc();
$stmt->close();

if ($verifica_placa) {
    $conn->close();
    echo "<script>alert('Já existe uma ficha cadastrada com a mesma placa, horário e data com outro instrutor ou aluno.'); window.location.href='criarFicha.php?id={$id_aluno}';</script>";
    exit();
}

// Verificar se já existe um registro com o mesmo instrutor, na mesma data e horário
$query_verifica_instrutor_horario = "SELECT id FROM fichas WHERE instrutor = ? AND data_ficha = ? AND (
    (horario_inicio <= ? AND horario_fim >= ?) OR
    (horario_inicio <= ? AND horario_fim >= ?)
)";
$stmt = $conn->prepare($query_verifica_instrutor_horario);
$stmt->bind_param('ssssss', $instrutor, $data_ficha, $horario_inicio, $horario_inicio, $horario_fim, $horario_fim);
$stmt->execute();
$result_verifica_instrutor = $stmt->get_result();
$verifica_instrutor_horario = $result_verifica_instrutor->fetch_assoc();
$stmt->close();

if ($verifica_instrutor_horario) {
    $conn->close();
    echo "<script>alert('Conflito de horário: O instrutor já tem uma ficha cadastrada para esse horário e data.'); window.location.href='criarFicha.php?id={$id_aluno}';</script>";
    exit();
}

// Inserir a ficha
$query_inserir = "INSERT INTO fichas (nome, rg, cpf, ladv, vencimento_processo, categoria, instrutor, placa, registro, horario_inicio, horario_fim, data_ficha, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Em Andamento')";
$stmt = $conn->prepare($query_inserir);
$stmt->bind_param('ssssssssssss', $nome, $rg, $cpf, $ladv, $vencimento_processo, $categoria, $instrutor, $placa, $registro, $horario_inicio, $horario_fim, $data_ficha);

if ($stmt->execute()) {
    echo "<script>alert('Ficha cadastrada com sucesso.'); window.location.href='criarFicha.php?id={$id_aluno}';</script>";
} else {
    echo "<script>alert('Erro ao cadastrar ficha: " . $stmt->error . "'); window.location.href='criarFicha.php?id={$id_aluno}';</script>";
}

$stmt->close();
$conn->close();
?>