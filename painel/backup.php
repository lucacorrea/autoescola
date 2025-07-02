<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../img/logotipo.png" type="image/x-icon">
    <title>Painel - Backup</title>
</head>
<body>
<?php
// Incluindo o arquivo de conexão
include "conexao.php";

// Diretório onde o backup será salvo
$backup_dir = 'uploads/backup/';
$backup_file = $backup_dir . 'backup_autoescola.sql';

// Certifica-se de que o diretório de backup existe
if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
}

try {
    // Apaga o arquivo de backup existente, se houver
    if (file_exists($backup_file)) {
        unlink($backup_file); // Remove o arquivo anterior
    }

    // Obtém todas as tabelas do banco de dados
    $tables = $conn->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    $sql = '';

    // Loop pelas tabelas para gerar o SQL
    foreach ($tables as $table) {
        // Estrutura da tabela
        $create_table = $conn->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_ASSOC);
        $sql .= $create_table["Create Table"] . ";\n\n";

        // Dados da tabela
        $result = $conn->query("SELECT * FROM `$table`");
        foreach ($result as $row) {
            $row = array_map([$conn, 'quote'], $row);
            $sql .= "INSERT INTO `$table` VALUES (" . implode(', ', $row) . ");\n";
        }
        $sql .= "\n\n";
    }

    // Salva o SQL gerado no arquivo de backup
    file_put_contents($backup_file, $sql);

    // Exibe mensagem de sucesso no script com redirecionamento garantido
    echo "<script>
        alert('Backup do banco de dados realizado com sucesso!');
        window.location.href = 'backupSistema.php'; // Redireciona imediatamente após o alerta
    </script>";

} catch (PDOException $e) {
    // Exibe mensagem de erro em caso de falha
    echo "<div id='backupModal' class='modal'>
        <div class='modal-content' style='color: red;'>
            <p><b>Erro ao realizar o backup: " . htmlspecialchars($e->getMessage()) . "</b></p>
        </div>
    </div>";
}
?>
</body>
</html>
