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

// Configuração da conexão com o banco de dados
include 'conexao.php';

// Obtém a imagem do logotipo da associação
$id = 1; 
$sql = "SELECT logo_image FROM associacoes WHERE id = :id";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();
$associacao = $stmt->fetch(PDO::FETCH_ASSOC);

$logoImage = $associacao['logo_image'] ?? 'default.png';


// Obtendo o nome e o email do usuário da sessão
$id_usuario = $_SESSION['id_usuario'];
$sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
$stmt = $conn->prepare($sql);
$stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

$nome_usuario = $usuario['nome'] ?? 'Usuário';
$email_usuario = $usuario['email'] ?? 'Não cadastrado';

// Obtendo os usuários com o nível 'admin'
$sql = "SELECT id, nome, email FROM usuarios WHERE nivel = 'admin'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Painel - Configurações</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/modal.css">
</head>

<body>
    <section class="bg-menu">
        <div class="menu-left">
            <div class="logo">
                <img class="logo-admin" src="uploads/<?php echo htmlspecialchars($logoImage); ?>" width="100" alt="Logo">
            </div>
            <div class="menus">
                <a href="./home.php" class="menu-item"><i class="fas fa-home"></i> Início</a>
                <a href="./feriadosCadastrados.php" class="menu-item"><i class="fas fa-calendar-alt"></i> feriados</a>
                <a href="./legislacao.php" class="menu-item"><i class="fas fa-book-open"></i> Legislação</a>
                <a href="./alunos.php" class="menu-item"><i class="fas fa-users"></i> Alunos</a>
                <a href="./instrutores.php" class="menu-item"><i class="fas fa-chalkboard-teacher"></i> Instrutores/Placa</a>
                <a href="./configuracoes.php" class="menu-item active"><i class="fas fa-cog"></i> Configurações</a>
                <a href="./relatorio.php" class="menu-item"><i class="fas fa-donate"></i> Financeiro</a>
                <a href="./empresa.php" class="menu-item"><i class="fas fa-building"></i> Empresa</a>
            </div>
        </div>

        <div class="conteudo">
            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">
                            <h1 class="title-page"><b><i class="fas fa-cog"></i>&nbsp; CONFIGURAÇÕES DA EMPRESA</b></h1>
                            <div class="container-right">
                                <div class="container-dados">
                                    <p><?php echo htmlspecialchars($nome_usuario); ?></p>
                                    <?php if ($email_usuario) { ?>
                                    <span><?php echo htmlspecialchars($email_usuario); ?></span>
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
                                <a href="configuracoes.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-tools"></i> Suporte Técnico
                                </a>
                                <a href="guiaEstudo.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-book"></i> Guia de Estudo
                                </a>
                                <a href="formasPagamento.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-coins"></i> Formas de Pagamento
                                </a>
                                <a href="backupSistema.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-cloud-upload-alt"></i> Backup Sistema
                                </a>
                                <a href="usuarios.php" class="btn btn-white btn-sm active">
                                    <i class="fas fa-users"></i> Usuários
                                </a>
                            </div>
                        </div>

                        <div class="col-12 mt-5" id="guia-estudo">
                            <p class="title-categoria mb-0"><b>Configurações do Usuário</b></p>

                            <div class="container-group mb-3">
                                <div class="card card-address cursor-default mt-3" style="border: 1px solid #000 !important;">
                                    <div class="img-icon-details">
                                        <i class="fas fa-users"></i>
                                    </div>
                                    <div class="infos config">
                                        <p class="name mb-2" style="font-size: 18px !important;"><b>Cadastro de Usuários do Painel Administrativo</b></p>
                                        <p>Aqui você pode cadastrar os usuários que terão acesso ao painel administrativo.</p>
                                        <div class="col-10 text-right mt-1">
                                            <a href="cadastroUsuarios.php" class="btn btn-white btn-sm active" style="padding: 6px 13px !important;">Cadastrar Usuário</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="container-group mb-0 mt-4 row">
                                <p class="title-categoria"><b>Usuários Cadastrados</b></p>

                                <?php foreach ($usuarios as $usuario): ?>
                                    <div class="card mb-2  mb-4 mx-3 item-cardapio col-md-6" style="zoom:90%; border: 1px solid #000 !important; max-width: 555px;">
                                        <div class="d-flex">
                                            <div class="container-img-produto d-flex justify-content-center align-items-center" style="width: 100px; height: 100px; background: #f8f8bb;">
                                                <i class="fas fa-user" style="font-size: 48px;"></i>
                                            </div>

                                            <div class="infos-produto" style="flex-grow: 1; padding-left: 10px;">
                                                <p class="name mb-2"><b>Nome:</b> <?php echo htmlspecialchars($usuario['nome']); ?></p>
                                                <p class="name mb-1"><b>Email:</b> <?php echo htmlspecialchars($usuario['email']); ?></p>
                                            </div>
                                            <div class="actions ml-auto d-flex align-items-center">
                                               
                                                <button onclick="openModal(<?php echo $usuario['id']; ?>)" class="btn icon-action"><i class="fas fa-trash-alt"></i></button>
                                            </div>
                                        </div>
                                    </div>

                                    <div id="confirmDeleteUsuarioModal<?php echo $usuario['id']; ?>" class="modal-horarios" style="display: none;">
                                        <div class="modal-horarios-content">
                                            <div class="modal-horarios-header">
                                                <span class="close" onclick="closeModal(<?php echo $usuario['id']; ?>)">&times;</span>
                                                <h2 class="title-modal"><i class="fas fa-trash-alt"></i> Excluir Usuário</h2>
                                            </div>
                                            <p class="p mt-5">Tem certeza que deseja excluir este usuário?</p>
                                            <button class="excluir excluir-secondary" onclick="closeModal(<?php echo $usuario['id']; ?>)">
                                                <span class="text">Não</span>
                                            </button>
                                            <a href="excluirUsuario.php?id=<?php echo $usuario['id']; ?>" class="excluir">Sim</a>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
    <script>
    window.onload = function() {
        uppercaseBElements();
    };

    function openModal(id) {
        console.log('Abrindo modal com ID:', id);
        var modal = document.getElementById('confirmDeleteUsuarioModal' + id);
        console.log('Modal:', modal);
        if (modal) {
            modal.style.display = 'block';
            adjustTableOpacity(0); // Define a opacidade para 50%
        } else {
            console.error('Modal não encontrada.');
        }
    }

    function closeModal(id) {
        console.log('Fechando modal com ID:', id);
        var modal = document.getElementById('confirmDeleteUsuarioModal' + id);
        if (modal) {
            modal.style.display = 'none';
            adjustTableOpacity(1); // Retorna a opacidade ao normal (100%)
        } else {
            console.error('Modal não encontrada.');
        }
    }

    function adjustTableOpacity(opacity) {
        var thElements = document.querySelectorAll('.table-responsive thead th');
        thElements.forEach(function(th) {
            th.style.opacity = opacity;
        });
    }
    </script>
</body>
</html>
