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

// Captura o ID da associação
$id = 1; 
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);
$logoImage = $associacao['logo_image'] ?? '';

// Captura o ID do aluno
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<script>alert('ID inválido.');history.back();</script>";
}

$id_aluno = (int)$_GET['id'];

// Busca o RG do aluno na tabela 'alunos'
$sql_rg_aluno = "SELECT rg FROM alunos WHERE id = :id";
$stmt_rg_aluno = $conn->prepare($sql_rg_aluno);
$stmt_rg_aluno->bindParam(':id', $id_aluno, PDO::PARAM_INT);
$stmt_rg_aluno->execute();
$aluno = $stmt_rg_aluno->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    echo "<script>
    alert('Aluno não encontrado.');history.back();</script>";

}

$rg_aluno = $aluno['rg'];

// Busca todas as fichas relacionadas ao RG do aluno, separando por categoria
$sql_fichas = "SELECT data_ficha, horario_inicio, horario_fim, nome, rg, cpf, categoria, placa, registro 
               FROM fichas
               WHERE rg = :rg";
$stmt_fichas = $conn->prepare($sql_fichas);
$stmt_fichas->bindParam(':rg', $rg_aluno, PDO::PARAM_STR);
$stmt_fichas->execute();
$fichas = $stmt_fichas->fetchAll(PDO::FETCH_ASSOC);

// Separa as fichas por categoria
$fichasA = [];
$fichasB = [];
$fichasD = [];
$fichasA_AB = []; // Renomeado para evitar confusão
$fichasB_AB = []; // Renomeado para evitar confusão
$fichasAD = []; // Renomeado para evitar confusão

foreach ($fichas as $ficha) {
    // Verifica cada categoria e armazena as fichas no array correspondente
    if ($ficha['categoria'] === 'A') {
        $fichasA[] = $ficha;
    } elseif ($ficha['categoria'] === 'B') {
        $fichasB[] = $ficha;
    } elseif ($ficha['categoria'] === 'D') {
        $fichasD[] = $ficha;
    } elseif ($ficha['categoria'] === 'A/AB') { // Corrigido para verificar a categoria 'A/AB' corretamente
        $fichasA_AB[] = $ficha;
    } elseif ($ficha['categoria'] === 'B/AB') { // Corrigido para verificar a categoria 'A/AB' corretamente
        $fichasB_AB[] = $ficha;
    } elseif ($ficha['categoria'] === 'A/D') { // Corrigido para verificar a categoria 'A/AB' corretamente
        $fichasAD[] = $ficha;
    }
}

// Verifica se há fichas
if (empty($fichasA) && empty($fichasB) && empty($fichasD) && empty($fichasA_AB) && empty($fichasB_AB) && empty($fichasAD)) {
    echo "<script>alert('Nenhuma ficha encontrada.');history.back();</script>";
}


// Busca dados da associação (endereço, CNPJ)
$sql_endereco = "SELECT endereco, bairro, numero, cnpj, cidade, uf, telefone FROM enderecos LIMIT 1";
$stmt_endereco = $conn->prepare($sql_endereco);
$stmt_endereco->execute();
$endereco = $stmt_endereco->fetch(PDO::FETCH_ASSOC);

// Funções para formatação de data e hora
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
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imprimir Ficha</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="./css/imprimirFicha.css">
    <style>
    .container{
        margin-left: 31%;
        zoom: 50% !important;
    }
    
    .signature-line {
        border-top: 1px solid #000;
        margin-top: 28px !important;
        padding-top: 5px;
        width: 90%;
        margin: 0 auto;
    }
    
    .endereco, .cnpj {
        font-size: 30px !important;
    }
    
    .ficha {
        line-height: 0.8;
        text-align: left;
        border: 1.5px solid #000 !important;
        font-size: 25.8px !important;
    }
    </style>
</head>
<body>

