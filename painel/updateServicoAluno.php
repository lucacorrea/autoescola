<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como administrador ou presidente
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é admin ou presidente
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        // Verificar se o nível de usuário é admin ou presidente
        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
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

require_once 'conexao.php';

// Obtém os dados do formulário e faz a sanitização
$servico_aluno = isset($_POST['servico_aluno']) ? $_POST['servico_aluno'] : '';
$pago_aluno = isset($_POST['pago_aluno']) ? $_POST['pago_aluno'] : 'NÃO';
$nome_aluno = isset($_POST['nome_aluno']) ? $_POST['nome_aluno'] : '';
$categoria_aluno = isset($_POST['categoria_aluno']) ? $_POST['categoria_aluno'] : '';
$forma_pagamento_aluno = isset($_POST['forma_pagamento_aluno']) ? $_POST['forma_pagamento_aluno'] : '';
$numero_parcelas_aluno = isset($_POST['numero_parcelas_aluno']) ? intval($_POST['numero_parcelas_aluno']) : 0;
$valor_entrada = isset($_POST['valor_entrada']) ? $_POST['valor_entrada'] : '';
$aluno_id = isset($_POST['aluno_id']) ? intval($_POST['aluno_id']) : 0;

// Obtém os dados atuais do serviço do aluno
$sql = "SELECT numero_parcelas, preco, forma_pagamento, data_pagamento, valor_entrada FROM servicos_aluno WHERE nome_aluno = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$nome_aluno]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$numero_parcelas_atual = $result['numero_parcelas'] ?? 0;
$preco_atual = $result['preco'] ?? 0;
$forma_pagamento_atual = $result['forma_pagamento'] ?? '';
$data_pagamento_atual = $result['data_pagamento'] ?? null;
$valor_entrada_atual = $result['valor_entrada'] ?? '';

// Inicializa a variável data_pagamento
$data_pagamento = null;

// Verifica se o pagamento foi realizado e atualiza os dados
if ($pago_aluno === 'SIM') {
    if ($numero_parcelas_aluno > 0) {
        $numero_parcelas_novo = $numero_parcelas_aluno - 1;
        $forma_pagamento_novo = $numero_parcelas_novo > 0 ? 'Parcelado' : $forma_pagamento_atual;

        if ($numero_parcelas_aluno == 1) {
            $data_pagamento = null; // Define como NULL
        } elseif ($pago_aluno === 'SIM') {
            $data_pagamento = new DateTime($data_pagamento_atual);
            $data_pagamento->add(new DateInterval('P1M'));
            $data_pagamento = $data_pagamento->format('Y-m-d');
        }

        $sql = "UPDATE servicos_aluno SET servico=?, forma_pagamento=?, preco=?, numero_parcelas=?, valor_entrada=?, categoria=?, pago=?, data_pagamento=? WHERE nome_aluno=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $servico_aluno, 
            $forma_pagamento_novo, 
            $preco_atual, 
            $numero_parcelas_novo, 
            $valor_entrada_atual, 
            $categoria_aluno, 
            $pago_aluno, 
            $data_pagamento, 
            $nome_aluno
        ]);
    } elseif ($numero_parcelas_aluno == 0) {
        $data_pagamento = null; // Define como NULL para última parcela

        $sql = "UPDATE servicos_aluno SET servico=?, forma_pagamento=?, preco=?, numero_parcelas=0, valor_entrada=?, categoria=?, pago=?, status='finalizado', data_pagamento=? WHERE nome_aluno=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $servico_aluno, 
            'Pago', 
            $preco_atual, 
            $valor_entrada_atual, 
            $categoria_aluno, 
            $pago_aluno, 
            $data_pagamento, 
            $nome_aluno
        ]);
    } else {
        $sql = "UPDATE servicos_aluno SET servico=?, forma_pagamento=?, preco=?, numero_parcelas=0, valor_entrada=?, categoria=?, pago=?, status='finalizado', data_pagamento=NULL WHERE nome_aluno=?";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $servico_aluno, 
            'Pago', 
            $preco_atual, 
            $valor_entrada_atual, 
            $categoria_aluno, 
            $pago_aluno, 
            $nome_aluno
        ]);
    }

    if ($stmt) {
        // Realiza o INSERT INTO na tabela relatorios
        $sql_relatorio = "INSERT INTO relatorios (nome_aluno, preco, data_preco, categoria_preco, valor_saida) VALUES (?, ?, NOW(), ?, ?)";
        $stmt_relatorio = $conn->prepare($sql_relatorio);
        $stmt_relatorio->execute([
            $nome_aluno, 
            $valor_entrada_atual, 
            $categoria_aluno, 
            $valor_entrada_atual
        ]);

        $sql = "INSERT INTO info_parcelas (nome_aluno, servico, forma_pagamento, valor_entrada, preco, numero_parcelas, data_pagamento, pago, categoria, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE servico=VALUES(servico), forma_pagamento=VALUES(forma_pagamento), valor_entrada=VALUES(valor_entrada), preco=VALUES(preco), numero_parcelas=VALUES(numero_parcelas), data_pagamento=VALUES(data_pagamento), pago=VALUES(pago), categoria=VALUES(categoria), status=VALUES(status)";

        $stmt = $conn->prepare($sql);
        $status_atualizado = $numero_parcelas_aluno > 0 ? 'Parcelado' : 'Finalizado';
        $stmt->execute([
            $nome_aluno, 
            $servico_aluno, 
            $forma_pagamento_aluno, 
            $valor_entrada_atual, 
            $preco_atual, 
            $numero_parcelas_aluno, 
            $data_pagamento, 
            $pago_aluno, 
            $categoria_aluno, 
            $status_atualizado
        ]);

        echo "<script>alert('Cadastro atualizado com sucesso.'); window.location.href='alunos.php';</script>";
    } else {
        
    }
} else {
    echo "<script>alert('Pagamento não confirmado. Nenhuma atualização foi realizada.'); window.location.href='alunos.php';</script>";
}
?>

