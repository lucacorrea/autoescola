<?php

//-------------------SESSION------------------------

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

//-------------------END SESSION--------------------


//------------------SESSION IMAGE EMPRESA-----------

    include 'conexao.php';

    $id = 1; 

    $sql = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);


    $logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";

//------------------END SESSION IMAGE EMPRESA-------


//------------------SESSION USER--------------------

    $id_usuario = $_SESSION['id_usuario'];
    $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();


    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        $nome_usuario = $usuario["nome"];
        $email_usuario = $usuario["email"];
    } else {
        echo "Nenhum resultado encontrado.";
    }

//------------------END SESSION USER----------------

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
        <link rel="stylesheet" href="./css/input.css">
        <!-- End custom CSS -->
    
    </head>
    
    <body>

        <div class="container-mensagens" id="container-mensagens">
        </div>

        <!-- Section -->
        <section class="bg-menu">

            <!-- menu-left -->
            <div class="menu-left">

                <div class="logo">

                    <?php if (!empty($logoImage)): ?>
                        <img class="logo-admin" src="uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" width="100" alt="Logo">
                    <?php else: ?>
                        
                    <?php endif; ?>

                </div>

                <div class="menus">

                    <a href="./home.php" class="menu-item">
                        <i class="fas fa-home"></i> Início
                    </a>

                    <a href="./feriadosCadastrados.php" class="menu-item">
                        <i class="fas fa-calendar-alt"></i> Feriados
                    </a>

                    <a href="./legislacao.php" class="menu-item">
                        <i class="fas fa-book-open"></i> Legislação
                    </a>

                    <a href="./alunos.php" class="menu-item active">
                        <i class="fas fa-users"></i> Alunos
                    </a>

                    <a href="./instrutores.php" class="menu-item">
                        <i class="fas fa-chalkboard-teacher"></i> Instrutores/Placa
                    </a>

                    <a href="./configuracoes.php" class="menu-item">
                        <i class="fas fa-cog"></i> Configurações
                    </a>

                    <a href="./relatorio.php" class="menu-item">
                        <i class="fas fa-donate"></i> Financeiro
                    </a>

                    <a href="./empresa.php" class="menu-item">
                        <i class="fas fa-building"></i> Empresa
                    </a>

                </div>

            </div>
            <!-- End menu-left -->

            <!-- conteudo -->
            <div class="conteudo">

                <!-- menu-top -->
                <section class="menu-top">

                    <div class="container">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center mt-4">
                                <h1 class="title-page fas fa-users"><b>&nbsp; PAINEL - ALUNOS</b></h1>
                                <div class="container-right">
                                    <div class="container-dados">
                                        <p><?php echo $nome_usuario; ?></p>
                                        <?php if ($email_usuario) { ?>
                                        <span><?php echo $email_usuario; ?></span>
                                        <?php } ?>
                                    </div>

                                    <a href="logout.php" class="btn btn-white btn-sm" onclick="return confirm('Tem certeza que deseja sair?')">
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
                            <div class="col-12 mt-0">

                                <div class="menus-config">
                                    <a href="alunos.php" class="btn btn-white btn-sm">
                                        <i class="fas fa-users"></i> Alunos Cadastrados
                                    </a>
                                    <a href="cadastroAluno.php" class="btn btn-white btn-sm active">
                                        <i class="fas fa-user-plus"></i> Cadastrar Aluno
                                    </a>
                                
                                </div>

                            </div>

                            <div class="col-12 mt-5 tab-item" id="categoria">

                                <div class="col-12" id="categorias">

                                    <!-- form -->
                                    <form id="formAssociado" action="processarAluno.php" method="POST" style="zoom: 95%;" enctype="multipart/form-data">
                                        
                                        <!-- container-group -->
                                        <div class="container-group mb-5">

                                            <!-- Parte 1 -->
                                            <div class="col-12 mb-4 card card-form socio">
                                                <div class="row">
                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-2"><b>Nome Completo:</b><span class="badge-obrigatorio">Obrigatório <span class="sinal">*</span></span></p>
                                                            <input type="text" name="nome_aluno" class="form-control mb-2" oninput="this.value = this.value.toUpperCase()"  />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>RG:</b><span class="badge-obrigatorio">Obrigatório <span class="sinal">*</span></span></p>
                                                            <input type="text" name="rg_aluno" class="form-control mb-2" oninput="this.value = this.value.toUpperCase()"  />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>CPF:</b><span class="badge-obrigatorio">Obrigatório <span class="sinal">*</span></span></p>
                                                            <input type="text" name="cpf_aluno" class="form-control mb-2" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-3">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Data de Nascimento:</b><span class="badge-obrigatorio">Obrigatório <span class="sinal">*</span></span></p>
                                                            <input type="date" name="data_nascimento_aluno" class="form-control" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-3">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Telefone:</b></p>
                                                            <input type="text" name="telefone_aluno" class="form-control" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-3">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Renach AM:</b><span class="badge-obrigatorio">Obrigatório <span class="sinal">*</span></span></p>
                                                            <input type="text" name="renach_aluno" class="form-control" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-3">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>L.A.D.V:</b></p>
                                                            <input type="date" name="ladv_aluno" class="form-control" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-3">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Vencimento do Processo:</b><span class="badge-obrigatorio">Obrigatório <span class="sinal">*</span></span></p>
                                                            <input type="date" name="vencimento_processo_aluno" class="form-control" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-3">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Rua:</b><span class="badge-obrigatorio">Obrigatório <span class="sinal">*</span></span></p>
                                                            <input type="text" name="rua_aluno" class="form-control" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Bairro:</b><span class="badge-obrigatorio">Obrigatório <span class="sinal">*</span></span></p>
                                                            <input type="text" name="bairro_aluno" class="form-control" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Número:</b><span class="badge-obrigatorio">Obrigatório <span class="sinal">*</span></span></p>
                                                            <input type="text" name="numero_aluno" class="form-control" oninput="this.value = this.value.toUpperCase()" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Observação:</b></p>
                                                            <input type="text" name="observacao_aluno" class="form-control" oninput="this.value = this.value.toUpperCase()" />
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
                                                            <p class="title-categoria mb-1"><b>Foto do Candidato:</b></p>
                                                            <input type="file" name="foto_aluno" class="custom-file-input" accept="image/*"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Documentos do Candidato:</b></p>
                                                            <input type="file" name="documento_aluno" class="custom-file-input"/>
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                    </div>
                                                    
                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Email do Candidato:</b></p>
                                                            <input type="email" name="email_aluno" class="form-control" id="entradaValor" placeholder="Digite o seu email" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Senha:</b></p>
                                                            <input type="password" name="senha_aluno" class="form-control" id="entradaValor" placeholder="Digite sua senha" />
                                                        </div>
                                                    </div>

                                                    <div class="col-4 mb-2">
                                                        <div class="form-group container-cep">
                                                            <p class="title-categoria mb-1"><b>Confirmar Senha:</b></p>
                                                            <input type="password" name="confirmar_senha_aluno" class="form-control" id="entradaValor" placeholder="Confirme a sua senha" />
                                                        </div>
                                                    </div>

                                                    <div class="col-12 text-right">
                                                        <button type="button" class="btn btn-yellow btn-sm btn-proximo mt-4 volta">
                                                            <i class="fas fa-arrow-left"></i> &nbsp; Anterior
                                                        </button>
                                                        <button type="submit" class="btn btn-yellow btn-sm mt-4 btn-proximo" style="float:right;">
                                                            <i class="fas fa-check"></i> &nbsp; Finalizar
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- End parte 2 -->

                                        </div>
                                        <!-- End container-group -->

                                    </form>
                                    <!-- End form -->

                                </div>

                            </div>

                        </div>
                    </div>
                    <!-- End container -->

                </section>
                <!-- End container-inner -->

            </div>

        </section>
        <!-- End Section -->

        <!-- Scripts -->
        <script src="../js/jquery-3.6.0.min.js"></script>
        <script src="../js/bootstrap.min.js"></script>
        <script src="../js/jquery.mask.min.js"></script>
        <script src="../js/sweetalert2.all.min.js"></script>
        <script src="./js/visualizarSocios.js"></script>
        <script src="./js/ajax.js"></script>
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
