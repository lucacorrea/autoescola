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

// Função para calcular horas DETRAN (arredonda para baixo a cada 1 hora completa)
function calcularHorasDetran($horario_inicio, $horario_fim) {
    $inicio = new DateTime($horario_inicio);
    $fim = new DateTime($horario_fim);
    $interval = $inicio->diff($fim);
    
    // Calcula o total de horas
    $horas = $interval->h;
    $minutos = $interval->i;
    
    // Se tiver pelo menos 1 hora completa, conta como 1 hora DETRAN
    // Se tiver 1 hora e 40 minutos (como 17:00-18:40), conta como 2 horas DETRAN
    if ($horas >= 1 && $minutos >= 40) {
        return 2;
    } elseif ($horas >= 1) {
        return 1;
    } else {
        return 0; // Menos de 1 hora não conta
    }
}

// Chama a função de verificação de aulas apenas para as categorias permitidas
if (in_array($categoria, ['A', 'B', 'AB', 'D'])) {
    if (!verificarCategorias($conn, $categoria, $rg, $_POST['horario_inicio'], $_POST['horario_fim'])) {
        $conn->close();
        echo "<script>alert('Erro na verificação de horas para esta categoria.'); history.back();</script>";
        exit();
    }
}

function verificarCategorias($conn, $categoria, $rg, $horario_inicio, $horario_fim) {
    // Calcular horas DETRAN para esta aula
    $horas_detran = calcularHorasDetran($horario_inicio, $horario_fim);
    
    if ($horas_detran <= 0) {
        echo "<script>alert('A aula deve ter pelo menos 1 hora completa para ser contabilizada.');</script>";
        return false;
    }

    // Verificar total de horas DETRAN já cadastradas para esta categoria e RG
    $query = "SELECT horario_inicio, horario_fim FROM fichas WHERE categoria = ? AND rg = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Erro ao preparar a consulta: " . $conn->error . "');</script>";
        return false;
    }

    $stmt->bind_param('ss', $categoria, $rg);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_horas_detran = 0;
    while ($row = $result->fetch_assoc()) {
        $total_horas_detran += calcularHorasDetran($row['horario_inicio'], $row['horario_fim']);
    }

    // Se já atingiu ou ultrapassou as 20h DETRAN, não permite nova ficha
    if ($total_horas_detran >= 20) {
        echo "<script>alert('O aluno já completou as 20 horas exigidas para a categoria {$categoria}.');</script>";
        return false;
    }

    // Verificar se a nova aula excederia o limite
    $nova_horas = $total_horas_detran + calcularHorasDetran($horario_inicio, $horario_fim);
    if ($nova_horas > 20) {
        echo "<script>alert('Esta aula excederia o limite de 20 horas DETRAN para a categoria {$categoria}.');</script>";
        return false;
    }

    return true;
}

// Chama a função de verificação de aulas para categorias A/AB, B/AB, A/D
if (in_array($categoria, ['A/AB', 'B/AB', 'A/D'])) {
    if (!verificarAulas($conn, $categoria, $rg, $_POST['horario_inicio'], $_POST['horario_fim'])) {
        $conn->close();
        echo "<script>alert('Erro na verificação de horas para esta categoria.'); history.back();</script>";
        exit();
    }
}

function verificarAulas($conn, $categoria, $rg, $horario_inicio, $horario_fim) {
    // Calcular horas DETRAN para esta aula
    $horas_detran = calcularHorasDetran($horario_inicio, $horario_fim);
    
    if ($horas_detran <= 0) {
        echo "<script>alert('A aula deve ter pelo menos 1 hora completa para ser contabilizada.');</script>";
        return false;
    }

    // Verificar total de horas DETRAN já cadastradas para esta categoria e RG
    $query = "SELECT horario_inicio, horario_fim FROM fichas WHERE categoria = ? AND rg = ?";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Erro ao preparar a consulta: " . $conn->error . "');</script>";
        return false;
    }

    $stmt->bind_param('ss', $categoria, $rg);
    $stmt->execute();
    $result = $stmt->get_result();

    $total_horas_detran = 0;
    while ($row = $result->fetch_assoc()) {
        $total_horas_detran += calcularHorasDetran($row['horario_inicio'], $row['horario_fim']);
    }

    // Se já atingiu ou ultrapassou as 15h DETRAN, bloquear
    if ($total_horas_detran >= 15) {
        echo "<script>alert('O aluno já completou as 15 horas exigidas para a categoria {$categoria}.');</script>";
        return false;
    }

    // Verificar se a nova aula excederia o limite
    $nova_horas = $total_horas_detran + calcularHorasDetran($horario_inicio, $horario_fim);
    if ($nova_horas > 15) {
        echo "<script>alert('Esta aula excederia o limite de 15 horas DETRAN para a categoria {$categoria}.');</script>";
        return false;
    }

    return true;
}

