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

require "conexao.php";

// Verifica se o usuário está logado
if (!isset($_SESSION['id_usuario'])) {
    header("Location: login.php");
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

try {
    // Obtém os dados do usuário da tabela `usuarios`
    $sql = "SELECT nome, email FROM usuarios WHERE id = :id_usuario";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $nome_usuario = htmlspecialchars($user['nome']);
        $email_usuario = htmlspecialchars($user['email']);
    } else {
        throw new Exception("Usuário não encontrado.");
    }
} catch (Exception $e) {
    die("Erro ao buscar dados do usuário: " . $e->getMessage());
}

// Inicializa variáveis com valores padrão
$cep = $telefone = $endereco = $bairro = $numero = $cidade = $complemento = $uf = $cnpj = $id = "";

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Captura os dados enviados pelo formulário
    $cep = trim($_POST['cep'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $endereco = trim($_POST['endereco'] ?? '');
    $bairro = trim($_POST['bairro'] ?? '');
    $numero = trim($_POST['numero'] ?? '');
    $cidade = trim($_POST['cidade'] ?? '');
    $complemento = trim($_POST['complemento'] ?? '');
    $cnpj = trim($_POST['cnpj'] ?? '');
    $uf = trim($_POST['uf'] ?? '');
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;

    try {
        if ($id > 0) {
            // Atualiza os dados existentes
            $sql = "UPDATE enderecos 
                    SET cep = :cep, telefone = :telefone, endereco = :endereco, bairro = :bairro, 
                        numero = :numero, cidade = :cidade, complemento = :complemento, 
                        cnpj = :cnpj, uf = :uf 
                    WHERE id = :id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cep', $cep);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':bairro', $bairro);
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':cidade', $cidade);
            $stmt->bindParam(':complemento', $complemento);
            $stmt->bindParam(':cnpj', $cnpj);
            $stmt->bindParam(':uf', $uf);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        } else {
            // Insere novos dados
            $sql = "INSERT INTO enderecos (cep, telefone, endereco, bairro, numero, cidade, complemento, cnpj, uf) 
                    VALUES (:cep, :telefone, :endereco, :bairro, :numero, :cidade, :complemento, :cnpj, :uf)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':cep', $cep);
            $stmt->bindParam(':telefone', $telefone);
            $stmt->bindParam(':endereco', $endereco);
            $stmt->bindParam(':bairro', $bairro);
            $stmt->bindParam(':numero', $numero);
            $stmt->bindParam(':cidade', $cidade);
            $stmt->bindParam(':complemento', $complemento);
            $stmt->bindParam(':cnpj', $cnpj);
            $stmt->bindParam(':uf', $uf);
            $stmt->execute();
        }

        echo "<script>alert('Dados salvos com sucesso.'); window.location.href='enderecoEmpresa.php';</script>";
    } catch (PDOException $e) {
        die("Erro ao salvar os dados: " . $e->getMessage());
    }
}

// Busca o primeiro endereço cadastrado (ou ajusta conforme necessário)
try {
    $sql = "SELECT * FROM enderecos LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $cep = htmlspecialchars($row['cep']);
        $telefone = htmlspecialchars($row['telefone']);
        $endereco = htmlspecialchars($row['endereco']);
        $bairro = htmlspecialchars($row['bairro']);
        $numero = htmlspecialchars($row['numero']);
        $cidade = htmlspecialchars($row['cidade']);
        $complemento = htmlspecialchars($row['complemento']);
        $cnpj = htmlspecialchars($row['cnpj']);
        $uf = htmlspecialchars($row['uf']);
        $id = $row['id'];
    }
} catch (PDOException $e) {
    die("Erro ao buscar dados do endereço: " . $e->getMessage());
}