<div class="container mt-2" style="zoom: 70%;">
<div class="row <?php echo empty($fichasA) ? 'd-none' : ''; ?>">
    <!-- Coluna para Agendamento Categoria A -->
    <div class="col-md-6">
        <div class="row horario-ficha">
            <?php if (!empty($fichasA)): ?>
                <?php foreach ($fichasA as $ficha): ?>
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
                    <p>Nenhuma ficha encontrada para Categoria A.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna para Informações da Autoescola para Categoria A -->
    <div class="col-md-6 mb-5">
        <div class="card-dados">
            <div class="card-body d-flex align-items-start">
                <!-- Imagem -->
                <?php if (!empty($logoImage)): ?>
                    <img class="img-fluid me-3" width="100" src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                <?php endif; ?>

                <!-- Informações da Autoescola -->
                <div class="row dados-empresa">
                    <p class="col-md-12"><strong>Rua:</strong> <span class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>, <?php echo htmlspecialchars($endereco['numero']); ?>, <?php echo htmlspecialchars($endereco['bairro']); ?> - <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span> <strong>CNPJ:</strong> <span class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                    <p class="col-md-12"><strong>Fone/Fax:</strong> <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                </div>
            </div>

            <div class="card-body card-dados-aluno">
                <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                <p class="card-text border p-2"><strong>Candidato:</strong><br> <?php echo htmlspecialchars($ficha['nome']); ?></p>
                <div class="row rg">
                    <div class="col-md-6 mb-3">
                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                    </div>
                </div>
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
                <div class="row">
                    <div class="col-md-12 text-center mt-5 mb-5">
                        <div class="signature-line"></div>
                        <p><strong>Diretor:</strong></p>
                    </div>
                    <div class="col-md-12 text-center mb-5">
                        <div class="signature-line"></div>
                        <p><strong>Instrutor:</strong></p>
                    </div>
                    <div class="col-md-12 text-center mb-5">
                        <div class="signature-line"></div>
                        <p><strong>Candidato:</strong></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Separação para Categoria B -->
<div class="row mt-2 <?php echo empty($fichasB) ? 'd-none' : ''; ?>">
    <!-- Coluna para Agendamento Categoria B -->
    <div class="col-md-6">
        <div class="row horario-ficha">
            <?php if (!empty($fichasB)): ?>
                <?php foreach ($fichasB as $ficha): ?>
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
                    <p>Nenhuma ficha encontrada para Categoria B.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna para Informações da Autoescola para Categoria B -->
    <div class="col-md-6 mb-5">
        <div class="card-dados">
            <div class="card-body d-flex align-items-start">
                <!-- Imagem -->
                <?php if (!empty($logoImage)): ?>
                    <img class="img-fluid me-3" width="100" src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                <?php endif; ?>

                <!-- Informações da Autoescola -->
                <div class="row dados-empresa">
                    <p class="col-md-12"><strong>Rua:</strong> <span class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>, <?php echo htmlspecialchars($endereco['numero']); ?>, <?php echo htmlspecialchars($endereco['bairro']); ?> - <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span> <strong>CNPJ:</strong> <span class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                    <p class="col-md-12"><strong>Fone/Fax:</strong> <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                </div>
            </div>

            <div class="card-body card-dados-aluno">
                <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                <p class="card-text border p-2"><strong>Candidato:</strong><br> <?php echo htmlspecialchars($ficha['nome']); ?></p>
                <div class="row rg">
                    <div class="col-md-6 mb-3">
                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                    </div>
                </div>
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

<!-- Separação para Categoria D -->
<div class="row mt-2 <?php echo empty($fichasD) ? 'd-none' : ''; ?>">
    <!-- Coluna para Agendamento Categoria D -->
    <div class="col-md-6 ">
        <div class="row horario-ficha">
            <?php if (!empty($fichasD)): ?>
                <?php foreach ($fichasD as $ficha): ?>
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
                    <p>Nenhuma ficha encontrada para Categoria D.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna para Informações da Autoescola para Categoria D -->
    <div class="col-md-6 mb-5">
        <div class="card-dados">
            <div class="card-body d-flex align-items-start">
                <!-- Imagem -->
                <?php if (!empty($logoImage)): ?>
                    <img class="img-fluid me-3" width="100" src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                <?php endif; ?>

                <!-- Informações da Autoescola -->
                <div class="row dados-empresa">
                    <p class="col-md-12"><strong>Rua:</strong> <span class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>, <?php echo htmlspecialchars($endereco['numero']); ?>, <?php echo htmlspecialchars($endereco['bairro']); ?> - <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span> <strong>CNPJ:</strong> <span class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                    <p class="col-md-12"><strong>Fone/Fax:</strong> <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                </div>
            </div>

            <div class="card-body card-dados-aluno">
                <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                <p class="card-text border p-2"><strong>Candidato:</strong><br> <?php echo htmlspecialchars($ficha['nome']); ?></p>
                <div class="row rg">
                    <div class="col-md-6 mb-3">
                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                    </div>
                </div>
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

