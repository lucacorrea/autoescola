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

    // Captura o ID do aluno, se disponível
    $aluno_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    // Variáveis para armazenar os dados do aluno
    $categoria_aluno = '';
    $pago_aluno = '';
    $nome_aluno = '';
    $servico_aluno = '';
    $forma_pagamento_aluno = '';
    $preco_aluno = '';
    $numero_parcelas_aluno = '';
    $valor_entrada = '';

    // Se um ID válido foi fornecido, busque os dados do aluno
    if ($aluno_id > 0) {
        include "conexao.php";

        try {
            // Query para buscar os dados do aluno
            $sql = "SELECT nome FROM alunos WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id', $aluno_id, PDO::PARAM_INT);
            $stmt->execute();

            // Verifica se o aluno foi encontrado
            if ($stmt->rowCount() > 0) {
                $nome_aluno = $stmt->fetchColumn();
            } else {
                echo "Aluno não encontrado.";
            }
        } catch (PDOException $e) {
            echo "Erro ao buscar os dados do aluno: " . $e->getMessage();
        }
    }

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Alunos</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css">
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
</head>
<body>
    <div class="container-mensagens" id="container-mensagens"></div>
    <div class="loader-full animated fadeIn hidden">
        <img src="../img/loader.png" width="100" class="animated pulse infinite" />
    </div>
    <section class="bg-menu">
        <div class="conteudo">
            <section class="menu-top">
                <div class="container">
                    <div class="row">
                        <div style="margin-left: -70px;" class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page fas fa-users"><b>&nbsp; CADASTRAR SERVIÇO - ALUNOS</b></h1>
                            <div class="container-right">
                                <div class="container-dados"></div>
                                <a href="alunos.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Voltar
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="conteudo-inner">
                <div class="container">
                    <div class="row">
                        <div style="margin-left: -90px;" class="col-12 mt-5 tab-item" id="categoria">
                            <div class="col-12" id="categorias">
                                <form id="formAssociado" action="processarServicoAluno.php" method="POST" style="zoom: 95%;">
                                    <div class="container-group mb-5">
                                        <div class="col-12 mb-4 card card-form socio">
                                            <div class="row">
                                            <div class="col-6 mb-2">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-2"><b>Serviço:</b></p>
                                                        <input type="text" name="servico_aluno" class="form-control mb-2" value="<?php echo htmlspecialchars($categoria_aluno); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                <p class="title-categoria mb-2"><b>Pago:</b></p>
                                                <select name="pago_aluno" class="form-select form-control" aria-label="Default select example" id="pague">
                                                    <option value="NÃO" <?php echo $pago_aluno == 'NÃO' ? 'selected' : ''; ?>>NÃO</option>
                                                    <option value="SIM" <?php echo $pago_aluno == 'SIM' ? 'selected' : ''; ?>>SIM</option>
                                                </select>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-2"><b>Nome Completo:</b></p>
                                                        <input type="text" name="nome_aluno" class="form-control mb-2" value="<?php echo htmlspecialchars($nome_aluno); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                    </div>
                                                </div>
                                                
                                                <div class="col-6 mb-2">
                                                    <div class="form-group">
                                                        <p class="title-categoria mb-1"><b>Categoria:</b></p>
                                                        <select name="categoria_aluno" class="form-select form-control" aria-label="Default select example" id="pague">
                                                            <option selected value="...">Selecione a categoria:</option>
                                                            <option value="A" <?php echo $categoria_aluno == 'A' ? 'selected' : ''; ?>>A</option>
                                                            <option value="B" <?php echo $categoria_aluno == 'B' ? 'selected' : ''; ?>>B</option>
                                                            <option value="AB" <?php echo $categoria_aluno == 'AB' ? 'selected' : ''; ?>>AB</option>
                                                            <option value="A/AB" <?php echo $categoria_aluno == 'A/AB' ? 'selected' : ''; ?>>A/AB</option>
                                                            <option value="B/AB" <?php echo $categoria_aluno == 'B/AB' ? 'selected' : ''; ?>>B/AB</option>
                                                            <option value="D" <?php echo $categoria_aluno == 'D' ? 'selected' : ''; ?>>D</option>
                                                            <option value="A/D" <?php echo $categoria_aluno == 'A/D' ? 'selected' : ''; ?>>A/D</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <div class="form-group">
                                                        <p class="title-categoria mb-1"><b>Forma de Pagamento:</b></p>

                                                        <?php

                                                            include "conexao.php";

                                                            try {
                                                                // Query para buscar as formas de pagamento
                                                                $sql = "SELECT id, CASE WHEN forma = 'Carnê' THEN 'Parcelado' ELSE forma END AS forma FROM formas_pagamento";
                                                                $stmt = $conn->query($sql);

                                                                if ($stmt->rowCount() > 0) {
                                                                    echo '<select name="forma_pagamento_aluno" class="form-select form-control" aria-label="Default select example" id="paymentSelect">';
                                                                    echo '<option selected value="...">Selecione a Forma de Pagamento:</option>';
                                                                    
                                                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                                                        $selected = (isset($forma_pagamento_aluno) && $forma_pagamento_aluno == $row['forma']) ? 'selected' : '';
                                                                        echo '<option value="' . htmlspecialchars($row['forma'], ENT_QUOTES, 'UTF-8') . '" ' . $selected . '>' . htmlspecialchars($row['forma'], ENT_QUOTES, 'UTF-8') . '</option>';
                                                                    }
                                                                    
                                                                    echo '</select>';
                                                                } else {
                                                                    echo '<p>Nenhuma forma de pagamento encontrada.</p>';
                                                                }
                                                            } catch (PDOException $e) {
                                                                echo "Erro ao buscar as formas de pagamento: " . $e->getMessage();
                                                            }

                                                        ?>

                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-1"><b>Preços:</b></p>
                                                        <input type="text" name="preco_aluno" class="form-control" value="<?php echo htmlspecialchars($preco_aluno); ?>" oninput="this.value = this.value.toUpperCase()" />
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2 entradaa hidden" id="entradaaDiv">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-1"><b>Número de Parcelas:</b></p>
                                                        <input name="numero_parcelas_aluno" type="text" class="form-control" id="parcelas" value="<?php echo htmlspecialchars($numero_parcelas_aluno); ?>" placeholder="Digite o número de parcelas" />
                                                    </div>
                                                </div>
                                                <div class="col-6 mb-2 entradaa hidden" id="valorEntradaDiv">
                                                    <div class="form-group container-cep">
                                                        <p class="title-categoria mb-1"><b>Valor de Entrada:</b></p>
                                                        <input type="text" class="form-control" name="valor_entrada" id="entradaValor" value="<?php echo htmlspecialchars($valor_entrada); ?>" placeholder="Digite o valor de entrada" />
                                                    </div>
                                                </div>
                                                <div class="col-4 hidden" id="vaziaDiv"></div>
                                                <div class="col-12 text-right">
                                            
                                                    <button type="submit" class="btn btn-yellow btn-sm btn-proximo mt-4" style="float:right;">
                                                        Atualizar &nbsp;<i class="fas fa-save"></i>
                                                    </button>
                                            
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="aluno_id" value="<?php echo $aluno_id; ?>">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </section>

    <script src="../js/jquery.min.js"></script>
    <script src="../js/popper.min.js"></script>
    <script src="../js/bootstrap.min.js"></script>
    <script src="../js/fontawesome.js"></script>
    <script src="../js/simplyCountdown.min.js"></script>
    <script>

        $(document).ready(function() {
            $('#paymentSelect').change(function() {
                var selectedOption = $(this).val();
                if (selectedOption == 'Parcelado') {
                    $('#valorEntradaDiv').removeClass('hidden');
                    $('#entradaaDiv').removeClass('hidden');
                    $('#vaziaDiv').removeClass('hidden');
                } else {
                    $('#valorEntradaDiv').addClass('hidden');
                    $('#entradaaDiv').addClass('hidden');
                    $('#vaziaDiv').addClass('hidden');
                }
            });
        });

    </script>
    
</body>

</html>
