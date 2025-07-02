<?php

include 'conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Captura os valores do formulário
        $nome = $_POST['nome'] ?? '';
        $preco = $_POST['preco'] ?? 0;
        $parcelado = $_POST['parcelado'] ?? '';
        $status = $_POST['status'] ?? '';
        $action = $_POST['action'] ?? '';
        $imagem = null;

        // Verifica se foi enviado um arquivo de imagem
        if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] == 0) {
            $target_dir = "../uploads/"; // Diretório para salvar as imagens
            $imagem_nome = basename($_FILES["imagem"]["name"]); // Nome do arquivo enviado pelo usuário
            $imagem_caminho = $target_dir . $imagem_nome; // Caminho completo para salvar no banco

            // Cria o diretório, se necessário
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            // Move o arquivo para o diretório
            if (move_uploaded_file($_FILES["imagem"]["tmp_name"], $imagem_caminho)) {
                $imagem = $imagem_caminho; // Salva o caminho completo no banco
            } else {
                echo "<script>alert('Erro ao fazer upload da imagem.'); history.back();</script>";
                exit;
            }
        }

        // Prepara a consulta com base na ação
        if ($action === 'adicionar') {
            $sql = "INSERT INTO categorias (nome, preco, parcelado, status, imagem) 
                    VALUES (:nome, :preco, :parcelado, :status, :imagem)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':parcelado', $parcelado);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':imagem', $imagem);

        } elseif ($action === 'atualizar') {
            // Atualiza a categoria, incluindo imagem apenas se enviada
            $sql = "UPDATE categorias SET 
                        preco = :preco, 
                        parcelado = :parcelado, 
                        status = :status" .
                        ($imagem ? ", imagem = :imagem" : "") . 
                    " WHERE nome = :nome";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':preco', $preco);
            $stmt->bindParam(':parcelado', $parcelado);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':nome', $nome);

            if ($imagem) {
                $stmt->bindParam(':imagem', $imagem);
            }
        } else {
            echo "<script>alert('Ação inválida.'); history.back();</script>";
            exit;
        }

        // Executa a consulta
        $stmt->execute();

        echo "<script>alert('Categoria $action com sucesso!'); history.back();</script>";

    } catch (PDOException $e) {
        echo "<script>alert('Erro ao processar a solicitação: " . $e->getMessage() . "'); history.back();</script>";
    }
} else {
    echo "<script>alert('Nenhum dado foi enviado.'); history.back();</script>";
}

?>
