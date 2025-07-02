<?php

    //-------------SESSION-----------------------

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

    //-------------END SESSION-------------------


    //-------------SESSION PROCESSAMENTO--------

        include "conexao.php";

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_aluno'])) {
            $id_aluno = intval($_POST['id_aluno']);

            $sql_atual = "SELECT nome, documento FROM alunos WHERE id = ?";
            $stmt_atual = $conn->prepare($sql_atual);
            $stmt_atual->execute([$id_aluno]);
            $row = $stmt_atual->fetch(PDO::FETCH_ASSOC);
            $nome_atual = $row['nome'];
            $documento_atual = $row['documento'];

            $novo_nome = isset($_POST['nome_aluno']) ? $_POST['nome_aluno'] : $nome_atual;
            $rg = isset($_POST['rg_aluno']) ? $_POST['rg_aluno'] : '';
            $cpf = isset($_POST['cpf_aluno']) ? $_POST['cpf_aluno'] : '';
            $data_nascimento = isset($_POST['data_nascimento_aluno']) ? $_POST['data_nascimento_aluno'] : null;
            $telefone = isset($_POST['telefone_aluno']) ? $_POST['telefone_aluno'] : '';
            $renach = isset($_POST['renach_aluno']) ? $_POST['renach_aluno'] : '';
            $ladv = !empty($_POST['ladv_aluno']) ? $_POST['ladv_aluno'] : null;
            $vencimento_processo = isset($_POST['vencimento_processo_aluno']) ? $_POST['vencimento_processo_aluno'] : null;
            $rua = isset($_POST['rua_aluno']) ? $_POST['rua_aluno'] : '';
            $bairro = isset($_POST['bairro_aluno']) ? $_POST['bairro_aluno'] : '';
            $numero = isset($_POST['numero_aluno']) ? $_POST['numero_aluno'] : '';
            $observacao = isset($_POST['observacao_aluno']) ? $_POST['observacao_aluno'] : '';

            $categoria = isset($_POST['categoria']) ? $_POST['categoria'] : '';
            $forma_pagamento = isset($_POST['forma_pagamento_aluno']) ? $_POST['forma_pagamento_aluno'] : '';
            $valor_entrada = isset($_POST['valor_entrada_aluno']) ? $_POST['valor_entrada_aluno'] : '';
            $preco = isset($_POST['preco_aluno']) && $_POST['preco_aluno'] !== '' ? $_POST['preco_aluno'] : '0.00';
            $numero_parcelas = isset($_POST['numero_parcelas_aluno']) ? intval($_POST['numero_parcelas_aluno']) : 1;
            $data_pagamento = !empty($_POST['data_pagamento_aluno']) ? $_POST['data_pagamento_aluno'] : null;

            if (isset($_FILES['documento_novo']) && $_FILES['documento_novo']['error'] === UPLOAD_ERR_OK) {
                $diretorio = 'uploads/documentos/';
                
                $nome_arquivo = $_FILES['documento_novo']['name'];
                $nome_arquivo_normalizado = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $nome_arquivo);
                $nome_arquivo_normalizado = preg_replace('/[^a-zA-Z0-9_.-]/', ' ', $nome_arquivo_normalizado);
                
                $documento_novo = $diretorio . basename($nome_arquivo_normalizado);
            
                if (!empty($documento_atual) && file_exists($documento_atual)) {
                    unlink($documento_atual);
                }
            
                if (!move_uploaded_file($_FILES['documento_novo']['tmp_name'], $documento_novo)) {
                    die("Erro ao fazer upload do arquivo.");
                }
            } else {
                $documento_novo = $documento_atual;
            }    

            $conn->beginTransaction();

            try {
                $sql_aluno = "
                    UPDATE alunos 
                    SET nome = ?, rg = ?, cpf = ?, data_nascimento = ?, telefone = ?, renach = ?, ladv = ?, vencimento_processo = ?, rua = ?, bairro = ?, numero = ?, observacao = ?, documento = ? 
                    WHERE id = ?
                ";

                $stmt_aluno = $conn->prepare($sql_aluno);
                $stmt_aluno->execute([$novo_nome, $rg, $cpf, $data_nascimento, $telefone, $renach, $ladv, $vencimento_processo, $rua, $bairro, $numero, $observacao, $documento_novo, $id_aluno]);

                if ($novo_nome !== $nome_atual) {
                    $sql_update_servico = "
                        UPDATE servicos_aluno 
                        SET nome_aluno = ? 
                        WHERE nome_aluno = ?
                    ";
                    $stmt_servico = $conn->prepare($sql_update_servico);
                    $stmt_servico->execute([$novo_nome, $nome_atual]);

                    $sql_update_login = "
                        UPDATE login_aluno 
                        SET nome_aluno = ? 
                        WHERE nome_aluno = ?
                    ";
                    $stmt_login = $conn->prepare($sql_update_login);
                    $stmt_login->execute([$novo_nome, $nome_atual]);

                    $sql_update_parcelas = "
                        UPDATE info_parcelas 
                        SET nome_aluno = ? 
                        WHERE nome_aluno = ?
                    ";
                    $stmt_parcelas = $conn->prepare($sql_update_parcelas);
                    $stmt_parcelas->execute([$novo_nome, $nome_atual]);

                    $sql_update_fichas = "
                        UPDATE fichas 
                        SET nome = ? 
                        WHERE nome = ?
                    ";
                    $stmt_fichas = $conn->prepare($sql_update_fichas);
                    $stmt_fichas->execute([$novo_nome, $nome_atual]);

                    $sql_update_relatorios = "
                        UPDATE relatorios 
                        SET nome_aluno = ? 
                        WHERE nome_aluno = ?
                    ";
                    $stmt_relatorios = $conn->prepare($sql_update_relatorios);
                    $stmt_relatorios->execute([$novo_nome, $nome_atual]);

                    $sql_update_turmas = "
                        UPDATE alunos_turmas 
                        SET nome_aluno = ? 
                        WHERE nome_aluno = ?
                    ";
                    $stmt_turmas = $conn->prepare($sql_update_turmas);
                    $stmt_turmas->execute([$novo_nome, $nome_atual]);
                }

                $sql_categoria = "
                    UPDATE servicos_aluno 
                    SET categoria = ?, forma_pagamento = ?, valor_entrada = ?, preco = ?, numero_parcelas = ?, data_pagamento = ? 
                    WHERE nome_aluno = ?
                ";

                $stmt_categoria = $conn->prepare($sql_categoria);
                $stmt_categoria->execute([$categoria, $forma_pagamento, $valor_entrada, $preco, $numero_parcelas, $data_pagamento, $novo_nome]);

                $conn->commit();
                echo "<script>alert('Dados Atualizados com sucesso!'); window.location.href='alunos.php';</script>";
            } catch (Exception $e) {
                $conn->rollBack();
                echo "Erro ao atualizar: " . $e->getMessage();
            }
        }

        $conn = null;

    //-------------END SESSION PROCESSAMENTO----

?>
