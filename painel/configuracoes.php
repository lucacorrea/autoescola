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

// Configurações do banco de dados
include 'conexao.php';
    if (isset($_SESSION['id_usuario'])) {
        $id_usuario = $_SESSION['id_usuario'];

        // Consulta segura com prepared statements
        $sql = "SELECT nome, email FROM usuarios WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id_usuario, PDO::PARAM_INT);
        $stmt->execute();

        // Verificando se há resultados
        if ($stmt->rowCount() > 0) {
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            $nome_usuario = $usuario['nome'];
            $email_usuario = $usuario['email'];
        } else {
            echo "Nenhum resultado encontrado.";
        }
    } else {
        echo "Usuário não autenticado.";
    }


    // Fechar a conexão (opcional com PDO)



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
<html lang="en">
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
   

</head>

<body>

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

            <div class="menu-top">
                <div class="container">
                    <div class="row">
                        <div class="col-12 d-flex align-items-center mt-4">

                            <h1 class="title-page">
                                <b>
                                    <i class="fas fa-cog"></i>&nbsp; CONFIGURAÇÕES DA EMPRESA
                                </b>
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
                                <a href="configuracoes.php" class="btn btn-white btn-sm active">
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

                                <a href="usuarios.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-users"></i> Usuarios
                                </a>
                            </div>

                        </div>

                        <div class="col-12 mt-5" id="suporte-tecnico">

                            <p class="title-categoria mb-0">
                                <b>Contate nosso suporte técnico para obter assistência</b>
                            </p>

                            <div class="container-group mb-3">                              
                                <div class="card card-address cursor-default mt-3">
                                    <div class="img-icon-details">
                                        <i class="fas fa-headset"></i>
                                    </div>
                                    <div class="infos config">
                                      
                                        <p class="name mb-3 name-suporte"><b>Suporte Técnico</b></p>
                                        <p>Para obter ajuda técnica, entre em contato conosco pelo e-mail ou telefone abaixo:</p>
                                        <span>
                                            <b>E-mail:</b> 
                                            <a href="mailto:suportecodegeek@gmail.com?subject=D%C3%BAvida&body=Oi%2C%20tudo%20bom%3F%20Eu%20preciso%20tirar%20uma%20d%C3%BAvida" 
                                            target="_blank" 
                                            
                                            onmouseover="this.style.color='rgb(0, 68, 146)';" 
                                            onmouseout="this.style.color='black';">
                                                suportecodegeek@gmail.com
                                            </a>
                                        </span>
                                        <br>
                                        <span>
                                            <b>Whatsapp:</b> 
                                            <a href="https://wa.me/5592991515710?text=Oi%2C%20tudo%20bom%3F%20Eu%20preciso%20tirar%20uma%20d%C3%BAvida" 
                                            target="_blank" 
                                            
                                            onmouseover="this.style.color='rgb(0, 68, 146)';" 
                                            onmouseout="this.style.color='black';">
                                                (92) 99151-5710
                                            </a>
                                        </span>

                                    </div>
                                </div>
                                
                       
                    </div>
                </div>       
            </div>

        </div>

    </section>

    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>

</body>
</html>
