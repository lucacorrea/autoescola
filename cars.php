<?php
include 'conexao.php';

$sql = "SELECT * FROM categorias";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

    <head>
        <meta charset="utf-8">
        <title>Auto Escola Dinâmica</title>
        <link rel="shortcut icon" href="img/logotipo.png" type="image/x-icon">
        <meta content="width=device-width, initial-scale=1.0" name="viewport">
        <meta content="" name="keywords">
        <meta content="" name="description">

        <!-- Google Web Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,400;0,700;0,900;1,400;1,700;1,900&family=Montserrat:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet"> 

        <!-- Icon Font Stylesheet -->
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css" rel="stylesheet">

        <!-- Libraries Stylesheet -->
        <link href="lib/animate/animate.min.css" rel="stylesheet">
        <link href="lib/owlcarousel/assets/owl.carousel.min.css" rel="stylesheet">


        <!-- Customized Bootstrap Stylesheet -->
        <link href="./css/2.0bootstrap.min.css" rel="stylesheet">



        <!-- Template Stylesheet -->
    
        <link href="./css/estilo.css" rel="stylesheet">
    </head>

    <body>

        <!-- Spinner Start -->
        <div id="spinner" class="show bg-white position-fixed translate-middle w-100 vh-100 top-50 start-50 d-flex align-items-center justify-content-center">
            <div class="spinner-border text-primary" style="width: 3rem; height: 3rem;" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
        <!-- Spinner End -->

        <!-- Topbar Start -->
          <div class="container-fluid topbar bg-secondary d-none d-xl-block w-100">
            <div class="container">
                <div class="row gx-0 align-items-center" style="height: 45px;">
                    <div class="col-lg-6 text-center text-lg-start mb-lg-0">
                        
                    </div>
                    <div class="col-lg-6 text-center text-lg-end">
                        <div class="d-flex align-items-center justify-content-end">
                            <a target="_blank" href="https://wa.me/5597999022498?text=Olá,%20gostaria%20de%20mais%20informações!" class="btn btn-light btn-sm-square rounded-circle me-3"><i class="fab fa-whatsapp"></i></a>
                            <a target="_blank" href="mailto:autoescoladinamica918@gmail.com" class="btn btn-light btn-sm-square rounded-circle me-3"><i class="fas fa-envelope"></i></a>
                            <a target="_blank" href="https://www.instagram.com/_autoescola_dinamica?igsh=dGQybjdxcHZ5ejMx"  class="btn btn-light btn-sm-square rounded-circle me-3"><i class="fab fa-instagram"></i></a>  
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Topbar End -->

        <!-- Navbar & Hero Start -->
        <div class="container-fluid nav-bar sticky-top px-0 px-lg-4 py-2 py-lg-0">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <a href="" class="navbar-brand p-0">
                         <img src="img/logotipo.png" alt="Logo">
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse">
                        <span class="fa fa-bars"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarCollapse">
                        <div class="navbar-nav mx-auto py-0">
                            <a href="index.php" class="nav-item nav-link">Home</a>
                            <a href="about.html" class="nav-item nav-link">Sobre</a>
                            <a href="service.html" class="nav-item nav-link">Serviços</a>
                            <a href="cars.php" class="nav-item nav-link active">Categorias </a>
                            <a href="contact.html" class="nav-item nav-link">Contato</a>
                        </div>
                            <a href="./login.php" target="_blank"  class="btn btn-primary rounded-pill me-3 py-2 px-5">Login</a>
                            <a href="./criarConta.php" target="_blank"  class="btn btn-primary rounded-pill py-2 px-4">Criar Conta</a>
                    </div>
                </nav>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <!-- Header Start -->
        <div class="container-fluid bg-breadcrumb mb-5">
            <div class="container text-center py-5" style="max-width: 900px;">
                <h4 class="text-white display-4 mb-4 wow fadeInDown" data-wow-delay="0.1s">Categorias</h4>
                <ol class="breadcrumb d-flex justify-content-center mb-0 wow fadeInDown" data-wow-delay="0.3s">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active text-primary">Categorias</li>
                </ol>    
            </div>
        </div>
        <!-- Header End -->
         
        <!-- Car categories Start -->
        <div class="container-fluid categories pb-5 mt-5">
            <div class="container pb-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                    <h1 class="display-5 text-capitalize mb-5">
                        <span class="text-primary mt-5">Categorias</span> Disponíveis
                    </h1>
                    <p class="mb-0 text-justify">
                        Oferecemos as categorias de CNH para atender às suas necessidades de aprendizado de direção. 
                        Desde as categorias básicas até as especializações, nossa equipe está pronta para ajudar você a conquistar sua habilitação.
                    </p>
                </div>

                <div class="categories-carousel owl-carousel wow fadeInUp" data-wow-delay="0.1s">
                    <?php while($row = $result->fetch_assoc()): ?>
                    <div class="categories-item p-4">
                        <div class="categories-item-inner">
                            <div class="categories-img rounded-top">
                                <img src="<?= $row['imagem'] ?>" class="img-fluid w-100 rounded-top" alt="<?= $row['nome'] ?>">
                            </div>
                            <div class="categories-content rounded-bottom p-4">
                                <h4><?= $row['nome'] ?></h4>
                                <!-- Badge com lógica para 3 cores -->
                                <?php $badgeClass = $row['status'] === 'Disponível' ? 'success' : ($row['status'] === 'Indisponível' ? 'danger' : 'warning');?>
                                    <span class="badge bg-<?= $badgeClass ?> text-white mb-4"><?= $row['status'] ?></span>
                                <div class="mb-4">
                                    <h4 class="bg-white text-primary rounded-pill py-2 px-4 mb-0">R$ <?= number_format($row['preco'], 2, ',', '.') ?>/Curso</h4>
                                </div>
                                <div class="row gy-2 gx-0 text-center mb-4">
                                    <p class="mb-0">R$<?= $row['parcelado'] ?> Parcelado de até 3x com Entrada</p>
                                </div>
                                <a target="_blank" href="https://wa.me/5597999022498?text=Olá,%20gostaria%20de%20mais%20informações%20sobre%20as%20categorias!" class="btn btn-primary rounded-pill d-flex justify-content-center py-3">Saiba Mais</a>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
            </div>
            <?php $conn->close(); ?>  
        </div>
        <!-- Car categories End -->

        <!-- Banner Start -->
        <div class="container-fluid banner pb-5 wow zoomInDown" data-wow-delay="0.1s">
            <div class="container pb-5">
                <div class="banner-item rounded">
                    <img src="img/banner-1.jpg" class="img-fluid rounded w-100" alt="">
                    <div class="banner-content">
                        <h2 class="text-primary">Faça sua Matricula</h2>
                        <h1 class="text-white">Interessado em se Matricular?</h1>
                        <p class="text-white">Não hesite e envie-nos uma mensagem.</p>
                        <div class="banner-btn">
                            <a href="#" class="btn btn-secondary rounded-pill py-3 px-4 px-md-5 me-2">Whatsapp</a>
                            <a href="#" class="btn btn-primary rounded-pill py-3 px-4 px-md-5 ms-2">Instagram</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Banner End -->

        <!-- Footer Start -->
        <div class="container-fluid footer py-5 wow fadeIn" data-wow-delay="0.2s">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <div class="footer-item">
                                <h4 class="text-white mb-4">Sobre Nós</h4>
                                
                                <p class="text-justify mb-3">
                                    A Autoescola Dinâmica é referência em formação de condutores, com ensino de qualidade, instrutores capacitados, infraestrutura moderna e foco na segurança e aprendizado responsável.</p>
                            </div>
                            
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                    <div class="footer-item d-flex flex-column">
                            <h4 class="text-white mb-4">Links Rapidos</h4>
                            <a href="index.php" class="fas fa-angle-right me-2">Home</a>
                            <a href="about.html" class="fas fa-angle-right me-2">Sobre</a>
                            <a href="service.html" class="fas fa-angle-right me-2">Serviços</a>
                            <a href="feature.html" class="fas fa-angle-right me-2">Recursos</a>
                            <a href="cars.php" class="fas fa-angle-right me-2">Categorias </a>
                            <a href="contact.html" class="fas fa-angle-right me-2">Contato</a>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="text-white mb-4">Horário Comercial</h4>
                            <div class="mb-3">
                                <h6 class="text-muted mb-0">Seg - Sexta-feira:</h6>
                                <p class="text-white mb-0">08:00 ás 12:00</p>
                                <p class="text-white mb-0">14:30 ás 18:00</p>
                            </div>
                            
                            <div class="mb-3">
                                <h6 class="text-muted mb-0">Férias:</h6>
                                <p class="text-white mb-0">Todos Sábados e Domingos</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3">
                        <div class="footer-item d-flex flex-column">
                            <h4 class="text-white mb-4">Informações de Contato</h4>
                            <!-- Link que abre o modal -->
                            <a href="#" data-bs-toggle="modal" data-bs-target="#mapModal">
                                <i class="fa fa-map-marker-alt me-2"></i> RUA 5 SETEMBRO, 161 - CENTRO, COARI/AM
                            </a>

                            <!-- Modal -->
                            <div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="mapModalLabel">Localização no Google Maps</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body">
                                            <!-- Google Maps Iframe -->
                                            <div class="ratio ratio-16x9">
                                                <iframe 
                                                   src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d739.7130686774076!2d-63.1439846!3d-4.0849668!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x921669b27d1a451f%3A0x26d80bcb9fb2a79f!2sAuto%20Escola%20Din%C3%A2mica!5e1!3m2!1spt-BR!2sbr!4v1733344876345!5m2!1spt-BR!2sbr"
                                                    style="border:0;" 
                                                    allowfullscreen="" 
                                                    loading="lazy">
                                                </iframe>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            <a href="tel:+55 97999022498"><i class="fas fa-phone me-2"></i> (97) 999022498</a>
                            
                            <div class="d-flex">
                                <a class="btn btn-secondary btn-md-square rounded-circle me-3" target="_blank" href="https://www.instagram.com/_autoescola_dinamica?igsh=dGQybjdxcHZ5ejMx"><i class="fab fa-instagram text-white"></i></a>
                                <a class="btn btn-secondary btn-md-square rounded-circle me-0" target="_blank" href="https://wa.me/5597999022498?text=Olá,%20gostaria%20de%20mais%20informações!"><i class="fab fa-whatsapp text-white"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Footer End -->
        
        <!-- Copyright Start -->
        <div class="container-fluid copyright py-4">
            <div class="container">
                <div class="row g-4 align-items-center">
                    <div class="col-md-6 text-center text-md-start mb-md-0">
                        <span class="text-body"><a href="https://codegeek.dev.br" class="border-bottom text-white" target="_blank"><i class="fas fa-copyright text-light me-2"></i>codegeek.dev.br</a>, Todos Direitos Reservados.</span>
                    </div>
                    <div class="col-md-6 text-center text-md-end text-body">
                        
                        Criado por <a class="border-bottom text-white" href="https://codegeek.dev.br" target="_blank">Codegeek</a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Copyright End -->



        <!-- Back to Top -->
        <a href="#" class="btn btn-secondary btn-lg-square rounded-circle back-to-top"><i class="fa fa-arrow-up"></i></a>   

        
    <!-- JavaScript Libraries -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.4/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="lib/wow/wow.min.js"></script>
    <script src="lib/easing/easing.min.js"></script>
    <script src="lib/waypoints/waypoints.min.js"></script>
    <script src="lib/counterup/counterup.min.js"></script>
    <script src="lib/owlcarousel/owl.carousel.min.js"></script>
    

    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    </body>

</html>