<!-- Separação para Categoria A/AB -->
<div class="row mt-2 <?php echo empty($fichasA_AB) ? 'd-none' : ''; ?>">
    <!-- Coluna para Agendamento Categoria A/AB -->
    <div class="col-md-6">
        <div class="row horario-ficha">
            <?php 
            // Acessando a chave 'A/AB' diretamente
            $fichas_A_AB = $fichasA_AB ?? []; 
            ?>
            <?php if (!empty($fichas_A_AB)): ?>
                <?php foreach ($fichas_A_AB as $ficha): ?>
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
                    <p>Nenhuma ficha encontrada para Categoria A/AB.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna para Informações da Autoescola para Categoria A/AB -->
    <div class="col-md-6 mb-5">
        <div class="card-dados">
            <div class="card-body d-flex align-items-start">
                <!-- Imagem -->
                <?php if (!empty($logoImage)): ?>
                    <img class="img-fluid me-3" width="100" src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                <?php endif; ?>

                <!-- Informações da Autoescola -->
                <div class="row dados-empresa">
                    <p class="col-md-12"><strong>Rua:</strong> <span class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>, <?php echo htmlspecialchars($endereco['numero']); ?>, <?php echo htmlspecialchars($endereco['bairro']); ?> - <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span> <strong>CNPJ:</strong> <span class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                    <p class="col-md-12"><strong>Fone/Fax:</strong> <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                </div>
            </div>

            <div class="card-body card-dados-aluno">
                <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                <p class="card-text border p-2"><strong>Candidato:</strong><br> <?php echo htmlspecialchars($ficha['nome']); ?></p>
                <div class="row rg">
                    <div class="col-md-6 mb-3">
                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                    </div>
                </div>
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

<!-- Separação para Categoria B/AB -->
<div class="row mt-2 <?php echo empty($fichasB_AB) ? 'd-none' : ''; ?>">
    <!-- Coluna para Agendamento Categoria B/AB -->
    <div class="col-md-6">
        <div class="row horario-ficha">
            <?php 
            // Acessando a chave 'B/AB' diretamente
            $fichas_B_AB = $fichasB_AB ?? []; 
            ?>
            <?php if (!empty($fichas_B_AB)): ?>
                <?php foreach ($fichas_B_AB as $ficha): ?>
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
                    <p>Nenhuma ficha encontrada para Categoria B/AB.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna para Informações da Autoescola para Categoria B/AB -->
    <div class="col-md-6 mb-5">
        <div class="card-dados">
            <div class="card-body d-flex align-items-start">
                <!-- Imagem -->
                <?php if (!empty($logoImage)): ?>
                    <img class="img-fluid me-3" width="100" src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                <?php endif; ?>

                <!-- Informações da Autoescola -->
                <div class="row dados-empresa">
                    <p class="col-md-12"><strong>Rua:</strong> <span class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>, <?php echo htmlspecialchars($endereco['numero']); ?>, <?php echo htmlspecialchars($endereco['bairro']); ?> - <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span> <strong>CNPJ:</strong> <span class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                    <p class="col-md-12"><strong>Fone/Fax:</strong> <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                </div>
            </div>

            <div class="card-body card-dados-aluno">
                <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                <p class="card-text border p-2"><strong>Candidato:</strong><br> <?php echo htmlspecialchars($ficha['nome']); ?></p>
                <div class="row rg">
                    <div class="col-md-6 mb-3">
                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                    </div>
                </div>
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

<!-- Separação para Categoria B/AB -->
<div class="row mt-2 <?php echo empty($fichasAD) ? 'd-none' : ''; ?>">
    <!-- Coluna para Agendamento Categoria AD -->
    <div class="col-md-6">
        <div class="row horario-ficha">
            <?php 
            // Acessando a chave 'AD' diretamente
            $fichasAD = $fichasAD ?? []; 
            ?>
            <?php if (!empty($fichasAD)): ?>
                <?php foreach ($fichasAD as $ficha): ?>
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
                    <p>Nenhuma ficha encontrada para Categoria AD.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Coluna para Informações da Autoescola para Categoria AD -->
    <div class="col-md-6 mb-5">
        <div class="card-dados">
            <div class="card-body d-flex align-items-start">
                <!-- Imagem -->
                <?php if (!empty($logoImage)): ?>
                    <img class="img-fluid me-3" width="100" src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                <?php endif; ?>

                <!-- Informações da Autoescola -->
                <div class="row dados-empresa">
                    <p class="col-md-12"><strong>Rua:</strong> <span class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>, <?php echo htmlspecialchars($endereco['numero']); ?>, <?php echo htmlspecialchars($endereco['bairro']); ?> - <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span> <strong>CNPJ:</strong> <span class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                    <p class="col-md-12"><strong>Fone/Fax:</strong> <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                </div>
            </div>

            <div class="card-body card-dados-aluno">
                <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                <p class="card-text border p-2"><strong>Candidato:</strong><br> <?php echo htmlspecialchars($ficha['nome']); ?></p>
                <div class="row rg">
                    <div class="col-md-6 mb-3">
                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                    </div>
                    <div class="col-md-6">
                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados"> <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                    </div>
                </div>
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

<script src="../js/bootstrap.bundle.min.js"></script>
<script>
    window.onload = function() {
        window.print();
    }

    // Redireciona após a impressão
    window.onafterprint = function() {
        window.location.href = 'ficha.php?id=<?php echo $id_aluno; ?>'; // Substitua 'outra_pagina.php' pela URL para a qual deseja redirecionar
    };
</script>
</body>
</html>
