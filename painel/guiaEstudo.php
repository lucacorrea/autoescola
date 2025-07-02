<?php
    session_start(); // Inicia a sessão

    // Função para verificar se o usuário está logado como administrador ou presidente
    function verificarAcesso() {
        if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
            // Se o usuário estiver logado, verifique se é admin ou presidente
            $nivel_usuario = $_SESSION['nivel']; // Supondo que o nível de usuário esteja armazenado na sessão

            // Verificar se o nível de usuário é admin, presidente ou suporte
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

    // Obtém a imagem do logotipo da associação
    $id = 1; 
    $sql = "SELECT logo_image FROM associacoes WHERE id = :id";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $associacao = $stmt->fetch(PDO::FETCH_ASSOC);

    $logoImage = $associacao['logo_image'] ?? '';

    // Obtendo o nome e o email do usuário da sessão usando uma consulta SQL
    $id_usuario = $_SESSION['id_usuario'];
    $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    $nome_usuario = $usuario['nome'] ?? '';
    $email_usuario = $usuario['email'] ?? '';

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
                <a href="./feriadosCadastrados.php" class="menu-item"><i class="fas fa-calendar-alt"></i> Feriados</a>
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
                                <a href="configuracoes.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-tools"></i> Suporte Técnico
                                </a>
                                <a href="guiaEstudo.php" class="btn btn-white btn-sm active">
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

                        <div class="col-12 mt-5" id="guia-estudo">
                            <p class="title-categoria mb-0"><b>Configurações do Guia de Estudo</b></p>

                            <div class="container-group mb-3">
                                <div class="card card-address cursor-default mt-3" style="border: 1px solid #000 !important;">
                                    <div class="img-icon-details">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <div class="infos config">
                                        <p class="name mb-2" style="font-size: 18px !important;"><b>Guia de Estudo da Autoescola</b></p>
                                        <p>Aqui você pode fornecer os materiais do guia de estudo da autoescola.</p>
                                        <div class="col-10 text-right mt-1">
                                            <a href="cadastroMaterial.php" class="btn btn-white btn-sm active" style="padding: 6px 13px !important;">Cadastrar Materiais</a>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="container-group mb-0 mt-4 row">
                                <p class="title-categoria"><b>Materiais Disponíveis</b></p>
                                <?php
                                    // Conecte-se ao banco de dados usando PDO
                                    include 'conexao.php';

                                    // Consulta para obter os materiais cadastrados
                                    $sql = "SELECT id, titulo_material, nome_capa, nome_material FROM materiais_estudo";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->execute();

                                    // Verifica se há resultados
                                    if ($stmt->rowCount() > 0) {
                                        // Exibe cada material como um card
                                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                            echo '<div class="card mb-4 mx-2 item-cardapio col-md-6" style="border: 1px solid #000 !important; max-width: 580px;">';
                                            echo '  <div class="d-flex">';
                                            echo '    <div class="container-img-produto" style="background-color:#fff; border: 1.5px solid #f8f8bb; background-image: url(\'uploads/' . htmlspecialchars($row['nome_capa']) . '\'); background-size: cover; width: 100px; height: 100px;"></div>';
                                            echo '    <div class="infos-produto" style="flex-grow: 1; padding-left: 10px;">';
                                            echo '      <p class="name mb-1"><b>' . htmlspecialchars($row['titulo_material']) . '</b></p>';
                                            echo '      <a href="uploads/' . htmlspecialchars($row['nome_material']) . '" class="price mt-3 link-material" target="_blank"><b>Visualizar Material</b></a>';
                                            echo '    </div>';
                                            echo '    <div class="actions ml-auto d-flex align-items-center">';
                                            echo '      <a href="editarMaterial.php?id=' . $row['id'] . '" class="mr-2 icon-action"><i class="fas fa-pencil-alt"></i></a>';
                                            echo '      <button onclick="openModal(' . $row["id"] . ')" class="btn icon-action"><i class="fas fa-trash-alt"></i></button>';
                                            echo '    </div>';
                                            echo '  </div>';
                                            echo '</div>';
                                            echo '<div id="confirmDeleteHorarioModal' . $row["id"] . '" class="modal-horarios" style="display: none;">';
                                            echo '<div class="modal-horarios-content">';
                                            echo '<div class="modal-horarios-header">';
                                            echo '<span class="close" onclick="closeModal(' . $row["id"] . ')">&times;</span>';
                                            echo '<h2 class="title-modal"><i class="fas fa-trash-alt"></i> Excluir Material</h2>';
                                            echo '</div>';
                                            echo '<p class="p mt-5">Tem certeza que deseja excluir este material?</p>';
                                            echo '<button class="excluir excluir-secondary" onclick="closeModal(' . $row["id"] . ')">';
                                            echo '<span class="text">Não</span>';
                                            echo '</button>';
                                            echo '<a href="excluirMaterial.php?id=' . $row["id"] . '"  class="excluir">Sim</a>';
                                            echo '</div>';
                                            echo '</div>';
                                            }
                                        } else {
                                            echo "<p>Nenhum material cadastrado.</p>";
                                        }

                                        // Fechar a conexão
                                        $conn = null;
                                    ?>

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
    var modal = document.getElementById('confirmDeleteHorarioModal' + id);
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
    var modal = document.getElementById('confirmDeleteHorarioModal' + id);
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
