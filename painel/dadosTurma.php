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


// Conexão com o banco de dados usando PDO
include 'conexao.php' ;
 
// Verifica se o ID da turma foi passado pela URL
if (isset($_GET['id'])) {
    $id_turma = $_GET['id'];

    try {
        // Consulta SQL para buscar os dados da turma
        $query = "SELECT * FROM turmas WHERE id = :id_turma";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':id_turma', $id_turma, PDO::PARAM_INT);
        $stmt->execute();

        // Verifica se a turma foi encontrada
        if ($stmt->rowCount() > 0) {
            // Armazena os dados da turma
            $turma = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            echo "Turma não encontrada.";
            exit;
        }
    } catch (PDOException $e) {
        echo "Erro ao buscar a turma: " . $e->getMessage();
        exit;
    }
} else {
    echo "ID da turma não fornecido.";
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
    <title>Dados da Turma</title>

    <link rel="stylesheet" href="../css/bootstrap.min.css" />
    <link rel="stylesheet" href="../css/fontawesome.css" />
    <link rel="stylesheet" href="../css/animate.css" />
    <link rel="stylesheet" href="../css/main.css" />
    <link rel="stylesheet" href="./css/painel.css" />
    <link rel="stylesheet" href="./css/dadosAlunos.css">

</head>
<body>

<section class="conteudo-dados mb-4">
    <header class="width-fix mt-5 mb-4">
        <div class="card">
            <div class="d-flex">
                <a href="./legislacao.php" class="container-voltar">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="infos text-center">
                    <h1 class="mb-0"><b>Dados da Turma</b></h1>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Exibição dos dados da turma -->
    <section class="carrinho width-fix mt-0">
        <div class="card card-address">
            <div class="img-icon-details">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="infos">
                <p class="name mb-0"><b>INSTRUTOR:</b></p>
                <span><?php echo htmlspecialchars($turma['instrutor']); ?></span>
            </div>
        </div>
    </section>
    
    <section class="opcionais width-fix mt-0 pb-5">
        <div class="container-group mb-0">
            <div class="card card-address mt-0">
                <div class="img-icon-details">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <div class="infos">
                    <p class="name mb-0"><b>LOCAL: &nbsp;</b></p>
                    <span><?php echo htmlspecialchars($turma['local']); ?></span>
                </div>
            </div>
        </div>

        <div class="container-group mb-0">
            <div class="card card-address mt-0">
                <div class="img-icon-details">
                    <i class="fas fa-business-time"></i>
                </div>
                <div class="infos">
                    <p class="name mb-0"><b>TURNO:</b></p>
                    <span><?php echo htmlspecialchars($turma['turno']); ?></span>
                </div>
            </div>
        </div>

        <div class="container-group mb-0">
            <div class="card card-address mt-0">
                <div class="img-icon-details">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="infos">
                    <p class="name mb-0"><b>DATA INÍCIO: &nbsp;</b></p>
                    <span>
                        <?php 
                        // Convertendo data para formato BR
                        echo htmlspecialchars(date('d/m/Y', strtotime($turma['data_inicio']))); 
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="container-group mb-0">
            <div class="card card-address mt-0">
                <div class="img-icon-details">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="infos">
                    <p class="name mb-0"><b>DATA FIM:</b></p>
                    <span>
                        <?php 
                        // Convertendo data para formato BR
                        echo htmlspecialchars(date('d/m/Y', strtotime($turma['data_fim']))); 
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="container-group mb-0">
            <div class="card card-address mt-0">
                <div class="img-icon-details">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="infos">
                    <p class="name mb-0"><b>HORÁRIO INÍCIO:</b></p>
                    <span>
                        <?php 
                        // Exibindo horário com dois últimos zeros (hh:mm)
                        echo htmlspecialchars(substr($turma['horario_inicio'], 0, 5)); 
                        ?>
                    </span>
                </div>
            </div>
        </div>

        <div class="container-group mb-0">
            <div class="card card-address mt-0">
                <div class="img-icon-details">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="infos">
                    <p class="name mb-0"><b>HORÁRIO FIM:</b></p>
                    <span>
                        <?php 
                        // Exibindo horário com dois últimos zeros (hh:mm)
                        echo htmlspecialchars(substr($turma['horario_fim'], 0, 5)); 
                        ?>
                    </span>
                </div>
            </div>
        </div>

    </section>
</section>

</body>
</html>
