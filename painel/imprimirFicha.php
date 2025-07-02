<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado com permissão
function verificarAcesso()
{
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel'];
        if (in_array($nivel_usuario, ['admin', 'presidente', 'suporte'])) {
            return true;
        }
    }
    // Redireciona se não tiver permissão
    header("Location: loader.php");
    exit();
}

// Verifica o acesso antes de continuar
verificarAcesso();

require_once "conexao.php";

// Captura o ID da associação (exemplo fixo)
$id_associacao = 1;
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id_associacao, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);
$logoImage = $associacao['logo_image'] ?? '';

// Verifica se o CPF foi passado via GET
if (!isset($_GET['cpf']) || empty($_GET['cpf'])) {
    echo "<script>alert('CPF não informado.');history.back();</script>";
    exit;
}

$cpf_aluno = $_GET['cpf'];

// Verifica se o aluno com este CPF existe
$sql_aluno = "SELECT id FROM alunos WHERE cpf = :cpf";
$stmt_aluno = $conn->prepare($sql_aluno);
$stmt_aluno->bindParam(':cpf', $cpf_aluno, PDO::PARAM_STR);
$stmt_aluno->execute();
$aluno = $stmt_aluno->fetch(PDO::FETCH_ASSOC);

if (!$aluno) {
    echo "<script>alert('Aluno não encontrado.');history.back();</script>";
    exit;
}

$id_aluno = $aluno['id']; // Corrigido para usar no JS

// Busca todas as fichas relacionadas ao CPF diretamente
$sql_fichas = "SELECT data_ficha, horario_inicio, horario_fim, nome, rg, cpf, categoria, placa, registro 
               FROM fichas
               WHERE cpf = :cpf";
$stmt_fichas = $conn->prepare($sql_fichas);
$stmt_fichas->bindParam(':cpf', $cpf_aluno, PDO::PARAM_STR);
$stmt_fichas->execute();
$fichas = $stmt_fichas->fetchAll(PDO::FETCH_ASSOC);

// Separa fichas por categoria
$fichasA = [];
$fichasB = [];
$fichasD = [];
$fichasA_AB = [];
$fichasB_AB = [];
$fichasAD = [];

foreach ($fichas as $ficha) {
    switch ($ficha['categoria']) {
        case 'A':
            $fichasA[] = $ficha;
            break;
        case 'B':
            $fichasB[] = $ficha;
            break;
        case 'D':
            $fichasD[] = $ficha;
            break;
        case 'A/AB':
            $fichasA_AB[] = $ficha;
            break;
        case 'B/AB':
            $fichasB_AB[] = $ficha;
            break;
        case 'A/D':
            $fichasAD[] = $ficha;
            break;
    }
}

// Verifica se há fichas cadastradas
if (empty($fichasA) && empty($fichasB) && empty($fichasD) && empty($fichasA_AB) && empty($fichasB_AB) && empty($fichasAD)) {
    echo "<script>alert('Nenhuma ficha encontrada para este CPF.');history.back();</script>";
    exit;
}

// Busca dados da associação (endereço, CNPJ, etc.)
$sql_endereco = "SELECT endereco, bairro, numero, cnpj, cidade, uf, telefone FROM enderecos LIMIT 1";
$stmt_endereco = $conn->prepare($sql_endereco);
$stmt_endereco->execute();
$endereco = $stmt_endereco->fetch(PDO::FETCH_ASSOC);

// Funções de formatação
function formatarData($data)
{
    if (!$data)
        return '';
    $date = new DateTime($data);
    return $date->format('d/m/Y');
}

