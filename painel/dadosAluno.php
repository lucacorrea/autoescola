<?php

//-------------------SESSION------------------------

    session_start();

    function verificarAcesso() {
        if(isset($_SESSION['id_usuario']) && isset($_SESSION['nivel'])) {
            $nivel_usuario = $_SESSION['nivel'];

            if($nivel_usuario == 'admin' || $nivel_usuario == 'presidente' || $nivel_usuario == 'suporte') {
                return true;
            }
        }
        
        header("Location: loader.php");
        exit();
    }

    verificarAcesso();

//-------------------END SESSION--------------------


//-------------------GET ALUNOS---------------------

    include "conexao.php";

    try {
        $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

        $sql = "SELECT * FROM alunos WHERE id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            die("Aluno não encontrado.");
        }

        $foto = !empty($row['foto']) ? $row['foto'] : 'user.png';

        $fotoPath = $foto;
        if (!file_exists($fotoPath) || empty($row['foto'])) {
            $fotoPath = 'uploads/user.png'; 
        }

    } catch (PDOException $e) {
        die("Erro ao buscar os dados do aluno: " . $e->getMessage());
    }

//-------------------END GET ALUNOS

?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="shortcut icon" href="./img/logo.png" type="image/x-icon">
        <title>Dados do Aluno</title>

        <!-- custom CSS -->
        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css" />
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/painel.css" />
        <link rel="stylesheet" href="./css/dadosAlunos.css">
        <!--End custom CSS -->

    </head>

    <body>

        <!-- section -->
        <section class="conteudo-dados mb-4">

            <!-- header -->
            <header class="width-fix mt-5 mb-4">
                <div class="card">
                    <div class="d-flex">
                        <a href="./alunos.php" class="container-voltar">
                            <i class="fas fa-arrow-left"></i>
                        </a>
                        <div class="infos text-center">
                            <h1 class="mb-0"><b>Dados do Aluno</b></h1>
                        </div>
                    </div>
                </div>
            </header>
            <!-- header -->

            <!-- secton 2 -->
            <section class="carrinho width-fix mt-0">
                <div class="card card-address">
                    <div class="img-icon-details">
                        <i class="fas fa-male"></i>
                    </div>
                    <div class="infos">
                        <p class="name mb-0"><b>NOME:</b></p>
                        <span><?php echo htmlspecialchars($row['nome']); ?></span>
                    </div>
                    <div class="logo-empresa mt-2">
                        <div class="container-img-sobre" style="background-image: url('<?php echo $fotoPath; ?>'); background-size: 100%;">
                            <input type="file" id="fileInput" name="logoImage" style="display: none;" accept="image/*"/>
                            <button class="icon-action" data-bs-toggle="tooltip" data-bs-placement="top" title="Editar" id="openModalBtn">
                                <i class="fas fa-pencil-alt"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
            <!-- End Section 2 -->
            
            <!-- Section 3 -->
            <section class="opcionais width-fix mt-0 pb-5">

                <div class="container-group mb-0">
                    <div class="card card-address mt-0">
                        <div class="img-icon-details">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="infos">
                            <p class="name mb-0"><b>RG: &nbsp;</b></p>
                            <span><?php echo htmlspecialchars($row['rg']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="container-group mb-0">
                    <div class="card card-address mt-0">
                        <div class="img-icon-details">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="infos">
                            <p class="name mb-0"><b>CPF: &nbsp;</b></p>
                            <span><?php echo htmlspecialchars($row['cpf']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="container-group mb-0">
                    <div class="card card-address mt-0">
                        <div class="img-icon-details">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="infos">
                            <p class="name mb-0"><b>DATA DE NASCIMENTO:</b></p>
                            <span><?php echo date('d/m/Y', strtotime($row['data_nascimento'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="container-group mb-0">
                    <div class="card card-address mt-0">
                        <div class="img-icon-details">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="infos">
                            <p class="name mb-0"><b>TELEFONE:</b></p>
                            <span><?php echo htmlspecialchars($row['telefone']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="container-group mb-0">
                    <div class="card card-address mt-0">
                        <div class="img-icon-details">
                            <i class="fas fa-id-card"></i>
                        </div>
                        <div class="infos">
                            <p class="name mb-0"><b>RENACH AM:</b></p>
                            <span><?php echo htmlspecialchars($row['renach']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="container-group mb-0">
                    <div class="card card-address mt-0">
                        <div class="img-icon-details">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <div class="infos">
                            <p class="name mb-0"><b>L.A.D.V:</b></p>
                            <span><?php echo date('d/m/Y', strtotime($row['ladv'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="container-group mb-0">
                    <div class="card card-address mt-0">
                        <div class="img-icon-details">
                            <i class="fas fa-calendar-alt"></i>
                        </div>
                        <div class="infos">
                            <p class="name mb-0"><b>VENCIMENTO DO PROCESSO:</b></p>
                            <span><?php echo date('d/m/Y', strtotime($row['vencimento_processo'])); ?></span>
                        </div>
                    </div>
                </div>

                <div class="container-group mb-0">
                    <div class="card card-address mt-0">
                        <div class="img-icon-details">
                            <i class="fas fa-map-marked-alt"></i>
                        </div>
                        <div class="infos">
                            <p class="name mb-0"><b>ENDEREÇO:</b></p>
                            <span class="text mb-0"><?php echo htmlspecialchars($row['rua'] . ', ' . $row['bairro'] . ', ' . $row['numero']); ?></span>
                        </div>
                    </div>
                </div>

                <div class="container-group mb-0">
                    <div class="card card-address mt-0">
                        <div class="img-icon-details">
                            <i class="fas fa-sticky-note"></i>
                        </div>
                        <div class="infos">
                            <p class="name mb-0"><b>OBSERVAÇÃO:</b></p>
                            <span class="text mb-0"><?php echo htmlspecialchars($row['observacao']); ?></span>
                        </div>
                    </div>
                </div>


            </section>
            <!-- End Section 3 -->

        </section>
        <!-- End Section -->

        <!-- modal -->
        <div id="imageModal" class="modal">

            <!-- modal-content -->
            <div class="modal-content">

                <span class="close">&times;</span>
                <h2>Escolha uma Foto</h2>
                
                <!-- form -->
                <form action="updateFotoAluno.php" method="POST" enctype="multipart/form-data">

                    <input type="file" id="imageInput" name="image" accept="image/*" required>

                    <div class="preview-container">
                        <img id="imagePreview" class="preview" src="" alt="Prévia da Imagem">
                    </div>


                    <input type="hidden" name="id" value="<?php echo $id; ?>">

                    <div class="modal-footer">
                        <button type="submit" id="submitBtn">Enviar</button>
                        <button type="button" id="cancelBtn">Cancelar</button>
                    </div>

                </form>
                <!-- End form -->

            </div>
            <!-- End modal-content -->

        </div>
        <!-- End modal -->

        <!-- Scripts -->
        <script type="text/javascript" src="./js/bootstrap.bundle.min.js"></script>
        <script type="text/javascript" src="./js/item.js"></script>
        <script>

            const modal = document.getElementById("imageModal");
            const openModalBtn = document.getElementById("openModalBtn");
            const closeModalBtn = document.querySelector(".close");
            const imageInput = document.getElementById("imageInput");
            const imagePreview = document.getElementById("imagePreview");
            const cancelBtn = document.getElementById("cancelBtn");

            openModalBtn.onclick = function() {
                modal.style.display = "block";
            }

            closeModalBtn.onclick = function() {
                modal.style.display = "none";
            }

            cancelBtn.onclick = function() {
                modal.style.display = "none";
            }

            imageInput.onchange = function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.src = e.target.result;
                        imagePreview.style.display = "block";
                    }
                    reader.readAsDataURL(file);
                }
            }

            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            }
        </script>
        <!-- End Scripts -->

    </body>
</html>

