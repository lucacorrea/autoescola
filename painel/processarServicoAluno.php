<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);


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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recebendo os dados do formulário
    $nome_aluno = $_POST['nome_aluno'];
    $servico_aluno = $_POST['servico_aluno'];
    $forma_pagamento_aluno = $_POST['forma_pagamento_aluno'];
    $preco_aluno = $_POST['preco_aluno'];
    $valor_entrada = $_POST['valor_entrada'];
    $categoria_aluno = $_POST['categoria_aluno'];
    $numero_parcelas = isset($_POST['numero_parcelas_aluno']) ? intval($_POST['numero_parcelas_aluno']) : 1;
    $pago_aluno = $_POST['pago_aluno']; // Recebendo o valor do campo pago (SIM/NÃO)

    // Calcula a data de pagamento com base no campo 'pago_aluno'
    if ($forma_pagamento_aluno == 'Parcelado') {
        if ($pago_aluno == 'SIM') {
            $data_pagamento = date('Y-m-d', strtotime("+1 month"));
            $numero_parcelas = max($numero_parcelas - 1, 1); // Diminui 1 do número de parcelas, garantindo que não seja menor que 1
        } else {
            $data_pagamento = date('Y-m-d'); // Data padrão quando parcelado e não pago
        }
    } else {
        if ($pago_aluno == 'SIM') {
            $data_pagamento = NULL; // Data atual se não for parcelado e foi pago
        } else {
            // Se não for parcelado e não foi pago, exibe mensagem e encerra o script
            echo "<script>alert('Pagamento não confirmado. Nenhuma atualização foi realizada.'); window.location.href='alunos.php';</script>";
            exit();
        }
    }

    include "conexao.php";
    
    $preco_aluno = !empty($preco_aluno) ? $preco_aluno : null;
    $valor_entrada = isset($valor_entrada) && $valor_entrada !== '' ? $valor_entrada : 0.00;
    $data_pagamento = !empty($data_pagamento) ? $data_pagamento : null;

    // Prepara a inserção na tabela servicos_aluno
    $sql_servico = "INSERT INTO servicos_aluno (nome_aluno, servico, forma_pagamento, preco, valor_entrada, numero_parcelas, data_pagamento, categoria, pago, data_cadastro) 
                    VALUES (:nome_aluno, :servico, :forma_pagamento, :preco, :valor_entrada, :numero_parcelas, :data_pagamento, :categoria, :pago, NOW())";

    $stmt_servico = $conn->prepare($sql_servico);
    $stmt_servico->bindParam(':nome_aluno', $nome_aluno, PDO::PARAM_STR);
    $stmt_servico->bindParam(':servico', $servico_aluno, PDO::PARAM_STR);
    $stmt_servico->bindParam(':forma_pagamento', $forma_pagamento_aluno, PDO::PARAM_STR);
    $stmt_servico->bindParam(':preco', $preco_aluno, PDO::PARAM_STR);
    $stmt_servico->bindParam(':valor_entrada', $valor_entrada, PDO::PARAM_STR);
    $stmt_servico->bindParam(':numero_parcelas', $numero_parcelas, PDO::PARAM_INT);
    $stmt_servico->bindParam(':data_pagamento', $data_pagamento, PDO::PARAM_STR);
    $stmt_servico->bindParam(':categoria', $categoria_aluno, PDO::PARAM_STR);
    $stmt_servico->bindParam(':pago', $pago_aluno, PDO::PARAM_STR);

    if ($stmt_servico->execute()) {
        // Recupera o ID do aluno baseado no nome após o cadastro do serviço
        $sql_aluno = "SELECT id FROM alunos WHERE nome = :nome_aluno";
        $stmt_aluno = $conn->prepare($sql_aluno);

        // Verifique se a preparação da consulta foi bem-sucedida
        if (!$stmt_aluno) {
            die("Erro ao preparar a consulta: " . $conn->errorInfo()[2]);
        }

        // Depuração: Verifique se o nome do aluno foi passado corretamente
        if (empty($nome_aluno)) {
            die("Nome do aluno está vazio. Verifique o processo de cadastro.");
        }

        $stmt_aluno->bindParam(':nome_aluno', $nome_aluno, PDO::PARAM_STR);
        $stmt_aluno->execute();
        $stmt_aluno->bindColumn('id', $id_aluno);
        $stmt_aluno->fetch(PDO::FETCH_ASSOC);

        // Verifique se o ID do aluno foi recuperado corretamente
        if (empty($id_aluno)) {
            die("Erro: ID do aluno não encontrado para o nome '$nome_aluno'. Verifique se o aluno foi cadastrado corretamente.");
        }

        // Redireciona para a página do recibo com o ID do aluno
        echo "<script>alert('Cadastro realizado com sucesso.'); window.location.href='recibo.php?id=$id_aluno';</script>";

    } else {
        echo "Erro: " . implode(" ", $stmt_servico->errorInfo());
    }

    // Validação do preço
    $preco_aluno = isset($preco_aluno) && $preco_aluno !== '' ? $preco_aluno : 0.00;
    
    // Se a forma de pagamento não for "Parcelado", insere o preço na tabela relatorios
    if ($forma_pagamento_aluno != 'Parcelado') {
        $sql_relatorio = "INSERT INTO relatorios (nome_aluno, preco, data_preco, categoria_preco, valor_saida) 
                          VALUES (:nome_aluno, :preco, NOW(), :categoria, :preco)";
        $stmt_relatorio = $conn->prepare($sql_relatorio);
        $stmt_relatorio->bindParam(':nome_aluno', $nome_aluno, PDO::PARAM_STR);
        $stmt_relatorio->bindParam(':preco', $preco_aluno, PDO::PARAM_STR);
        $stmt_relatorio->bindParam(':categoria', $categoria_aluno, PDO::PARAM_STR);
    
        if (!$stmt_relatorio->execute()) {
            echo "Erro ao inserir no relatório: " . implode(" ", $stmt_relatorio->errorInfo());
        }
    }

    // Se a forma de pagamento for "Parcelado", insere o valor_entrada na tabela relatorios
    if ($forma_pagamento_aluno == 'Parcelado') {
        $sql_relatorio = "INSERT INTO relatorios (nome_aluno, preco, data_preco, categoria_preco, valor_saida) VALUES (:nome_aluno, :valor_entrada, NOW(), :categoria, :valor_entrada)";
        $stmt_relatorio = $conn->prepare($sql_relatorio);
        $stmt_relatorio->bindParam(':nome_aluno', $nome_aluno, PDO::PARAM_STR);
        $stmt_relatorio->bindParam(':valor_entrada', $valor_entrada, PDO::PARAM_STR);
        $stmt_relatorio->bindParam(':categoria', $categoria_aluno, PDO::PARAM_STR);

        if (!$stmt_relatorio->execute()) {
            echo "Erro ao inserir no relatório: " . implode(" ", $stmt_relatorio->errorInfo());
        }
    }

}

?>