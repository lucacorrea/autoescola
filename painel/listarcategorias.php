<?php
session_start(); // Inicia a sessão

// Exibe erros para depuração (remova em produção)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para verificar se o usuário está logado como administrador ou presidente
function verificarAcesso() {
    if (isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
        $nivel_usuario = $_SESSION['nivel'];

        // Verificar se o nível de usuário é permitido
        if ($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
            return true;
        }
    }
    // Redireciona para loader.php se não tiver permissão
    header("Location: loader.php");
    exit();
}

// Verificar o acesso antes de continuar
verificarAcesso();


require "conexao.php";

try {

    // Consulta SQL para buscar categorias com a imagem
    $sql = "SELECT id_categoria, nome, preco, parcelado, status, imagem FROM categorias";
    $stmt = $conn->query($sql);
    $categorias = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco de dados ou executar consulta: " . $e->getMessage());
}

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
    <title>Listar Categorias</title>
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
                <div class="logo">
                    <img class="logo-admin" src="uploads/<?php echo htmlspecialchars($logoImage); ?>" width="100" alt="Logo">
                </div>
            </div>
            <div class="menus">
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
                            <h1 class="title-page"><b><i class="fas fa-car"></i>&nbsp;PAINEL - CATEGORIAS</b></h1>
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
                        <div class="col-12 mt-0" id="guia-estudo">
                            <div class="menus-config">
                                
                                <a href="empresa.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-info-circle"></i> Sobre a empresa
                                </a>
                                
                                <a href="enderecoEmpresa.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-map-marked-alt"></i> Endereço físico
                                </a>
                                
                                <a href="horarioFuncionamento.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-clock"></i> Horário de funcionamento
                                </a>
                                
                                <a href="listarcategorias.php" class="btn btn-white btn-sm active">
                                    <i class="fas fa-car"></i> Categorias
                                </a>
                            </div>
                            
                            <p class="title-categoria mt-5"><b>Configurações das Categorias</b></p>

                            <div class="container-group mb-3">
                                <div class="card card-address cursor-default mt-3" style="border: 1px solid #000 !important;">
                                    <div class="img-icon-details">
                                        <i class="fas fa-tools"></i>
                                    </div>
                                    <div class="infos config">
                                        <p class="name mb-2" style="font-size: 18px !important;"><b>Categorias Disponíveis</b></p>
                                        <p>Consulte ou adicione categorias para a autoescola.</p>
                                        <div class="col-10 text-right mt-1">
                                            <a href="form.php" class="btn btn-white btn-sm active" style="padding: 6px 13px !important;">Cadastrar Categoria</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <p class="title-categoria mt-5"><b>Categorias Cadastradas</b></p>

                            <div class="container-group mb-0 mt-4 row">
                                <?php
                                    // Verifica se há categorias
                                    if (!empty($categorias)) {
                                        foreach ($categorias as $categoria) {
                                            // Definir o caminho da imagem (se a imagem não existir, usar uma padrão)
                                            $imagem = (!empty($categoria['imagem']) && file_exists($categoria['imagem'])) 
                                                ? $categoria['imagem'] 
                                                : '../uploads/default_categoria.jpg';
                                    
                                            echo '<div class="card mb-4 mx-2 item-cardapio col-md-6" style="border: 1px solid #000 !important; max-width: 580px;">';
                                            echo '  <div class="d-flex">';
                                            echo '    <div class="container-img-produto" style="background-color:#fff; border: 1.5px solid #f8f8bb; background-image: url(\'' . $imagem . '\'); background-size: 90%; width: 100px; height: 100px;"></div>';
                                            echo '    <div class="infos-produto" style="flex-grow: 1; padding-left: 10px;">';
                                            echo '      <p class="name mb-2"><b>' . htmlspecialchars($categoria['nome']) . '</b></p>';
                                            echo '      <p class="preco"><b>Preço: R$</b><span class="text-primary"> ' . number_format($categoria['preco'], 2, ',', '.') . '</span></p>';
                                            
                                            // Define a classe de acordo com o status
                                            $statusCor = '';
                                            if ($categoria['status'] === 'Disponível') {
                                                $statusCor = 'text-success'; // Classe para cor verde
                                            } elseif ($categoria['status'] === 'Indisponível') {
                                                $statusCor = 'text-danger'; // Classe para cor vermelha
                                            } elseif ($categoria['status'] === 'Promoção') {
                                                $statusCor = 'text-warning'; // Classe para cor amarela/laranja
                                            }
                                            
                                            // Exibe o status com a classe correspondente
                                            echo '      <p class="status" style="margin-top: -15px;"><b>Status: </b><span class="' . $statusCor . '">' . htmlspecialchars($categoria['status']) . '</span></p>';


                                            echo '    </div>';
                                            echo '    <div class="actions ml-auto d-flex align-items-center">';
                                            echo '      <a href="editarCategoria.php?id_categoria=' . $categoria['id_categoria'] . '" class="mr-2 icon-action"><i class="fas fa-pencil-alt"></i></a>';
                                            echo '      <button onclick="openModal(' . intval($categoria['id_categoria']) . ')" class="btn icon-action"><i class="fas fa-trash-alt"></i></button>';
                                            echo '    </div>';
                                            echo '  </div>';
                                            echo '</div>';
                                            echo '<div id="confirmDeleteHorarioModal' . intval($categoria['id_categoria']) . '" class="modal-horarios" style="display: none;">';
                                            echo '<div class="modal-horarios-content">';
                                            echo '<div class="modal-horarios-header">';
                                            echo '<span class="close" onclick="closeModal(' . intval($categoria['id_categoria']) . ')">&times;</span>';
                                            echo '<h2 class="title-modal"><i class="fas fa-trash-alt"></i> Excluir Categoria</h2>';
                                            echo '</div>';
                                            echo '<p class="p mt-5">Tem certeza que deseja excluir esta categoria?</p>';
                                            echo '<button class="excluir excluir-secondary" onclick="closeModal(' . intval($categoria['id_categoria']) . ')">';
                                            echo '<span class="text">Não</span>';
                                            echo '</button>';
                                            echo '<a href="excluirCategoria.php?id=' . intval($categoria['id_categoria']) . '"  class="excluir">Sim</a>';
                                            echo '</div>';
                                            echo '</div>';
                                        }
                                    } else {
                                        echo "<p>Nenhuma categoria encontrada.</p>";
                                    }
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
            var thElements = document.querySelectorAll('.table th');
            var trElements = document.querySelectorAll('.table tr');
            thElements.forEach(function(element) {
                element.style.opacity = opacity;
            });
            trElements.forEach(function(element) {
                element.style.opacity = opacity;
            });
        }
    </script>
</body>
</html>
