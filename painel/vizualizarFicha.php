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
?>
<?php

include "conexao.php";

$id_aluno = (int)$_GET['id'];

// Buscar o RG do aluno da tabela alunos
$sql_rg_aluno = "SELECT rg FROM alunos WHERE id = :id";
$stmt_rg_aluno = $conn->prepare($sql_rg_aluno);
$stmt_rg_aluno->bindParam(':id', $id_aluno, PDO::PARAM_INT);
$stmt_rg_aluno->execute();
$aluno = $stmt_rg_aluno->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    die("Aluno não encontrado.");
}

$rg_aluno = $aluno['rg'];

// Buscar todas as categorias do aluno
$sql_categorias = "SELECT DISTINCT categoria FROM fichas WHERE rg = :rg";
$stmt_categorias = $conn->prepare($sql_categorias);
$stmt_categorias->bindParam(':rg', $rg_aluno, PDO::PARAM_STR);
$stmt_categorias->execute();
$categorias = $stmt_categorias->fetchAll(PDO::FETCH_COLUMN);

$selected_categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';

if ($selected_categoria) {
    // Buscar todos os dados da tabela fichas onde o RG é igual ao RG do aluno e a categoria é a selecionada
    $sql_fichas = "SELECT data_ficha, horario_inicio, horario_fim, nome, rg, cpf, categoria, placa, registro 
                   FROM fichas
                   WHERE rg = :rg AND categoria = :categoria";
    $stmt_fichas = $conn->prepare($sql_fichas);
    $stmt_fichas->bindParam(':rg', $rg_aluno, PDO::PARAM_STR);
    $stmt_fichas->bindParam(':categoria', $selected_categoria, PDO::PARAM_STR);
    $stmt_fichas->execute();
    $fichas = $stmt_fichas->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Se nenhuma categoria foi selecionada, buscar todas as fichas
    $sql_fichas = "SELECT data_ficha, horario_inicio, horario_fim, nome, rg, cpf, categoria, placa, registro 
                   FROM fichas
                   WHERE rg = :rg";
    $stmt_fichas = $conn->prepare($sql_fichas);
    $stmt_fichas->bindParam(':rg', $rg_aluno, PDO::PARAM_STR);
    $stmt_fichas->execute();
    $fichas = $stmt_fichas->fetchAll(PDO::FETCH_ASSOC);
}

// Buscar dados da tabela enderecos
$sql_endereco = "SELECT endereco, cnpj, telefone, cep, bairro, numero FROM enderecos LIMIT 1";
$stmt_endereco = $conn->prepare($sql_endereco);
$stmt_endereco->execute();
$endereco = $stmt_endereco->fetch(PDO::FETCH_ASSOC);

// Funções para formatar data e horário
function formatarData($data) {
    $date = new DateTime($data);
    return $date->format('d/m/Y');
}

function formatarHorario($hora) {
    $time = new DateTime($hora);
    return $time->format('H:i');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Visualização da Ficha</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/ficha.css">

</head>
<body>

    <section class="bg-menu">

        <div class="conteudo" style="margin-left: -240px;">
            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page">
                                <b>
                                    <i class="fas fa-calendar-alt"></i>&nbsp; FICHA
                                </b>
                            </h1>

                            <div class="container-right">
                                <form method="post">
                                    <select name="categoria" class="select" onchange="this.form.submit()">
                                        <option value="">Selecione uma Categoria</option>
                                        <?php foreach ($categorias as $categoria): ?>
                                            <option value="<?php echo htmlspecialchars($categoria); ?>" <?php echo ($categoria === $selected_categoria) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($categoria); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </form>
                                &nbsp; &nbsp; &nbsp; &nbsp;<a href="ficha.php?id=<?php echo $id_aluno; ?>" class="btn btn-white btn-sm">
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
                    <div class="container mt-5">
                        <div class="row">
                            <!-- Coluna para Agendamento -->
                            <div class="col-md-6">
                                <div class="row">
                                    <!-- Card 1 -->
                                    <?php if (!empty($fichas)): ?>
                                        <?php foreach ($fichas as $ficha): ?>
                                            <div class="col-md-4 ficha-card">
                                                <div class="">
                                                    <div class="card-body ficha mb-2">
                                                        <p class="card-text"><strong>Data:</strong> <?php echo formatarData($ficha['data_ficha']); ?></p>
                                                        <p class="card-text"><strong>Início:</strong> <?php echo formatarHorario($ficha['horario_inicio']); ?></p>
                                                        <p class="card-text"><strong>Fim:</strong> <?php echo formatarHorario($ficha['horario_fim']); ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <div class="col-12">
                                            <p>Nenhuma ficha encontrada.</p>
                                        </div>
                                    <?php endif; ?>
                    
                                </div>
                            </div>
                    
                                <!-- Coluna para Informações da Autoescola -->
                                <div class="col-md-6 mb-5">
                                <div class=" card-dados">
                                    <div class="card-body d-flex align-items-start">
                                        <!-- Imagem -->
                                        <?php if (!empty($logoImage)): ?>
                                            <img class="img-fluid me-3" width="100" src="uploads/<?php echo htmlspecialchars($logoImage); ?>" width="100" alt="Logo">
                                        <?php else: ?>
                                            
                                        <?php endif; ?>
                                        
                                        <!-- Informações da Autoescola -->
                                        <div class="row dados-empresa">
                                            <p class="col-md-12"><strong>Rua:</strong> <span class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?></span></p>
                                            <p class="col-md-12"><strong>CNPJ:</strong> <span class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                                            <p class="col-md-6"><strong>Fone/Fax:</strong> <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                                            <p class="col-md-6"><strong>Cel:</strong> <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                                        </div>
                                    </div>
                    
                                    <div class="card-body card-dados-aluno">
                                        <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                                        <!-- Nome separado -->
                                        <p class="card-text border p-2"><strong>Candidato:</strong><br> <?php echo htmlspecialchars($ficha['nome']); ?></p>
                    
                                        <!-- RG e CPF lado a lado -->
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                                            </div>
                                            <div class="col-md-6">
                                                <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                                            </div>
                                        </div>
                    
                                        <!-- Categoria, Placa e Reg. CFC lado a lado -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <p class="card-text border p-2"><strong>Categoria:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['categoria']); ?> </span></p>
                                            </div>
                                            <div class="col-md-4">
                                                <p class="card-text border p-2"><strong>Placa:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['placa']); ?> </span></p>
                                            </div>
                                            <div class="col-md-4 mb-4">
                                                <p class="card-text border p-2"><strong>Reg. C.F.C.:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['registro']); ?> </span></p>
                                            </div>
                                        </div>
                    
                                        <!-- Diretor, Instrutor, e Candidato um embaixo do outro com linha de assinatura -->
                                        <div class="row">
                                            <div class="col-md-12 text-center mt-5 mb-3">
                                                <div class="signature-line"></div>
                                                <p><strong>Diretor:</strong></p>
                                                
                                            </div>
                                            <div class="col-md-12 text-center mb-3">
                                                <div class="signature-line"></div>
                                                <p><strong>Instrutor:</strong></p>
                                            </div>
                                            <div class="col-md-12 text-center mb-3">
                                                <div class="signature-line"></div>
                                                <p><strong>Candidato:</strong></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </section>
</body>
</html>
