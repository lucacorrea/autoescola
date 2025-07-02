<?php
session_start(); // Inicia a sessão

// Função para verificar se o usuário está logado como presidente
function verificarAcesso() {
    if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        // Se o usuário estiver logado, verifique se é presidente
        $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

        // Verificar se o nível de usuário é presidente
        if($nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            // O usuário é presidente, então ele tem permissão para acessar esta parte do sistema
            return true;
        } elseif($nivel_usuario == 'admin' ) {
            // Se o usuário é administrador, mas não presidente, ele não tem permissão
            // Redirecionar para outra página ou exibir uma mensagem de erro
            header("Location: paginaProtegida.php");
            exit(); // Encerra o script após o redirecionamento
        }
        
        
    }
    
    // Se não estiver logado como presidente, redirecione-o para a página de login
    header("Location: loader.php");
    exit(); // Encerra o script após o redirecionamento
}

// Verificar o acesso antes de permitir o acesso à página
verificarAcesso();

include "conexao.php";

// ID da associação
$id = 1; 

// Consulta para pegar a logo_image
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// Verifica se encontrou algum dado
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o campo 'logo_image' está definido e não é vazio
$logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";


include "conexao.php";

$id_usuario = $_SESSION['id_usuario'];

try {
    // Preparando a consulta para obter o nome e o email do usuário
    $sql = "SELECT nome, email FROM usuarios WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
    
    // Executando a consulta
    $stmt->execute();

    // Verificando se há resultados
    if ($stmt->rowCount() > 0) {
        // Obtendo os dados do usuário
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $nome_usuario = $row['nome'];
        $email_usuario = $row['email'];
    } else {
        echo "Nenhum resultado encontrado.";
    }
} catch (PDOException $e) {
    echo "Erro ao buscar os dados do usuário: " . $e->getMessage();
}


include "conexao.php";

// ID da associação
$id = 1; 

// Consulta para pegar a logo_image
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// Verifica se encontrou algum dado
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);

// Verifica se o campo 'logo_image' está definido e não é vazio
$logoImage = isset($associacao['logo_image']) ? $associacao['logo_image'] : "";

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Bakcup</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />

</head>
<body>
    <div class="container-mensagens" id="container-mensagens">
        <?php
        if (isset($_GET['success']) && $_GET['success'] == 'true') {
            echo '<div class="alert alert-success" role="alert">Backup feito com sucesso!</div>';
            header("refresh:1; url=backup_sistema.php");
            exit();
        }
        ?>
    </div>

    <div class="loader-full animated fadeIn hidden">
        <img src="../img/loader.png" width="100" class="animated pulse infinite" />
    </div>

    <section class="bg-menu">
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

                
                <a href="./alunos.php" class="menu-item">
                    <i class="fas fa-users"></i> Alunos
                </a>

                
                <a href="./instrutores.php" class="menu-item">
                    <i class="fas fa-chalkboard-teacher"></i> Instrutores/Placa
                </a>

                <a href="./configuracoes.php" class="menu-item active">
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

        <div class="conteudo">
            <section class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page fas fa-cloud-upload-alt">
                                <b>&nbsp; PAINEL - BACKUP</b>
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
            </section>

            <section class="conteudo-inner">
                <div class="container">
                    <div class="row">
                        <div class="col-12">
                            <div class="menus-config">
                                    <a href="configuracoes.php" class="btn btn-white btn-sm">
                                        <i class="fas fa-tools"></i> Suporte Técnico
                                    </a>
                                        <a href="guiaEstudo.php" class="btn btn-white btn-sm">
                                        <i class="fas fa-book"></i> Guia de Estudo
                                    </a>
                                    <a href="formasPagamento.php" class="btn btn-white btn-sm">
                                        <i class="fas fa-coins"></i> Formas de Pagamento
                                    </a>

                                    <a href="backupSistema.php" class="btn btn-white btn-sm active">
                                        <i class="fas fa-cloud-upload-alt"></i> Backup Sistema
                                    </a>

                                    <a href="usuarios.php" class="btn btn-white btn-sm">
                                        <i class="fas fa-users"></i> Usuarios
                                    </a>
                                </div>
                            <div class="menus-config mb-5"></div>
                                <div class="col-12 mt-5" id="categoria">
                                    <div class="col-12" id="categorias">
                                        <div class="container-group mb-5 mt-5">
                                            <div class="accordion" id="categoriasMenu">
                                                <div class="card mt-5">
                                                    <div class="card-drag" id="headingOne">
                                                        <div class="infos">
                                                            <a href="#" class="name mb-4" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                                <b>Selecione a opção abaixo para fazer o backup do seu sistema:</b>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <table class="table">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <a href="backup.php" class="btn btn-white active btn-sm tab-content"><i class="fas fa-cloud-upload-alt"></i>&nbsp;Fazer Backup</a>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
    </section>

</body>
</html>
