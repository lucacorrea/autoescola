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
    $associacao_logo = $stmt->fetch(PDO::FETCH_ASSOC);

    $logoImage = $associacao_logo['logo_image'] ?? '';

    // Conexão com o banco de dados
    include "conexao.php";

    // Verifica se o 'id' está presente no GET e não está vazio
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $aluno_id = $_GET['id'];
    } else {
        echo "ID do aluno não fornecido.";
        exit; // Interrompe a execução do script
    }


    // Definir local para exibir datas corretamente em português
    setlocale(LC_TIME, 'pt-BR.UTF-8');

    // Verifica se o aluno existe na tabela 'alunos' e relaciona com 'servicos_aluno'
    $queryAluno = $conn->prepare("
        SELECT a.nome, s.preco, s.categoria, s.forma_pagamento, s.data_cadastro 
        FROM alunos a
        JOIN servicos_aluno s ON a.nome = s.nome_aluno
        WHERE a.id = :id_aluno
    ");
    $queryAluno->bindParam(':id_aluno', $aluno_id, PDO::PARAM_INT);
    $queryAluno->execute();
    $dados = $queryAluno->fetch(PDO::FETCH_ASSOC);


    // Verifique se há dados retornados
    if ($dados) {
        $nome_aluno = $dados['nome'];
        $preco = $dados['preco']; 


        
        $forma_pagamento = $dados['forma_pagamento'];
        $categoria = $dados['categoria'];
        $data_cadastro = new DateTime($dados['data_cadastro']);
        $dia_semana = strftime('%A', $data_cadastro->getTimestamp());
        $dia = $data_cadastro->format('d');
        $mes = strftime('%B', $data_cadastro->getTimestamp());
        $ano = $data_cadastro->format('Y');
    } else {
        echo "<script>alert('Dados do aluno não encontrados.'); window.location.href='alunos.php';</script>";

        exit;
    }


    // Consulta para pegar o endereço e dados da associação
    $queryEndereco = $conn->query("SELECT * FROM enderecos LIMIT 1");
    $endereco = $queryEndereco->fetch(PDO::FETCH_ASSOC);

    $queryAssociacao = $conn->query("SELECT * FROM associacoes LIMIT 1");
    $associacao = $queryAssociacao->fetch(PDO::FETCH_ASSOC);

    // Verificar se os dados de endereço e associação foram encontrados
    if ($endereco && $associacao) {
        $rua = $endereco['endereco'];
        $bairro = $endereco['bairro'];
        $cidade = $endereco['cidade'];
        $uf = $endereco['uf'];
        $telefone = $endereco['telefone'];
        $numero = $endereco['numero'];
        $cnpj = $endereco['cnpj'];
        $nome_associacao = $associacao['nome_associacao'];
    } else {

        echo "<script>alert('Dados de endereço ou associação não encontrados.'); window.location.href='alunos.php';</script>";

        exit;
    }

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>RECIBO</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/recibo.css">

</head>
<body>

    <section class="bg-menu">
        <div class="conteudo" style="margin-left: -240px;">
            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page">
                                <a id="printButton" href="javascript:void(0);" class="btn btn-white btn-sm active">
                                    <i class="fas fa-receipt"></i>&nbsp; IMPRIMIR RECIBO
                                </a>
                            </h1>
                            <div class="container-right">
                                <div class="container-dados"></div>
                                <a id="exitButton" href="alunos.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="container mt-5">
                <!-- Primeiro Recibo -->
                <div class="recibo-header">
                    <?php if (!empty($logoImage)): ?>
                        <img src="uploads/<?php echo htmlspecialchars($logoImage); ?>" width="100" alt="Logo">
                    <?php endif; ?>
                    <div class="text-center" style="margin-top: -120px;">
                        <div class="recibo-info">
                            <h2 class="recibo-title"><?php echo htmlspecialchars($nome_associacao); ?></h2>
                            <div class="text-center">
                                <div class="separate" style="margin-top: -10px;"></div>
                            </div>
                            <small><strong>Rua:</strong> <?php echo $rua; ?>, <?php echo $numero; ?>, <?php echo $bairro; ?> - <?php echo $cidade; ?>/<?php echo $uf; ?> <br> <strong>CNPJ:</strong> <?php echo $cnpj; ?></small>
                            <small><strong>FONE/FAX:</strong> <?php echo $telefone; ?> &nbsp;&nbsp;<strong>Cel:</strong> <?php echo $telefone; ?></small>
                        </div>
                    </div>
                </div>
        
                <div class="text-center mb-4">
                    <h3><strong>..::RECIBO::..</strong></h3>
                    <div class="text-right">
                        <p style="float: right; margin-top: -40px; margin-right: 70px; font-size: 21px;"><strong>Nº: </strong></p>
                    </div>
                </div>
        
                <div class="recibo-card" style="margin-top: -20px;">
                    <p>RECEBI(EMOS) DE <?php echo strtoupper($nome_aluno); ?> A QUANTIA DE R$ <?php echo number_format($preco, 2, ',', '.'); ?> , <br class="mb-1"> CORRESPONDENTE AO PAGAMENTO EM <?php echo htmlspecialchars($forma_pagamento); ?> DA TROCA DE CAT.: <?php echo htmlspecialchars($categoria); ?>, E PARA CLAREZA FIRMO(AMOS) O PRESENTE NA CIDADE DE <?php echo $cidade; ?>/<?php echo $uf; ?> NA DATA DE <?php echo $dia_semana; ?>, <?php echo $dia; ?> de <?php echo $mes; ?> de <?php echo $ano; ?>.</p>
                </div>
        
                <div class="recibo-footer mb-5">
                    <hr>
                    <span class="signature-space">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                </div>

                <br><br>
                <div class="recibo-header">
                    <?php if (!empty($logoImage)): ?>
                        <img src="uploads/<?php echo htmlspecialchars($logoImage); ?>" width="100" alt="Logo">
                    <?php endif; ?>
                    <div class="text-center" style="margin-top: -120px;">
                        <div class="recibo-info">
                            <h2 class="recibo-title"><?php echo htmlspecialchars($nome_associacao); ?></h2>
                            <div class="text-center">
                                <div class="separate" style="margin-top: -10px;"></div>
                            </div>
                            <small><strong>Rua:</strong> <?php echo $rua; ?>, <?php echo $numero; ?>, <?php echo $bairro; ?> - <?php echo $cidade; ?>/<?php echo $uf; ?> <br> <strong>CNPJ:</strong> <?php echo $cnpj; ?></small>
                            <small><strong>FONE/FAX:</strong> <?php echo $telefone; ?> &nbsp;&nbsp;<strong>Cel:</strong> <?php echo $telefone; ?></small>
                        </div>
                    </div>
                </div>
        
                <div class="text-center mb-4">
                    <h3><strong>..::RECIBO::..</strong></h3>
                    <div class="text-right">
                        <p style="float: right; margin-top: -40px; margin-right: 70px; font-size: 21px;"><strong>Nº: </strong></p>
                    </div>
                </div>
        
                <div class="recibo-card" style="margin-top: -20px;">
                    <p>RECEBI(EMOS) DE <?php echo strtoupper($nome_aluno); ?> A QUANTIA DE R$ <?php echo number_format($preco, 2, ',', '.'); ?> , <br class="mb-1"> CORRESPONDENTE AO PAGAMENTO EM <?php echo htmlspecialchars($forma_pagamento); ?> DA TROCA DE CAT.: <?php echo htmlspecialchars($categoria); ?>, E PARA CLAREZA FIRMO(AMOS) O PRESENTE NA CIDADE DE <?php echo $cidade; ?>/<?php echo $uf; ?> NA DATA DE <?php echo $dia_semana; ?>, <?php echo $dia; ?> de <?php echo $mes; ?> de <?php echo $ano; ?>.</p>
                </div>
        
                <div class="recibo-footer mb-5
                ">
                    <hr>
                    <span class="signature-space">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>
                </div>
            </div>

        </div>

    </section>

    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('printButton').addEventListener('click', function() {
        // Esconde os botões
        this.style.display = 'none';
        document.getElementById('exitButton').style.display = 'none';

        // Imprime a página
        window.print();

        // Após a impressão, mostra os botões novamente
        this.style.display = 'inline-block';
        document.getElementById('exitButton').style.display = 'inline-block';
    });
</script>

</body>
</html>
