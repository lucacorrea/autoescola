<?php
session_start(); // Inicia a sessão

// Verifica se o aluno está logado
if (!isset($_SESSION['user_id'])) {
    // Redireciona para a página de login se o usuário não estiver logado
    header("Location: loaderAluno.php");
    exit();
}

include "./painel/conexao.php";


$id = 1; 
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);


$logoImage = $associacao['logo_image'];


// Conectar ao banco de dados
include "conexao.php";

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se o aluno está logado
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    // Buscar dados do aluno logado na tabela login_aluno
    $sql_login_aluno = "SELECT nome_aluno FROM login_aluno WHERE id = ?";
    $stmt = $conn->prepare($sql_login_aluno);

    // Verifique se a preparação da consulta foi bem-sucedida
    if (!$stmt) {
        die("Erro na consulta SQL (login_aluno): " . $conn->error);
    }

    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result_login_aluno = $stmt->get_result();
    $login_aluno = $result_login_aluno->fetch_assoc();

    if ($login_aluno) {
        $nome_login_aluno = $login_aluno['nome_aluno'];

        // Buscar dados do aluno na tabela alunos com base no nome
        $sql_aluno = "SELECT id, nome, cpf, rua, numero, bairro, foto FROM alunos WHERE nome = ?";
        $stmt = $conn->prepare($sql_aluno);

        if (!$stmt) {
            die("Erro na consulta SQL (alunos): " . $conn->error);
        }

        $stmt->bind_param("s", $nome_login_aluno);
        $stmt->execute();
        $result_aluno = $stmt->get_result();
        $aluno = $result_aluno->fetch_assoc();

        if ($aluno) {
            $id_aluno = $aluno['id']; // Obtenha o id do aluno

            // Buscar o texto do contrato na tabela contratos usando o id do aluno
            $sql_contrato = "SELECT texto_contrato FROM contratos WHERE id_aluno = ?";
            $stmt = $conn->prepare($sql_contrato);

            if (!$stmt) {
                die("Erro na consulta SQL (contrato): " . $conn->error);
            }

            $stmt->bind_param("i", $id_aluno); // Use id_aluno que é um inteiro
            $stmt->execute();
            $result_contrato = $stmt->get_result();
            $contrato = $result_contrato->fetch_assoc();

            $texto_contrato = ''; // Inicializa como uma string vazia
            $texto_pre_definido = 'As Partes têm entre si, justo e acertado, este Contrato, que será regulado pelas cláusulas e condições abaixo estabelecidas:
            1. O prazo da prestação de serviço é de 1(um) ano, a contar da data de contratação do mesmo junto ao DETRAN-AM: cumprindo os prazos e procedimentos pelo CTB e resoluções pertinentes prestação desse serviço tem caráter individual, podendo ser transferida a sua titularidade, desde que seja por escrito e pessoalmente em um dos escritórios da contratada, antes do início do processo sendo vedada a transferência após o início do processo.
            3. Este serviço poderá ser rescindido a qualquer momento por falta de pagamento das parcelas, descumprimento de alguma cláusula, ou por motivo de força maior por qualquer das partes, devendo ser comunicado por escrito.
            4. O curso de Legislação começará sempre nos seguintes horários: segunda à sexta NOTURNO (de 18:00 às 21:00).
            Quando dada a aptidão do exame psicotécnico, o candidato deverá apresentar o protocolo no CFC, prazo imediato para afirmação da Legislação, caso contrário, ocorrerá a pedia da vaga na turma de Legislação
            PARAGRAFO 1".: Quando do agendamento das aulas práticas, o candidato que necessitar faltar ou se atrasar a sua aula, deverá informar ao CFC ou ao instrutor. com 3 horas de antecedência
            PARAGRAFO 2" .: Para realização das aulas de direção o candidato deverá estar munido de: RG e LADV, não podendo fazer aula trajando bermuda, camisa sem manga, saia curta e calçados que não se fixam os pés.
            PARAGRAFO 3".: De acordo com a ma. N.º 778/19 do CONTRAN, o candidato que iniciar processo de 1" Habilitação, inclusão ou troca de categoria a partir de 10/09/19. deverá cumprir obrigatoriamente 1 hora de sua carga horária pratica no turno Noturno.
            6. Os pagamentos efectuados antes do vencimento não terão seus valores alterados
            7. Todo pagamento referente a aulas extras, de reposição, reteste e aluguel de veículos para Auto Escola ou no dia da prova para a secretaria do CFC; sendo extremamente proibido fazer qualquer tipo de pagamento para o Instrutor.
            0,2% ao dia. 8. Sobre os pagamentos efetuados após o vencimento das parcelas, devedo incidir multa pecuniária de 2%, juros mora de 0,2% ao dia
            * Em caso de reprovação, o candidato que desejar utilizar veículo do CFC para reteste deverá agendar com antecedência a data do exame e pagar o valor de: cat. À R$ 55,00, cat. B R$ 90,00, cat. D R$ 150,00, referente ao aluguel do mesmo.
            * De acordo com a resolução 285/08 do CONTRAN é considerado hora/aula o intervalo de 50 min.'; // Texto predefinido

            if ($contrato) {
                $texto_contrato = $contrato['texto_contrato']; // Texto do contrato do aluno
            } else {
                $texto_contrato = $texto_pre_definido; // Use o texto predefinido se não houver contrato
            }

            // Buscar informações da empresa
            $sql_empresa = "
                SELECT a.nome_associacao, e.endereco, e.bairro, e.numero, e.cnpj, e.cidade, e.uf 
                FROM associacoes a 
                JOIN enderecos e ON a.id = e.id 
                LIMIT 1";
            $result_empresa = $conn->query($sql_empresa);

            if (!$result_empresa) {
                die("Erro na consulta SQL (empresa): " . $conn->error);
            }

            $empresa = $result_empresa->fetch_assoc();

            // Se não houver foto do aluno, usar uma imagem padrão
            $foto_aluno = !empty($aluno['foto']) ? "./painel/" . $aluno['foto'] : "./painel/uploads/user.png";
        } else {
            echo "Aluno não encontrado.";
            exit;
        }
    } else {
        echo "Aluno não encontrado na tabela login_aluno.";
        exit;
    }
} else {
    echo "Usuário não está logado.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./painel/img/logo.png" type="image/x-icon">
    <title>Contrato do usuário</title>

    <link rel="stylesheet" href="./css/bootstrap.min.css" />
    <link rel="stylesheet" href="./css/fontawesome.css" />
    <link rel="stylesheet" href="./css/animate.css" />
    <link rel="stylesheet" href="./css/main.css" />
    <style>
       .container{
        text-align: justify;
    }
    .container-sm img{
        max-width: 100px;
        height: 110px;
        border: none !important;
    }
    .contrato{
        line-height: 28px;
        margin-bottom: 50px;
    }
    .user{
        margin-top: 10px;
        width: 90px !important;  /* Largura de 3cm convertida para pixels */
        height: 120px !important; /* Altura de 4cm convertida para pixels */;
        border: none !important;
    }
    
    hr{
        max-width: 50%;
        color: #000 !important;
        border: 0.5px solid #000 !important;
        margin: 0 auto;
        margin-top: 60px;
    }
    .contratante{
        display: flex;
        justify-content: center;
        align-items: center;
    }

    @media(max-width: 768px){
    .container-sm img{
        max-width: 100px;
        height: 100px;
        border: none !important;
    } 

    .user{
        margin-top: 10px;
        width: 90px !important;
        height: 120px !important;
        border: none !important;
    }

    }
    </style>
</head>
<body>
    <div class="bg-top"></div>

    <header class="width-fix mt-3 mb-5">
        <div class="card">
            <div class="d-flex">
                <a href="./info.php" class="container-voltar">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="infos text-center">
                    <h1 class="header-title mb-0"><b>Contrato</b></h1>
                </div>
            </div>
        </div>

    </header>

    <section class="lista width-fix mt-5 mb-4 pb-5">
        <div class="container-sm">
            <!-- Logo da empresa -->
            <?php if (!empty($logoImage)): ?>
                <img class="rounded float mt-5" src="./painel/uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" width="100" alt="Logo">
            <?php else: ?>
                
            <?php endif; ?>
       
            <!-- Foto do aluno -->
            <img src="<?= $foto_aluno ?>" class="rounded float-end user mt-4" style="background: #fff; border: 1px solid #000 !important; background-size: cover; background-position: center;" alt="...">
        </div>
        <div class="container mb-5 mt-0">

            <!-- Exibe as parcelas em cartões -->
            <h2 class="text-center">CONTRATO</h2> 
            <div class="contrato">
                <b>CONTRATANTE:</b> <span class="nameUser"><?= $aluno['nome'] ?></span>, inscrito no CPF nº <span class="cpf"><?= $aluno['cpf'] ?></span>, residente 
                na <span class="endereco"><?= $aluno['rua'] ?>, <?= $aluno['numero'] ?>, <?= $aluno['bairro'] ?>, Coari, Amazonas, 69460-000.</span></br>
                       
                <b>CONTRATADO:</b> <span class="empresa"><?= $empresa['nome_associacao'] ?? '' ?></span>, inscrita no CNPJ sob o nº <span class="cnpj"><?= $empresa['cnpj'] ?? '' ?></span>, com sede na 
                <span class="endereco"><?= $empresa['endereco'] ?? '' ?>, nº <?= $empresa['numero'] ?? ''?>, Bairro <?= $empresa['bairro'] ?? '' ?>, <?= $empresa['cidade'] ?? '' ?>-<?= $empresa['uf'] ?? ''?>.</span></br>
                
                <?= nl2br(htmlspecialchars($texto_contrato)) ?></br><br>

                <hr>
                <b class="contratante">Contratante</b>

                <hr>
                <b class="contratante">Contratante</b>

            </div>

            <a href="./info.php" class="btn btn-yellow btn-full voltar mt-3">Voltar</a>
        </div>
    </section>

    <script type="text/javascript" src="./js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript" src="./js/item.js"></script>
</body>
</html>
