<?php

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    include "conexao.php";

    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    try {
        $sqlSelect = "SELECT foto FROM alunos WHERE id = ?";
        $stmtSelect = $conn->prepare($sqlSelect);
        $stmtSelect->execute([$id]);
        $row = $stmtSelect->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $imagemAntiga = $row['foto'];

            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['image']['tmp_name'];

                $fileName = $_FILES['image']['name'];

                $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = uniqid() . '.' . $fileExtension;

                $uploadFileDir = 'uploads/';
                $dest_path = $uploadFileDir . $newFileName;

                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0755, true);
                }

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    if (!empty($imagemAntiga) && file_exists($imagemAntiga)) {
                        unlink($imagemAntiga);
                    }

                    $caminhoCompleto = $dest_path;

                    $sqlUpdate = "UPDATE alunos SET foto = ? WHERE id = ?";
                    $stmtUpdate = $conn->prepare($sqlUpdate);
                    $stmtUpdate->execute([$caminhoCompleto, $id]);

                    if ($stmtUpdate->rowCount() > 0) {
                        echo "<script>alert('Imagem enviada e atualizada com sucesso!'); window.location.href='dadosAluno.php?id={$id}';</script>";
                    } else {
                        echo "Erro ao atualizar a imagem no banco de dados.";
                    }
                } else {
                    echo "Erro ao mover o arquivo.";
                }
            } else {
                echo "Erro no upload da imagem.";
            }
        } else {
            echo "Aluno não encontrado.";
        }
    } catch (PDOException $e) {
        echo "Erro: " . $e->getMessage();
    }
}

?>