// Verifica se a data selecionada é um domingo
if (date('w', strtotime($data_ficha)) == 0) { // 0 representa domingo
    $conn->close();
    echo "<script>alert('A data selecionada cai em um domingo. Não é possível cadastrar a ficha.'); history.back();</script>";
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
    echo "<script>alert('A data informada é um feriado. Não é possível cadastrar a ficha.'); history.back();</script>";
    exit();
}

// Verificar sobreposição de horários para o instrutor/placa
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
    echo "<script>alert('Já existe uma ficha cadastrada com o mesmo instrutor, placa, horário e data que entra em conflito com este horário.'); history.back();</script>";
    exit();
}

// Verificar sobreposição de horários para o mesmo CPF
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
    echo "<script>alert('Este aluno já possui uma ficha cadastrada com data e horário que entra em conflito. Cadastro não permitido.'); history.back();</script>";
    exit();
}

// Verificar se já existe um registro exatamente igual
$query_verifica_igual = "SELECT id FROM fichas WHERE instrutor = ? AND placa = ? AND horario_inicio = ? AND horario_fim = ? AND data_ficha = ?";
$stmt = $conn->prepare($query_verifica_igual);
$stmt->bind_param('sssss', $instrutor, $placa, $horario_inicio, $horario_fim, $data_ficha);
$stmt->execute();
$result_verifica_igual = $stmt->get_result();
$verifica_igual = $result_verifica_igual->fetch_assoc();
$stmt->close();

if ($verifica_igual) {
    $conn->close();
    echo "<script>alert('Já existe uma ficha cadastrada com os mesmos dados.'); history.back();</script>";
    exit();
}

// Verificar sobreposição de horários para a mesma placa
$query_verifica_placa = "SELECT id FROM fichas WHERE placa = ? AND data_ficha = ? AND (
    (horario_inicio <= ? AND horario_fim > ?) OR
    (horario_inicio < ? AND horario_fim >= ?) OR
    (horario_inicio >= ? AND horario_fim <= ?)
)";
$stmt = $conn->prepare($query_verifica_placa);
$stmt->bind_param('ssssssss', $placa, $data_ficha, $horario_inicio, $horario_inicio, $horario_fim, $horario_fim, $horario_inicio, $horario_fim);
$stmt->execute();
$result_verifica_placa = $stmt->get_result();
$verifica_placa = $result_verifica_placa->fetch_assoc();
$stmt->close();

if ($verifica_placa) {
    $conn->close();
    echo "<script>alert('Já existe uma ficha cadastrada com a mesma placa e horário que entra em conflito.'); history.back();</script>";
    exit();
}

// Verificar sobreposição de horários para o mesmo instrutor
$query_verifica_instrutor = "SELECT id FROM fichas WHERE instrutor = ? AND data_ficha = ? AND (
    (horario_inicio <= ? AND horario_fim > ?) OR
    (horario_inicio < ? AND horario_fim >= ?) OR
    (horario_inicio >= ? AND horario_fim <= ?)
)";
$stmt = $conn->prepare($query_verifica_instrutor);
$stmt->bind_param('ssssssss', $instrutor, $data_ficha, $horario_inicio, $horario_inicio, $horario_fim, $horario_fim, $horario_inicio, $horario_fim);
$stmt->execute();
$result_verifica_instrutor = $stmt->get_result();
$verifica_instrutor = $result_verifica_instrutor->fetch_assoc();
$stmt->close();

if ($verifica_instrutor) {
    $conn->close();
    echo "<script>alert('Conflito de horário: O instrutor já tem uma ficha cadastrada para esse horário e data.'); history.back();</script>";
    exit();
}

// Inserir a ficha
$query_inserir = "INSERT INTO fichas (nome, rg, cpf, ladv, vencimento_processo, categoria, instrutor, placa, registro, horario_inicio, horario_fim, data_ficha, status)
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Em Andamento')";
$stmt = $conn->prepare($query_inserir);
$stmt->bind_param('ssssssssssss', $nome, $rg, $cpf, $ladv, $vencimento_processo, $categoria, $instrutor, $placa, $registro, $horario_inicio, $horario_fim, $data_ficha);

if ($stmt->execute()) {
    echo "<script>alert('Ficha cadastrada com sucesso.'); window.location.href='criarFicha.php?cpf={$cpf}';</script>";
} else {
    echo "<script>alert('Erro ao cadastrar ficha: " . $stmt->error . "'); history.back();</script>";
}

$stmt->close();
$conn->close();
?>