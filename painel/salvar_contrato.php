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


//-------------------SAVE DB------------------------

    include "conexao.php";

    try {
        $data = json_decode(file_get_contents("php://input"), true);

        $id_aluno = $data['id_aluno'];
        $texto_contrato = $data['texto_contrato'];

        $sql_verifica = "SELECT * FROM contratos WHERE id_aluno = :id_aluno";
        $stmt_verifica = $conn->prepare($sql_verifica);
        $stmt_verifica->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
        $stmt_verifica->execute();

        if ($stmt_verifica->rowCount() > 0) {
            $sql_update = "UPDATE contratos SET texto_contrato = :texto_contrato WHERE id_aluno = :id_aluno";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bindParam(':texto_contrato', $texto_contrato, PDO::PARAM_STR);
            $stmt_update->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
            $success = $stmt_update->execute();
        } else {
            $sql_insert = "INSERT INTO contratos (id_aluno, texto_contrato) VALUES (:id_aluno, :texto_contrato)";
            $stmt_insert = $conn->prepare($sql_insert);
            $stmt_insert->bindParam(':id_aluno', $id_aluno, PDO::PARAM_INT);
            $stmt_insert->bindParam(':texto_contrato', $texto_contrato, PDO::PARAM_STR);
            $success = $stmt_insert->execute();
        }

        $response = array("success" => $success);
        echo json_encode($response);
    } catch (PDOException $e) {
        $response = array("success" => false, "error" => $e->getMessage());
        echo json_encode($response);
    }

//-------------------END SAVE DB--------------------

?>