// Fechamento explícito da conexão (opcional com PDO)
$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
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
                
                <a href="./alunos.php" class="menu-item ">
                    <i class="fas fa-users"></i> Alunos
                </a>

                
                <a href="./instrutores.php" class="menu-item">
                    <i class="fas fa-chalkboard-teacher"></i> Instrutores/Placa
                </a>

                <a href="./configuracoes.php" class="menu-item">
                    <i class="fas fa-cog"></i> Configurações
                </a>

                <a href="./relatorio.php" class="menu-item">
                    <i class="fas fa-donate"></i> Financeiro
                </a>


                <a href="./empresa.php" class="menu-item active">
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
                                    <i class="fas fa-building"></i>&nbsp; CONFIGURAÇÕES DA EMPRESA
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
                                <a href="empresa.php" class="btn btn-white btn-sm">
                                    <i class="fas fa-info-circle"></i> Sobre a empresa
                                </a>
                                <a href="enderecoEmpresa.php" class="btn btn-white btn-sm active">
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

                        <form method="POST" action="">
                            <input type="hidden" name="id" value="<?php echo $id; ?>" />
                            <!-- Seção de Endereço -->
                            <div class="col-12 mt-5" id="endereco">
                                <div class="row">
                                    <!-- CEP -->
                                    <div class="col-4">
                                    <div class="form-group container-cep">
                                        <label class="title-categoria mb-0"><b>CEP:</b></label>
                                        <input type="text" class="form-control input-sobre" name="cep" value="<?php echo $cep; ?>" />
                                       
                                    </div>
                                </div>

                            <!-- Telefone -->
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="title-categoria mb-0 mt-0"><b>Telefone:</b></label>
                                    <input type="text" class="form-control input-sobre" name="telefone" value="<?php echo $telefone; ?>" placeholder="(55) 97123-1234" />
                                </div>
                            </div>

                            <!-- Telefone -->
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="title-categoria mb-0 mt-0"><b>CNPJ:</b></label>
                                    <input type="text" class="form-control input-sobre" name="cnpj" value="<?php echo $cnpj; ?>"  />
                                </div>
                            </div>

                            <!-- Endereço -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="title-categoria mb-0 mt-4"><b>Endereço:</b></label>
                                    <input type="text" class="form-control input-sobre" name="endereco" value="<?php echo $endereco; ?>" />
                                </div>
                            </div>

                            <!-- Bairro -->
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="title-categoria mb-0 mt-4"><b>Bairro:</b></label>
                                    <input type="text" class="form-control input-sobre" name="bairro" value="<?php echo $bairro; ?>" />
                                </div>
                            </div>

                            <!-- Número -->
                            <div class="col-2">
                                <div class="form-group">
                                    <label class="title-categoria mb-0 mt-4"><b>Número:</b></label>
                                    <input type="text" class="form-control input-sobre" name="numero" value="<?php echo $numero; ?>" />
                                </div>
                            </div>

                            <!-- Cidade -->
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="title-categoria mb-0 mt-4"><b>Cidade:</b></label>
                                    <input type="text" class="form-control input-sobre" name="cidade" value="<?php echo $cidade; ?>" />
                                </div>
                            </div>

                            <!-- Complemento -->
                            <div class="col-4">
                                <div class="form-group">
                                    <label class="title-categoria mb-0 mt-4"><b>Complemento:</b></label>
                                    <input type="text" class="form-control input-sobre" name="complemento" value="<?php echo $complemento; ?>" />
                                </div>
                            </div>

                            <!-- UF -->
                            <div class="col-2">
                                <div class="form-group">
                                    <label class="title-categoria mb-0 mt-4"><b>UF:</b></label>
                                    <select class="form-control input-sobre" name="uf">
                                        <option value="-1" <?php echo $uf == "-1" ? "selected" : ""; ?>>...</option>
                                        <option value="AC" <?php echo $uf == "AC" ? "selected" : ""; ?>>AC</option>
                                        <option value="AL" <?php echo $uf == "AL" ? "selected" : ""; ?>>AL</option>
                                        <option value="AP" <?php echo $uf == "AP" ? "selected" : ""; ?>>AP</option>
                                        <option value="AM" <?php echo $uf == "AM" ? "selected" : ""; ?>>AM</option>
                                        <option value="BA" <?php echo $uf == "BA" ? "selected" : ""; ?>>BA</option>
                                        <option value="CE" <?php echo $uf == "CE" ? "selected" : ""; ?>>CE</option>
                                        <option value="DF" <?php echo $uf == "DF" ? "selected" : ""; ?>>DF</option>
                                        <option value="ES" <?php echo $uf == "ES" ? "selected" : ""; ?>>ES</option>
                                        <option value="GO" <?php echo $uf == "GO" ? "selected" : ""; ?>>GO</option>
                                        <option value="MA" <?php echo $uf == "MA" ? "selected" : ""; ?>>MA</option>
                                        <option value="MT" <?php echo $uf == "MT" ? "selected" : ""; ?>>MT</option>
                                        <option value="MS" <?php echo $uf == "MS" ? "selected" : ""; ?>>MS</option>
                                        <option value="MG" <?php echo $uf == "MG" ? "selected" : ""; ?>>MG</option>
                                        <option value="PA" <?php echo $uf == "PA" ? "selected" : ""; ?>>PA</option>
                                        <option value="PB" <?php echo $uf == "PB" ? "selected" : ""; ?>>PB</option>
                                        <option value="PR" <?php echo $uf == "PR" ? "selected" : ""; ?>>PR</option>
                                        <option value="PE" <?php echo $uf == "PE" ? "selected" : ""; ?>>PE</option>
                                        <option value="PI" <?php echo $uf == "PI" ? "selected" : ""; ?>>PI</option>
                                        <option value="RJ" <?php echo $uf == "RJ" ? "selected" : ""; ?>>RJ</option>
                                        <option value="RN" <?php echo $uf == "RN" ? "selected" : ""; ?>>RN</option>
                                        <option value="RS" <?php echo $uf == "RS" ? "selected" : ""; ?>>RS</option>
                                        <option value="RO" <?php echo $uf == "RO" ? "selected" : ""; ?>>RO</option>
                                        <option value="RR" <?php echo $uf == "RR" ? "selected" : ""; ?>>RR</option>
                                        <option value="SC" <?php echo $uf == "SC" ? "selected" : ""; ?>>SC</option>
                                        <option value="SP" <?php echo $uf == "SP" ? "selected" : ""; ?>>SP</option>
                                        <option value="SE" <?php echo $uf == "SE" ? "selected" : ""; ?>>SE</option>
                                        <option value="TO" <?php echo $uf == "TO" ? "selected" : ""; ?>>TO</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                            <!-- Botão de Salvar Alterações -->
                            <button type="submit" class="btn btn-sm mt-5 btn-white active">
                                <i class="fas fa-check"></i>&nbsp; Salvar Alterações
                            </button>
                        </div>
                    </form>
                        
                       
                    </div>
                </div>       
            </div>

        </div>

    </section>

    <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>

</body>
</html>