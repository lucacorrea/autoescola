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

require "conexao.php";

// Obtendo o ID do usuário da sessão
$id_usuario = $_SESSION['id_usuario'];

// Consulta SQL para obter o nome e o email do usuário
$sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";

try {
    // Preparar a consulta
    $stmt = $conn->prepare($sql);

    // Vincular o parâmetro
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);

    // Executar a consulta
    $stmt->execute();

    // Verificar se há resultados
    if ($stmt->rowCount() > 0) {
        // Obter os dados do usuário
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nome_usuario = $row['nome'];
        $email_usuario = $row['email'];
    } else {
        echo "Nenhum resultado encontrado.";
    }
} catch (PDOException $e) {
    echo "Erro na consulta: " . $e->getMessage();
}

// Fecha a conexão com o banco de dados (opcional, pois o PDO fecha automaticamente)
$conn = null;


require "conexao.php";

// Recebe o ID da associação (exemplo: ID 1)
$id = 1; // Substitua pelo ID da associação que você deseja exibir

// Busca os dados da associação
$sql = "SELECT * FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se a associação foi encontrada
if (!$associacao) {
    $associacao = [
        'nome_associacao' => '',
        'sobre_associacao' => '',
        'logo_image' => ''
    ];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Empresa</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />

</head>
<body>

    <section class="bg-menu">

        <div class="menu-left">

            <div class="logo">
                <img class="logo-admin" src="uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>" width="100" alt="Logo" />
            </div>

            <div class="menus">
                <!-- Menus -->
                <a href="./home.php" class="menu-item"><i class="fas fa-home"></i> Início</a>
                <a href="./feriadosCadastrados.php" class="menu-item"><i class="fas fa-calendar-alt"></i> Feriados</a>
                <a href="./legislacao.php" class="menu-item"><i class="fas fa-book-open"></i> Legislação</a>
                <a href="./alunos.php" class="menu-item"><i class="fas fa-users"></i> Alunos</a>
                <a href="./instrutores.php" class="menu-item"><i class="fas fa-chalkboard-teacher"></i> Instrutores/Placa</a>
                <a href="./configuracoes.php" class="menu-item"><i class="fas fa-cog"></i> Configurações</a>
                <a href="./relatorio.php" class="menu-item"><i class="fas fa-donate"></i> Financeiro</a>
                <a href="./empresa.php" class="menu-item active"><i class="fas fa-building"></i> Empresa</a>
            </div>

        </div>

        <div class="conteudo">

            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page">
                                <b><i class="fas fa-building"></i>&nbsp; CONFIGURAÇÕES DA EMPRESA</b>
                            </h1>
                            <div class="container-right">
                                <div class="container-dados">
                                <p><?php echo $nome_usuario; ?></p>
                                    <?php if ($email_usuario) { ?>
                                    <span><?php echo $email_usuario; ?></span>
                                    <?php } ?>
                                </div>
                                <a href="logout.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="conteudo-inner">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="menus-config">
                                <a href="empresa.php" class="btn btn-white btn-sm active">
                                    <i class="fas fa-info-circle"></i> Sobre a empresa
                                </a>
                                <a href="enderecoEmpresa.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-map-marked-alt"></i> Endereço físico
                                </a>
                                <a href="horarioFuncionamento.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-clock"></i> Horário de funcionamento
                                </a>
                                <a href="listarcategorias.php" class="btn btn-white btn-sm ">
                                    <i class="fas fa-car"></i> Categorias
                                </a>
                            </div>
                        </div>

                        <div class="container mt-5">
                            <form id="associationForm" action="updateEmpresa.php" method="POST" enctype="multipart/form-data">
                                <div class="col-12 mt-0" id="sobre" style="zoom: 90%;">
                                    <div class="d-flex">
                                        <div class="logo-empresa">
                                            <div class="container-img-sobre" id="logoContainer" style="background-image: url('uploads/<?php echo htmlspecialchars($associacao['logo_image'] ?? 'default.png'); ?>'); background-size: 70%;">
                                                <input type="file" id="fileInput" name="logoImage" style="display: none;" accept="image/*"/>
                                                <a href="#" class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" id="editIcon">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </a>
                                            </div>
                                        </div>
                                        <div class="detalhes-empresa">
                                            <p class="title-categoria mb-0"><b>Nome da Associação:</b></p>
                                            <div class="form-group mt-2">
                                                <input type="text" name="nomeAssociacao" class="form-control input-sobre" value="<?php echo htmlspecialchars($associacao['nome_associacao'] ?? ''); ?>" required />
                                            </div>
                                            <p class="title-categoria mb-0 mt-4"><b>Sobre da Associação:</b></p>
                                            <div class="form-group mt-2">
                                                <textarea name="sobreAssociacao" class="form-control textarea" required><?php echo htmlspecialchars($associacao['sobre_associacao'] ?? ''); ?></textarea>
                                            </div>
                                            <button type="submit" class="btn btn-sm mt-3 btn-white active">
                                                <i class="fas fa-check"></i>&nbsp; Salvar Alterações
                                            </button>
                                            <input type="hidden" name="currentLogoImage" value="<?php echo htmlspecialchars($associacao['logo_image'] ?? ''); ?>">
                                            <input type="hidden" name="id" value="<?php echo htmlspecialchars($associacao['id'] ?? ''); ?>">
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>       
            </div>

        </div>

    </section>

    <script src="../js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('editIcon').addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('fileInput').click();
        });

        document.getElementById('fileInput').addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('logoContainer').style.backgroundImage = `url(${e.target.result})`;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>

</body>
</html>
