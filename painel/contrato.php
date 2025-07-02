<?php

//------------------SESSION----------------------
    session_start();

    function verificarAcesso() {
        if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
            $nivel_usuario = $_SESSION['nivel'];

            if($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
                return true;
            }
        }
        
        header("Location: loader.php");
        exit();
    }

    verificarAcesso();

//------------------END SESSION------------------


//-----------------CONTRATO----------------------

    include "conexao.php";

    $id_aluno = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $sql_contrato = "SELECT texto_contrato FROM contratos WHERE id_aluno = :id_aluno";
    $stmt_contrato = $conn->prepare($sql_contrato);
    $stmt_contrato->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
    $stmt_contrato->execute();
    $result_contrato = $stmt_contrato->fetch(PDO::FETCH_ASSOC);

    if ($result_contrato) {
        $texto_contrato = $result_contrato['texto_contrato'];
    } else {
        $texto_contrato = 'As Partes têm entre si, justo e acertado, este Contrato, que será regulado pelas cláusulas e condições abaixo estabelecidas:
        1. O prazo da prestação de serviço é de 1(um) ano, a contar da data de contratação do mesmo junto ao DETRAN-AM: cumprindo os prazos e procedimentos pelo CTB e resoluções pertinentes prestação desse serviço tem caráter individual, podendo ser transferida a sua titularidade, desde que seja por escrito e pessoalmente em um dos escritórios da contratada, antes do início do processo sendo vedada a transferência após o início do processo.
        3. Este serviço poderá ser rescindido a qualquer momento por falta de pagamento das parcelas, descumprimento de alguma cláusula, ou por motivo de força maior por qualquer das partes, devendo ser comunicado por escrito.
        4. O curso de Legislação começará sempre nos seguintes horários: segunda à sexta NOTURNO (de 18:00 às 21:00).
        Quando dada a aptidão do exame psicotécnico, o candidato deverá apresentar o protocolo no CFC, prazo imediato para afirmação da Legislação, caso contrário, ocorrerá a pedia da vaga na turma de Legislação
        PARAGRAFO 1".: Quando do agendamento das aulas práticas, o candidato que necessitar faltar ou se atrasar a sua aula, deverá informar ao CFC ou ao instrutor. com 3 horas de antecedência
        PARAGRAFO 2" .: Para realização das aulas de direção o candidato deverá estar munido de: RG e LADV, não podendo fazer aula trajando bermuda, camisa sem manga, saia curta e calçados que não se fixam os pés.
        PARAGRAFO 3".: De acordo com a ma. N.º 778/19 do CONTRAN, o candidato que iniciar processo de 1" Habilitação, inclusão ou troca de categoria a partir de 10/09/19. deverá cumprir obrigatoriamente 1 hora de sua carga horária pratica no turno Noturno.
        6. Os pagamentos efetuados antes do vencimento não terão seus valores alterados
        7. Todo pagamento referente a aulas extras, de reposição, reteste e aluguel de veículos para Auto Escola ou no dia da prova para a secretaria do CFC; sendo extremamente proibido fazer qualquer tipo de pagamento para o Instrutor.
        0,2% ao dia. 8. Sobre os pagamentos efetuados após o vencimento das parcelas, devedo incidir multa pecuniária de 2%, juros mora de 0,2% ao dia
        * Em caso de reprovação, o candidato que desejar utilizar veículo do CFC para reteste deverá agendar com antecedência a data do exame e pagar o valor de: cat. À R$ 55,00, cat. B R$ 90,00, cat. D R$ 150,00, referente ao aluguel do mesmo.
        * De acordo com a resolução 285/08 do CONTRAN é considerado hora/aula o intervalo de 50 min.';
    }

//------------------END CONTRATO-----------------


//------------------GET ALUNOS-------------------

    include "conexao.php";

    $id_aluno = isset($_GET['id']) ? intval($_GET['id']) : 0;

    $sql_aluno = "SELECT * FROM alunos WHERE id = :id_aluno";
    $stmt_aluno = $conn->prepare($sql_aluno);
    $stmt_aluno->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
    $stmt_aluno->execute();
    $aluno = $stmt_aluno->fetch(PDO::FETCH_ASSOC);

    if (!$aluno) {
        exit("Nenhum aluno encontrado.");
    }

    $sql_associacao = "SELECT a.nome_associacao, e.endereco, e.bairro, e.numero, e.cnpj, e.cidade, e.uf 
                    FROM associacoes a 
                    JOIN enderecos e ON e.id = a.id
                    WHERE a.id = 1";
    $stmt_associacao = $conn->prepare($sql_associacao);
    $stmt_associacao->execute();
    $associacao = $stmt_associacao->fetch(PDO::FETCH_ASSOC);

    if (!$associacao) {
        
    }

//------------------END GET ALUNOS---------------


//------------------SESSION IMAGE EMPRESA--------

    include "conexao.php";

    $sql_logo = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql_logo);
    $id = 1;
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $associacao_logo = $stmt->fetch(PDO::FETCH_ASSOC);

    $logoImage = $associacao_logo['logo_image'] ?? '';

//------------------SESSION IMAGE EMPRESA--------

?>

