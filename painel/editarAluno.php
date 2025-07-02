<?php

    //----------SESSION---------------

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

    //----------END SESSION-----------

    //----------SESSION LISTAGEM-------

        include "conexao.php";


        $id_aluno = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $sql = "
            SELECT a.*, s.categoria, s.forma_pagamento, s.valor_entrada, s.preco, s.numero_parcelas, s.data_pagamento, l.email, l.senha_hash
            FROM alunos a
            LEFT JOIN servicos_aluno s ON a.nome = s.nome_aluno
            LEFT JOIN login_aluno l ON a.nome = l.nome_aluno
            WHERE a.id = :id_aluno
        ";

        $stmt = $conn->prepare($sql);

        $stmt->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);

        $stmt->execute();

        if ($stmt->rowCount() === 0) {
            die("Aluno não encontrado.");
        }

        $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

        $stmt->closeCursor();
        $conn = null;

    //----------END SESSION LISTAGEM---

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <title>Painel - Alunos</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css">
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/painel.css" />
        <!-- End custom CSS -->

    </head>

    <body>

        <div class="container-mensagens" id="container-mensagens"></div>

        <!-- section -->
        <section class="bg-menu">

            <!-- conteudo -->
            <div class="conteudo" style="margin-left: -240px;">

                <!-- menu-top -->
                <section class="menu-top">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center mt-4">
                                <h1 class="title-page fas fa-users"><b>&nbsp; EDITAR ALUNO</b></h1>
                                <div class="container-right">
                                    <div class="container-dados"></div>
                                    <a href="alunos.php" class="btn btn-white btn-sm">
                                        <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <!-- End menu-top -->

                <!-- conteudo-inner -->
                <section class="conteudo-inner">

                    <!-- container -->
                    <div class="container">

                        <div class="row">

                            <div class="col-12 mt-5 tab-item" id="categoria">

                                <div class="col-12" id="categorias">

                                    <!-- form -->
                                    <form id="formAssociado" action="updateAluno.php" method="POST" style="zoom: 93%;" enctype="multipart/form-data">
                                        
                                        <div class="container-group mb-5">

                                            <!-- Parte 1 -->
                                            <div class="col-12 mb-4 card card-form socio">

                                                <div class="row">
                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Nome Completo:</b></p>
                                                            <input type="text" name="nome_aluno" class="form-control mb-2" value="<?php echo htmlspecialchars($aluno['nome']); ?>" oninput="this.value = this.value.toUpperCase()" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>RG:</b></p>
                                                            <input type="text" name="rg_aluno" class="form-control mb-2" value="<?php echo htmlspecialchars($aluno['rg']); ?>" oninput="this.value = this.value.toUpperCase()" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>CPF:</b></p>
                                                            <input type="text" name="cpf_aluno" class="form-control mb-2" value="<?php echo htmlspecialchars($aluno['cpf']); ?>" oninput="this.value = this.value.toUpperCase()" required />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Data de Nascimento:</b></p>
                                                            <input type="date" name="data_nascimento_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['data_nascimento']); ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Telefone:</b></p>
                                                            <input type="text" name="telefone_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['telefone']); ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Renach AM:</b></p>
                                                            <input type="text" name="renach_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['renach']); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>L.A.D.V:</b></p>
                                                            <input type="date" name="ladv_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['ladv']); ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Vencimento do Processo:</b></p>
                                                            <input type="date" name="vencimento_processo_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['vencimento_processo']); ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Rua:</b></p>
                                                            <input type="text" name="rua_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['rua']); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Bairro:</b></p>
                                                            <input type="text" name="bairro_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['bairro']); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Número:</b></p>
                                                            <input type="text" name="numero_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['numero']); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Observação:</b></p>
                                                            <input type="text" name="observacao_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['observacao']); ?>" oninput="this.value = this.value.toUpperCase()"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-12 text-right">
                                                        <button type="button" class="btn btn-yellow next btn-sm btn-proximo mt-4" style="float:right;">
                                                            Próximo &nbsp;<i class="fas fa-arrow-right"></i>
                                                        </button>
                                                    </div>

                                                </div>
                                            </div>
                                            <!-- End parte 1 -->
                                        
                                            <!-- Parte 2 -->
                                            <div class="col-12 mb-4 card card-form hidden socio">
                                                <div class="row">
                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <?php if (!empty($aluno['documento']) && file_exists('uploads/documentos/' . basename($aluno['documento']))): ?>
                                                                <p>Documento Atual: <br><a class="link" href="uploads/documentos/<?php echo htmlspecialchars(basename($aluno['documento'])); ?>" target="_blank"><?php echo htmlspecialchars(basename($aluno['documento'])); ?></a></p>
                                                            <?php else: ?>
                                                                <p>Nenhum documento encontrado.</p>
                                                            <?php endif; ?>
                                                            <p class="title-categoria mb-1"><b>Documento do Candidato:</b></p>
                                                            <input type="file" class="form-control" id="documento_novo" name="documento_novo" accept=".pdf, .doc, .docx, .jpg, .png" >
                                                        </div>

                                                    </div>

                                                    <div class="col-4 mb-2">
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Categoria:</b></p>
                                                            <select name="categoria" class="form-control">
                                                            <option selected value="">Selecione a categoria:</option>
                                                                <option value="A" <?php echo htmlspecialchars($aluno['categoria']) == 'A' ? 'selected' : ''; ?>>A</option>
                                                                <option value="B" <?php echo htmlspecialchars($aluno['categoria']) == 'B' ? 'selected' : ''; ?>>B</option>
                                                                <option value="AB" <?php echo htmlspecialchars($aluno['categoria']) == 'AB' ? 'selected' : ''; ?>>AB</option>
                                                                <option value="A/AB" <?php echo htmlspecialchars($aluno['categoria']) == 'A/AB' ? 'selected' : ''; ?>>A/AB</option>
                                                                <option value="B/AB" <?php echo htmlspecialchars($aluno['categoria']) == 'B/AB' ? 'selected' : ''; ?>>B/AB</option>
                                                                <option value="D" <?php echo htmlspecialchars($aluno['categoria']) == 'D' ? 'selected' : ''; ?>>D</option>
                                                                <option value="A/D" <?php echo htmlspecialchars($aluno['categoria']) == 'A/D' ? 'selected' : ''; ?>>A/D</option>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group">
                                                            <p class="title-categoria mb-1"><b>Forma de Pagamento:</b></p>
                                                            <?php

                                                                include "conexao.php";

                                                                $sql = "SELECT id, CASE WHEN forma = 'Carnê' THEN 'Parcelado' ELSE forma END AS forma FROM formas_pagamento";
                                                                $stmt = $conn->prepare($sql);
                                                                $stmt->execute();

                                                                if ($stmt->rowCount() > 0) {
                                                                    echo '<select name="forma_pagamento_aluno" class="form-select form-control" aria-label="Default select example">';
                                                                    echo '<option selected value="">Selecione a Forma de Pagamento:</option>';

                                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                        echo '<option value="' . $row['forma'] . '" ' . ($aluno['forma_pagamento'] == $row['forma'] ? 'selected' : '') . '>' . $row['forma'] . '</option>';
                                                                    }

                                                                    echo '</select>';
                                                                } else {
                                                                    echo '<p>Nenhuma forma de pagamento encontrada.</p>';
                                                                }

                                                                $stmt->closeCursor();
                                                                $conn = null;

                                                            ?>

                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group">
                                                            <p class="title-categoria mb-1"><b>Valor de Entrada:</b></p>
                                                            <input type="text" name="valor_entrada_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['valor_entrada']); ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group">
                                                            <p class="title-categoria mb-1"><b>Preço:</b></p>
                                                            <input type="text" name="preco_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['preco']); ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group">
                                                            <p class="title-categoria mb-1"><b>Número de Parcelas:</b></p>
                                                            <input type="number" name="numero_parcelas_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['numero_parcelas']); ?>" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group">
                                                            <p class="title-categoria mb-1"><b>Data de Pagamento:</b></p>
                                                            <input type="date" name="data_pagamento_aluno" class="form-control" value="<?php echo htmlspecialchars($aluno['data_pagamento']); ?>" />
                                                        </div>
                                                    </div> 

                                                    <div class="col-12 text-right">

                                                        <button type="button" class="btn btn-yellow volta btn-sm btn-anterior mt-4" style="float:left;">
                                                            <i class="fas fa-arrow-left"></i>&nbsp; Anterior
                                                        </button>

                                                        <button type="submit" class="btn btn-yellow btn-sm btn-proximo mt-4" style="float:right;">
                                                            Atualizar &nbsp;<i class="fas fa-save"></i>
                                                        </button>

                                                    </div>

                                                </div>
                                            </div>
                                            <!-- End parte 2 -->

                                        </div>

                                        <input type="hidden" name="id_aluno" value="<?php echo htmlspecialchars($id_aluno); ?>" />

                                    </form>
                                    <!-- End form -->

                                </div>

                            </div>

                        </div>

                    </div>
                    <!-- End container -->

                </section>
                <!-- End conteudo-inner -->

            </div>
            <!-- End conteudo -->

        </section>
        <!-- End Section -->

        <!-- Scripts -->
        <script src="../js/jquery-3.6.0.min.js"></script>
        <script src="../js/bootstrap.bundle.min.js"></script>
        <script src="../js/script.js"></script>
        <script>

            document.querySelectorAll('.next').forEach(button => {
                button.addEventListener('click', function() {
                    let currentStep = this.closest('.socio');
                    let nextStep = currentStep.nextElementSibling;
                    currentStep.classList.add('hidden');
                    nextStep.classList.remove('hidden');
                });
            });

            document.querySelectorAll('.volta').forEach(button => {
                button.addEventListener('click', function() {
                    let currentStep = this.closest('.socio');
                    let prevStep = currentStep.previousElementSibling;
                    currentStep.classList.add('hidden');
                    prevStep.classList.remove('hidden');
                });
            });

        </script>
        <!-- End Scripts -->

    </body>
</html>

