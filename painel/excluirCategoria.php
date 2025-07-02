<?php
session_start();

// Verificar se o usuário tem permissão para excluir
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel'];
        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true;
        }
    }
    header("Location: loader.php");
    exit();
}

verificarAcesso();

// Inclui a conexão com o banco de dados
include 'conexao.php';

try {
    // Verificar se o ID foi informado
    if (isset($_GET['id'])) {
        $id_categoria = intval($_GET['id']);

        // Buscar o nome da imagem para exclusão
        $sqlImagem = "SELECT imagem FROM categorias WHERE id_categoria = :id_categoria";
        $stmtImagem = $conn->prepare($sqlImagem);
        $stmtImagem->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);
        $stmtImagem->execute();

        if ($stmtImagem->rowCount() > 0) {
            $categoria = $stmtImagem->fetch(PDO::FETCH_ASSOC);
            $imagemPath = '../uploads/' . $categoria['imagem'];

            // Deletar a categoria do banco
            $sql = "DELETE FROM categorias WHERE id_categoria = :id_categoria";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':id_categoria', $id_categoria, PDO::PARAM_INT);

            if ($stmt->execute()) {
                // Deletar imagem, se não for a padrão
                if (file_exists($imagemPath) && $categoria['imagem'] !== 'default_categoria.jpg') {
                    unlink($imagemPath);
                }

                echo "<script> alert('Categoria excluída com sucesso!');history.back();</script>";
            } else {
                echo "<script> alert('Erro ao excluir a categoria.');history.back();</script>";
            }
        } else {
            echo "<script> alert('Categoria não encontrada.');history.back();</script>";
        }
    } else {
        echo "<script> alert('ID da categoria não informado');history.back();</script>";
        exit();
    }
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage();
}

?>