<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <link rel="shortcut icon" href="../img/logo.png" type="image/x-icon">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <title>Contrato do Candidato</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="../css/bootstrap.min.css">
        <link rel="stylesheet" href="./css/painel.css">
        <link rel="stylesheet" href="../css/fontawesome.css">
        <link rel="stylesheet" href="./css/contratoAluno.css">
        <!-- End Custom CSS -->

        
    </head>

    <body>

        <!-- menu-top -->
        <div class="menu-top">

            <div class="container">
                <div class="row">
                    <div class="col-12 d-flex align-items-center mt-4 mb-2">

                        <h1 class="title-page">

                            <a href="#" id="printButton" class="btn btn-white btn-sm active">
                                <i class="fas fa-save"></i>&nbsp; Imprimir Contrato
                            </a>
                            &nbsp;<button id="editButton" class="btn btn-white btn-sm active"><i class="fas fa-edit"></i> Editar Contrato</button>
                            <button id="cancelButton" class="btn btn-white btn-sm active" style="margin-top: -20px;"><i class="fas fa-times-circle"></i> Cancelar Edição</button>
                        
                        </h1>

                        <div class="container-right">

                            <a href="alunos.php" class="btn btn-white btn-sm" id="exitButton">
                                <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                            </a>

                        </div>

                    </div>
                </div>
            </div>

        </div>
        <!-- End menu-top -->

        <!-- container -->
        <div class="container dados">

            <!-- company-info -->
            <div class="company-info">

                <?php if (!empty($logoImage)): ?>
                    <img src="uploads/<?php echo htmlspecialchars($logoImage); ?>" width="100" alt="Logo">
                <?php endif; ?>

                <div class="company-details">
                    <div class="divider"></div>
                </div>

            </div>
            <!-- End company info -->

            <!-- card -->
            <div class="card card-candidato">

                <img class="foto" src="<?= !empty($aluno['foto']) ? $aluno['foto'] : './uploads/user.png' ?>" alt="Imagem do Candidato">

                <!-- card-body -->
                <div class="card-body">

                    <h1 class="title">CONTRATO</h1>

                    <div class="row">

                        <div class="contrato">

                            <b>CONTRATANTE:</b> <span class="nameUser"><?= $aluno['nome'] ?></span>, inscrito no CPF nº <span class="cpf"><?= $aluno['cpf'] ?></span>, residente na <span class="endereco"><?= $aluno['rua'] . ', ' . $aluno['numero'] . ', ' . $aluno['bairro'] ?>, Coari, Amazonas.</br>
                            <b>CONTRATADO:</b> <span class="empresa"><?= $associacao['nome_associacao'] ?? "" ?></span>, inscrita no CNPJ sob o nº <span class="cnpj"><?= $associacao['cnpj'] ?></span>, com sede na <span class="endereco"><?= $associacao['endereco'] . ', ' . $associacao['numero'] . ', ' . $associacao['bairro'] . ', ' . $associacao['cidade'] . '-' . $associacao['uf'] ?>.</span></br>

                            <div id="contratoDisplay">
                                <?= nl2br(htmlspecialchars($texto_contrato)); ?>
                            </div>

                            <div class="container-textarea">
                                <textarea id="contratoTexto"><?= htmlspecialchars($texto_contrato); ?></textarea>
                            </div>

                            <div class="mt-0 separate"></div>
                            <b class="contratante mb-0">Contratante</b>

                            <div class="mt-0 separate" style="margin-top: 70px !important;"></div>
                            <b class="contratante">Testemunha</b>


                            <div class="buttons-container">
                                <button id="saveButton" class="btn btn-white btn-sm active"><i class="fas fa-save"></i> Salvar Contrato</button>
                            
                            </div>

                        </div>

                    </div>

                </div>
                <!-- End card-body -->

            </div>
            <!-- End card -->

        </div>
        <!-- End container -->

        <!-- Scripts -->
        <script>
            const printButton = document.getElementById('printButton');
            const editButton = document.getElementById('editButton');
            const contratoDisplay = document.getElementById('contratoDisplay');
            const contratoTextarea = document.getElementById('contratoTexto');
            const buttonsContainer = document.querySelector('.buttons-container');
            const saveButton = document.getElementById('saveButton');
            const cancelButton = document.getElementById('cancelButton');

            window.onafterprint = function() {
                document.getElementById('printButton').style.display = 'inline-block';
                document.getElementById('exitButton').style.display = 'inline-block';
                document.getElementById('editButton').style.display = 'inline-block';
            }

            printButton.addEventListener('click', function() {
                printButton.style.display = 'none';
                editButton.style.display = 'none';
                document.getElementById('exitButton').style.display = 'none';
                window.print();
                window.onafterprint = restorePrintButton;
            });

            editButton.addEventListener('click', function() {
                contratoDisplay.style.display = 'none';
                contratoTextarea.style.display = 'block';
                buttonsContainer.style.display = 'block';
                cancelButton.style.display = 'block';
                editButton.style.display = 'none';
                printButton.style.display = 'none';
            });

            cancelButton.addEventListener('click', function() {
                contratoDisplay.style.display = 'block';
                printButton.style.display = 'inline-block';
                contratoTextarea.style.display = 'none';
                cancelButton.style.display = 'none';
                buttonsContainer.style.display = 'none';
                editButton.style.display = 'inline-block';
                restorePrintButton(); 
            });


            saveButton.addEventListener('click', function() {
                const contratoTexto = contratoTextarea.value;
                const contratoData = {
                    id_aluno: <?= $id_aluno; ?>,
                    texto_contrato: contratoTexto
                };

                fetch('salvar_contrato.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(contratoData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Contrato salvo com sucesso!');
                        contratoDisplay.textContent = contratoTexto;
                        contratoDisplay.style.display = 'block';
                        contratoTextarea.style.display = 'none';
                        buttonsContainer.style.display = 'none';
                        cancelButton.style.display = 'none';
                        editButton.style.display = 'inline-block';
                        printButton.style.display = 'inline-block';
                        restorePrintButton(); 
                    } else {
                        alert('Erro ao salvar contrato.');
                    }
                })
                .catch(error => {
                    console.error('Erro ao salvar contrato:', error);
                });
            });
        </script>
        <!-- End Scripts -->

    </body>
</html>