function formatarHorario($hora)
{
    if (!$hora)
        return '';
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
        .container {
            margin-left: 31%;
            zoom: 50% !important;
        }

        .signature-line {
            border-top: 1px solid #000;
            margin-top: 2px !important;
            padding-top: 5px;
            width: 90%;
            margin: 0 auto;
        }

        .endereco,
        .cnpj {
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
                                        <p class="card-text"><strong>Data:</strong>
                                            <?php echo formatarData($ficha['data_ficha']); ?></p>
                                        <p class="card-text"><strong>Início:</strong>
                                            <?php echo formatarHorario($ficha['horario_inicio']); ?></p>
                                        <p class="card-text"><strong>Fim:</strong>
                                            <?php echo formatarHorario($ficha['horario_fim']); ?></p>
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
                            <img class="img-fluid me-3" width="100"
                                src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                        <?php endif; ?>

                        <!-- Informações da Autoescola -->
                        <div class="row dados-empresa">
                            <p class="col-md-12"><strong>Rua:</strong> <span
                                    class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>,
                                    <?php echo htmlspecialchars($endereco['numero']); ?>,
                                    <?php echo htmlspecialchars($endereco['bairro']); ?> -
                                    <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span>
                                <strong>CNPJ:</strong> <span
                                    class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                            <p class="col-md-12"><strong>Fone/Fax:</strong>
                                <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                        </div>
                    </div>

                    <div class="card-body card-dados-aluno">
                        <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                                <p class="card-text border p-2"><strong>Candidato:</strong><br>
                                    <?php echo htmlspecialchars($ficha['nome']); ?></p>
                                <div class="row rg">
                                    <div class="col-md-6 mb-3">
                                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Categoria:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['categoria']); ?>
                                            </span></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Placa:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['placa']); ?> </span></p>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <p class="card-text border p-2"><strong>Reg. C.F.C.:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['registro']); ?>
                                            </span></p>
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
                                        <p class="card-text"><strong>Data:</strong>
                                            <?php echo formatarData($ficha['data_ficha']); ?></p>
                                        <p class="card-text"><strong>Início:</strong>
                                            <?php echo formatarHorario($ficha['horario_inicio']); ?></p>
                                        <p class="card-text"><strong>Fim:</strong>
                                            <?php echo formatarHorario($ficha['horario_fim']); ?></p>
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
                            <img class="img-fluid me-3" width="100"
                                src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                        <?php endif; ?>

                        <!-- Informações da Autoescola -->
                        <div class="row dados-empresa">
                            <p class="col-md-12"><strong>Rua:</strong> <span
                                    class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>,
                                    <?php echo htmlspecialchars($endereco['numero']); ?>,
                                    <?php echo htmlspecialchars($endereco['bairro']); ?> -
                                    <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span>
                                <strong>CNPJ:</strong> <span
                                    class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                            <p class="col-md-12"><strong>Fone/Fax:</strong>
                                <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                        </div>
                    </div>

                    <div class="card-body card-dados-aluno">
                        <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                                <p class="card-text border p-2"><strong>Candidato:</strong><br>
                                    <?php echo htmlspecialchars($ficha['nome']); ?></p>
                                <div class="row rg">
                                    <div class="col-md-6 mb-3">
                                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Categoria:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['categoria']); ?>
                                            </span></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Placa:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['placa']); ?> </span></p>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <p class="card-text border p-2"><strong>Reg. C.F.C.:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['registro']); ?>
                                            </span></p>
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
                                        <p class="card-text"><strong>Data:</strong>
                                            <?php echo formatarData($ficha['data_ficha']); ?></p>
                                        <p class="card-text"><strong>Início:</strong>
                                            <?php echo formatarHorario($ficha['horario_inicio']); ?></p>
                                        <p class="card-text"><strong>Fim:</strong>
                                            <?php echo formatarHorario($ficha['horario_fim']); ?></p>
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
                            <img class="img-fluid me-3" width="100"
                                src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                        <?php endif; ?>

                        <!-- Informações da Autoescola -->
                        <div class="row dados-empresa">
                            <p class="col-md-12"><strong>Rua:</strong> <span
                                    class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>,
                                    <?php echo htmlspecialchars($endereco['numero']); ?>,
                                    <?php echo htmlspecialchars($endereco['bairro']); ?> -
                                    <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span>
                                <strong>CNPJ:</strong> <span
                                    class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                            <p class="col-md-12"><strong>Fone/Fax:</strong>
                                <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                        </div>
                    </div>

                    <div class="card-body card-dados-aluno">
                        <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                                <p class="card-text border p-2"><strong>Candidato:</strong><br>
                                    <?php echo htmlspecialchars($ficha['nome']); ?></p>
                                <div class="row rg">
                                    <div class="col-md-6 mb-3">
                                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Categoria:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['categoria']); ?>
                                            </span></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Placa:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['placa']); ?> </span></p>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <p class="card-text border p-2"><strong>Reg. C.F.C.:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['registro']); ?>
                                            </span></p>
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
                                        <p class="card-text"><strong>Data:</strong>
                                            <?php echo formatarData($ficha['data_ficha']); ?></p>
                                        <p class="card-text"><strong>Início:</strong>
                                            <?php echo formatarHorario($ficha['horario_inicio']); ?></p>
                                        <p class="card-text"><strong>Fim:</strong>
                                            <?php echo formatarHorario($ficha['horario_fim']); ?></p>
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
                            <img class="img-fluid me-3" width="100"
                                src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                        <?php endif; ?>

                        <!-- Informações da Autoescola -->
                        <div class="row dados-empresa">
                            <p class="col-md-12"><strong>Rua:</strong> <span
                                    class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>,
                                    <?php echo htmlspecialchars($endereco['numero']); ?>,
                                    <?php echo htmlspecialchars($endereco['bairro']); ?> -
                                    <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span>
                                <strong>CNPJ:</strong> <span
                                    class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                            <p class="col-md-12"><strong>Fone/Fax:</strong>
                                <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                        </div>
                    </div>

                    <div class="card-body card-dados-aluno">
                        <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                                <p class="card-text border p-2"><strong>Candidato:</strong><br>
                                    <?php echo htmlspecialchars($ficha['nome']); ?></p>
                                <div class="row rg">
                                    <div class="col-md-6 mb-3">
                                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Categoria:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['categoria']); ?>
                                            </span></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Placa:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['placa']); ?> </span></p>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <p class="card-text border p-2"><strong>Reg. C.F.C.:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['registro']); ?>
                                            </span></p>
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
                                        <p class="card-text"><strong>Data:</strong>
                                            <?php echo formatarData($ficha['data_ficha']); ?></p>
                                        <p class="card-text"><strong>Início:</strong>
                                            <?php echo formatarHorario($ficha['horario_inicio']); ?></p>
                                        <p class="card-text"><strong>Fim:</strong>
                                            <?php echo formatarHorario($ficha['horario_fim']); ?></p>
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
                            <img class="img-fluid me-3" width="100"
                                src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                        <?php endif; ?>

                        <!-- Informações da Autoescola -->
                        <div class="row dados-empresa">
                            <p class="col-md-12"><strong>Rua:</strong> <span
                                    class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>,
                                    <?php echo htmlspecialchars($endereco['numero']); ?>,
                                    <?php echo htmlspecialchars($endereco['bairro']); ?> -
                                    <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span>
                                <strong>CNPJ:</strong> <span
                                    class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                            <p class="col-md-12"><strong>Fone/Fax:</strong>
                                <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                        </div>
                    </div>

                    <div class="card-body card-dados-aluno">
                        <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                                <p class="card-text border p-2"><strong>Candidato:</strong><br>
                                    <?php echo htmlspecialchars($ficha['nome']); ?></p>
                                <div class="row rg">
                                    <div class="col-md-6 mb-3">
                                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Categoria:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['categoria']); ?>
                                            </span></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Placa:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['placa']); ?> </span></p>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <p class="card-text border p-2"><strong>Reg. C.F.C.:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['registro']); ?>
                                            </span></p>
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
                                        <p class="card-text"><strong>Data:</strong>
                                            <?php echo formatarData($ficha['data_ficha']); ?></p>
                                        <p class="card-text"><strong>Início:</strong>
                                            <?php echo formatarHorario($ficha['horario_inicio']); ?></p>
                                        <p class="card-text"><strong>Fim:</strong>
                                            <?php echo formatarHorario($ficha['horario_fim']); ?></p>
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
                            <img class="img-fluid me-3" width="100"
                                src="uploads/<?php echo htmlspecialchars($logoImage); ?>" alt="Logo">
                        <?php endif; ?>

                        <!-- Informações da Autoescola -->
                        <div class="row dados-empresa">
                            <p class="col-md-12"><strong>Rua:</strong> <span
                                    class="endereco"><?php echo htmlspecialchars($endereco['endereco']); ?>,
                                    <?php echo htmlspecialchars($endereco['numero']); ?>,
                                    <?php echo htmlspecialchars($endereco['bairro']); ?> -
                                    <?php echo htmlspecialchars($endereco['cidade']); ?>/<?php echo htmlspecialchars($endereco['uf']); ?></span>
                                <strong>CNPJ:</strong> <span
                                    class="cnpj"><?php echo htmlspecialchars($endereco['cnpj']); ?></span></p>
                            <p class="col-md-12"><strong>Fone/Fax:</strong>
                                <span><?php echo htmlspecialchars($endereco['telefone']); ?></span></p>
                        </div>
                    </div>

                    <div class="card-body card-dados-aluno">
                        <h5 class="card-title border p-2 text-center"> >>DIRIGIR COM RESPONSABILIDADE<< </h5>
                                <p class="card-text border p-2"><strong>Candidato:</strong><br>
                                    <?php echo htmlspecialchars($ficha['nome']); ?></p>
                                <div class="row rg">
                                    <div class="col-md-6 mb-3">
                                        <p class="card-text border p-2"><strong>RG:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['rg']); ?> </span></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p class="card-text border p-2"><strong>CPF:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['cpf']); ?> </span></p>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Categoria:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['categoria']); ?>
                                            </span></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="card-text border p-2"><strong>Placa:</strong><br><span class="dados">
                                                <?php echo htmlspecialchars($ficha['placa']); ?> </span></p>
                                    </div>
                                    <div class="col-md-4 mb-4">
                                        <p class="card-text border p-2"><strong>Reg. C.F.C.:</strong><br><span
                                                class="dados"> <?php echo htmlspecialchars($ficha['registro']); ?>
                                            </span></p>
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
        // Garante que a impressão será chamada após o carregamento completo
        window.addEventListener('DOMContentLoaded', function () {
            setTimeout(function () {
                window.print();
            }, 500); // Pequeno delay para garantir renderização
        });

        // Redireciona após a impressão
        window.onafterprint = function () {
            window.location.href = 'ficha.php?cpf=<?php echo $cpf_aluno; ?>';
        };
    </script>
</body>

</html>