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

include "conexao.php";

// Verifica se o ID foi passado via GET
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para obter os dados do material
    $sql = "SELECT * FROM materiais_estudo WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $material = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$material) {
        die("Material não encontrado.");
    }
} else {
    die("ID não fornecido.");
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Editar Material</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/editMaterial.css">
    
</head>
<body>

    <div class="container">
        <div class="d-flex justify-content-end mb-3 mt-4">
            <a href="guiaEstudo.php" class="btn btn-white btn-sm">
                <i class="fas fa-sign-out-alt"></i>&nbsp; Voltar 
            </a>
        </div>
        
        <div class="col-12 mt-0" id="cadastro-material">
            <p class="title-categoria mb-0">
                <b>Editar Material de Estudo</b>
            </p>
        
            <div class="row">
                <!-- Formulário de Edição -->
                <div class="col-md-8">
                    <div class="container-group mb-3">
                        <form action="updateMaterial.php" method="POST" enctype="multipart/form-data">
                            <!-- ID do Material -->
                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($material['id']); ?>">

                            <!-- Nome do Material -->
                            <div class="card card-address card-title mt-3">
                                <div class="infos config">
                                    <p class="name mb-2" style="font-size: 18px !important; margin-left: -30px;"><b>Titulo do Material</b></p>
                                    <input type="text" class="form-control" name="titulo_atual" value="<?php echo htmlspecialchars($material['titulo_material']); ?>" required>
                                </div>
                            </div>
                            <!-- Capa do Material -->
                            <div class="card card-address cursor-default mt-2" style="border: 1px solid #000 !important;">
                                <div class="img-icon-details" style="position: relative; ">
                                    <i class="fas fa-pencil-alt" id="uploadCapaIcon"></i>
                                    <div style="position: absolute; opacity: 0; top: 0; left: 0; width: 100%; height: 100%; cursor: pointer !important;">
                                    </div>
                                        
                                </div>
                                <div class="infos config">
                                    <p class="name mb-2" style="font-size: 18px !important;"><b>Capa do Material</b></p>
                                    <p>Selecione uma imagem para a capa do material.</p>
                                    <input type="file" name="capa" id="capaInput" accept="image/*"/>
                                    <input type="hidden" name="capa_atual" value="<?php echo htmlspecialchars($material['nome_capa']); ?>">
                                </div>
                            </div>
            
                            <!-- Upload do Material -->
                            <div class="card card-address  mt-3" style="border: 1px solid #000 !important;">
                                <div class="img-icon-details">
                                    <i class="fas fa-file-upload"></i>
                                </div>
                                <div class="infos config">
                                    <p class="name mb-2" style="font-size: 18px !important;"><b>Upload do Material</b></p>
                                    <p>Selecione o arquivo do material de estudo (PDF, DOCX, etc.).</p>
                                    <input type="file" name="material" accept=".pdf,.doc,.docx"/>
                                    <input type="hidden" name="material_atual" value="<?php echo htmlspecialchars($material['nome_material']); ?>">
                                </div>
                            </div>
            
                            <!-- Botão de Submissão -->
                            <div class="col-12 text-right mt-3">
                                <button type="submit" class="btn btn-white btn-sm active" style="padding: 6px 13px !important;">Atualizar Material</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Pré-visualização da Capa -->
                <div class="col-md-4">
                    <div class="card card-preview">
                        <div class="card-body text-center">
                            <p><b>Prévia da Capa</b></p>
                            <img id="previewCapa" src="uploads/<?php echo htmlspecialchars($material['nome_capa']); ?>" alt="Prévia da Capa">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para mostrar a prévia da capa quando o ícone do lápis for clicado
        document.getElementById('uploadCapaIcon').addEventListener('click', function() {
            document.getElementById('capaInput').click();
        });
        
        document.getElementById('capaInput').addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                document.getElementById('previewCapa').src = URL.createObjectURL(file);
                document.getElementById('previewCapa').style.display = 'block';
            }
        });
    </script>
</body>
</html>
