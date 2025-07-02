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
                            <a href="index.php" class="nav-item nav-link active">Home</a>
                            <a href="about.html" class="nav-item nav-link">Sobre</a>
                            <a href="service.html" class="nav-item nav-link">Serviços</a>
                            <a href="cars.php" class="nav-item nav-link">Categorias </a>
                            <a href="contact.html" class="nav-item nav-link">Contato</a>
                        </div>
                           
                    </div>
                </nav>
            </div>
        </div>
        <!-- Navbar & Hero End -->

        <!-- Carousel Start -->
        <div class="header-carousel">
            <div id="carouselId" class="carousel slide" data-bs-ride="carousel" data-bs-interval="2500" data-bs-wrap="true">
                <ol class="carousel-indicators">
                    <li data-bs-target="#carouselId" data-bs-slide-to="0" class="active" aria-current="true" aria-label="First slide"></li>
                    <li data-bs-target="#carouselId" data-bs-slide-to="1" aria-label="Second slide"></li>
                </ol>
                <div class="carousel-inner" role="listbox">
                    <div class="carousel-item active">
                        <img src="img/carousel-2.png" class="img-fluid w-100" alt="First slide" />
                        <div class="carousel-caption">
                            <div class="container py-4">
                                <div class="row g-5">
                                    <div class="col-lg-6 d-lg-flex fadeInRight animated" style="animation-delay: 1s;">
                                        <div class="text-start">
                                            <h1 class="display-5 text-white">Auto Escola Dinâmica</h1>
                                            <p>Coari-Amazonas</p>
                                            <a href="./login.php" target="_blank"  class="btn btn-primary rounded-pill me-3 py-2 px-5">Login</a>
                                            <a href="./criarConta.php" target="_blank"  class="btn btn-primary rounded-pill py-2 px-4">Criar Conta</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="carousel-item">
                        <img src="img/carousel-1.jpg" class="img-fluid w-100" alt="Second slide" />
                        <div class="carousel-caption">
                            <div class="container py-4">
                                <div class="row g-5">
                                    <div class="col-lg-6 d-lg-flex fadeInRight animated" style="animation-delay: 1s;">
                                        <div class="text-start">
                                            <h1 class="display-5 text-white">Auto Escola Dinâmica</h1>
                                            <p>Coari-Amazonas</p>
                                            <a href="./login.php" target="_blank"  class="btn btn-primary rounded-pill me-2 py-2 px-5">Login</a>
                                            <a href="./criarConta.php" target="_blank"  class="btn btn-primary rounded-pill py-2 px-4">Criar Conta</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Carousel End -->

        <!-- Features Start -->
        <div class="container-fluid feature py-5">
            <div class="container py-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                    <h1 class="display-5  mb-3"> Vem para a <span class="text-primary">Dinâmica!</span></h1>
                    <p class="mb-0">
                       Aqui, formamos motoristas que fazem a diferença no trânsito. Sua jornada começa agora. Agende sua aula e descubra o que significa aprender com os melhores!
                    </p>
                </div>
                <div class="row g-4 align-items-center">
                    <div class="col-xl-4">
                        <div class="row gy-4 gx-0">
                            <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <span class="fa fa-trophy fa-2x"></span>
                                    </div>
                                    <div class="ms-4">
                                        <h5 class="mb-3">Serviços de Primeira </h5>
                                        <p class="mb-0  text-justify">
                                            Na nossa autoescola, você conta com serviços de primeira classe, garantindo aprendizado de qualidade,
                                             atendimento personalizado e uma experiência completa para sua formação no trânsito.
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 wow fadeInUp" data-wow-delay="0.3s">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <span class="fa fa-road fa-2x"></span>
                                    </div>
                                    <div class="ms-4">
                                        <h5 class="mb-3">Assistência 24 horas por dia, 7 dias por semana
                                        </h5>
                                        <p class="mb-0  text-justify">
                                            Oferecemos assistência 24 horas por dia, 7 dias por semana, para garantir que você tenha suporte sempre que precisar, a
                                             qualquer momento e em qualquer lugar.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 col-xl-4 wow fadeInUp" data-wow-delay="0.2s">
                       
                    </div>
                    <div class="col-xl-4">
                        <div class="row gy-4 gx-0">
                            <div class="col-12 wow fadeInUp" data-wow-delay="0.1s">
                                <div class="feature-item justify-content-end">
                                    <div class="text me-4">
                                      <h5 class="mb-3">Excelência no Máximo</h5>
                                        <p class="mb-0 text-justify">
                                            Oferecemos a melhor infraestrutura, instrutores altamente capacitados e um atendimento impecável, garantindo 
                                            que sua experiência conosco seja sempre excepcional, do início ao fim.
                                        </p>

                                    </div>
                                    <div class="feature-icon">
                                        <span class="fa fa-tag fa-2x"></span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 wow fadeInUp" data-wow-delay="0.3s">
                                <div class="feature-item justify-content-end">
                                    <div class="text me-4">
                                        <h5 class="mb-3">Segurança</h5>
                                        <p class="mb-0 text-justify">
                                            Em todas as etapas do processo. Utilizamos veículos modernos e bem mantidos, com acompanhamento constante de 
                                            profissionais treinados, para garantir que você aprenda e se desenvolva com total confiança e tranquilidade.
                                        </p>
                                    </div>
                                    <div class="feature-icon">
                                        <span class="fa fa-map-pin fa-2x"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Features End -->

        <!-- About Start -->
        <div class="container-fluid overflow-hidden about py-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-xl-6 wow fadeInLeft" data-wow-delay="0.2s">
                        <div class="about-item">
                            <div class="pb-5">
                                <h1 class="display-5 text-capitalize"><span class="text-primary">Sobre</span> Dinâmica</h1>
                                <p class="mb-0 text-justify">
                                    A Autoescola Dinâmica é referência em formação de condutores, oferecendo ensino de qualidade com
                                     instrutores capacitados e atendimento personalizado. Com infraestrutura moderna e veículos bem
                                      mantidos, prioriza a segurança e o aprendizado responsável. Além disso, proporciona suporte 
                                      contínuo para garantir que cada aluno se sinta preparado em todas as etapas da habilitação.
                                </p>
                            </div>
                            <div class="row g-4">
                                <div class="col-lg-6">
                                    <div class="about-item-inner border p-4">
                                        <div class="about-icon mb-4">
                                            <img src="img/about-icon-1.png" class="img-fluid w-50 h-50" alt="Icon">
                                        </div>
                                        <h5 class="mb-3">Nossa Visão</h5>
                                        <p class="mb-0 text-justify">Ser a autoescola mais confiável e inovadora de Coari, promovendo educação no trânsito com excelência.</p>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="about-item-inner border p-4">
                                        <div class="about-icon mb-4">
                                            <img src="img/about-icon-2.png" class="img-fluid h-50 w-50" alt="Icon">
                                        </div>
                                        <h5 class="mb-3">Nossa Missão</h5>
                                        <p class="mb-0 text-justify">Garantir que nossos alunos recebam a melhor formação no trânsito, com foco em segurança, responsabilidade e confiança.</p>
                                    </div>
                                </div>
                            </div>
                            <div class="row g-4 mt-4">
                                <div class="col-lg-6">
                                    <div class="text-center rounded bg-secondary p-4">
                                        <h1 class="display-6 text-white">+10</h1>
                                        <h5 class="text-light mb-0">Anos de Experiência</h5>
                                    </div>
                                </div>
                                <div class="col-lg-6">
                                    <div class="rounded">
                                        <p class="mb-2"><i class="fa fa-check-circle text-primary me-1"></i> Instrutores altamente qualificados</p>
                                        <p class="mb-2"><i class="fa fa-check-circle text-primary me-1"></i> Veículos modernos e seguros</p>
                                        <p class="mb-2"><i class="fa fa-check-circle text-primary me-1"></i> Atendimento personalizado</p>
                                        <p class="mb-0"><i class="fa fa-check-circle text-primary me-1"></i> Suporte 24 horas por dia</p>
                                    </div>
                                </div>
                                <div class="col-lg-5 d-flex align-items-center">
                                    <a href="about.html" class="btn btn-primary rounded py-3 px-5">Mais Sobre a Gente</a>
                                </div>
                                <div class="col-lg-7">
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6 wow fadeInRight" data-wow-delay="0.2s">
                        <div class="about-img">
                            <div class="img-1">
                                <img src="img/about-img.jpg" class="img-fluid rounded h-100 w-100" alt="">
                            </div>
                            <div class="img-2">
                                <img src="img/about-img-1.jpg" class="img-fluid rounded w-100" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- About End -->

        <!-- Fact Counter -->
        <div class="container-fluid counter bg-secondary py-5">
            <div class="container py-5">
                <div class="row g-5">
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="counter-item text-center">
                            <div class="counter-item-icon mx-auto">
                                <i class="fas fa-thumbs-up fa-2x"></i>
                            </div>
                            <div class="counter-counting my-3">
                                 <span class="h1 fw-bold text-white">+</span>
                                <span class="text-white fs-2 fw-bold" data-toggle="counter-up"> 829</span>
                            </div>
                            <h4 class="text-white mb-0">Clientes Felizes</h4>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="counter-item text-center">
                            <div class="counter-item-icon mx-auto">
                                <i class="fas fa-car-alt fa-2x"></i>
                            </div>
                            <div class="counter-counting my-3">
                                <span class="h1 fw-bold text-white">+</span>
                                <span class="text-white fs-2 fw-bold" data-toggle="counter-up"> 7</span>                               
                            </div>
                            <h4 class="text-white mb-0">Numeros de Veiculos</h4>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="counter-item text-center">
                            <div class="counter-item-icon mx-auto">
                                <i class="fas fa-user-tie fa-2x"></i>
                            </div>
                            <div class="counter-counting my-3"> 
                                <span class="h1 fw-bold text-white">+</span>
                                <span class="text-white fs-2 fw-bold" data-toggle="counter-up"> 7</span>                              
                            </div>
                            <h4 class="text-white mb-0">Instrutores</h4>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-6 col-xl-3 wow fadeInUp" data-wow-delay="0.7s">
                        <div class="counter-item text-center">
                            <div class="counter-item-icon mx-auto">
                                <i class="fas fa-clock fa-2x"></i>
                            </div>
                            <div class="counter-counting my-3">
                                <span class="h1 fw-bold text-white">+</span>
                                <span class="text-white fs-2 fw-bold" data-toggle="counter-up"> 10</span>                               
                            </div>
                            <h4 class="text-white mb-0">Anos de Experiencias</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Fact Counter -->

        <!-- Services Start -->
        <div class="container-fluid service py-5">
            <div class="container py-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                    <h1 class="display-5 text-capitalize mb-3">Serviços da <span class="text-primary">Dinâmica</span></h1>
                    <p class="mb-0 text-justify">
                        Na Autoescola Dinâmica, oferecemos uma ampla gama de serviços que garantem qualidade, segurança e flexibilidade 
                        para os nossos alunos. 
                        Do atendimento personalizado às aulas práticas, trabalhamos para fornecer a melhor experiência de aprendizagem.
                    </p>
                </div>
                <div class="row g-4">
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item p-4">
                            <div class="service-icon mb-4">
                                <i class="fa fa-phone-alt fa-2x"></i> <!-- Ícone de telefone -->
                            </div>
                            <h5 class="mb-3">Reserva por telefone</h5>
                            <p class="mb-0 text-justify">Agende suas aulas práticas de forma rápida e fácil, diretamente pelo telefone, 
                                com total comodidade.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="service-item p-4">
                            <div class="service-icon mb-4">
                                <i class="fa fa-tags fa-2x"></i> <!-- Ícone de etiquetas para simbolizar preços -->
                            </div>
                            <h5 class="mb-3">Tarifas Especiais</h5>
                            <p class="mb-0 text-justify">Oferecemos preços acessíveis e planos flexíveis para garantir que todos
                                possam obter sua habilitação com qualidade.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="service-item p-4">
                            <div class="service-icon mb-4">
                                <i class="fa fa-user-check fa-2x"></i> <!-- Ícone de check para simbolizar matrícula simplificada -->
                            </div>
                            <h5 class="mb-3">Matrículas Simplificadas</h5>
                            <p class="mb-0 text-justify">Processo de matrícula rápido e simplificado, garantindo que você comece suas 
                                aulas o mais breve possível.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.1s">
                        <div class="service-item p-4">
                            <div class="service-icon mb-4">
                                <i class="fa fa-shield-alt fa-2x"></i> <!-- Ícone de escudo para simbolizar segurança -->
                            </div>
                            <h5 class="mb-3">Aulas Seguras</h5>
                            <p class="mb-0 text-justify">Nossos veículos são equipados e 
                                revisados para garantir segurança máxima durante as aulas práticas.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.3s">
                        <div class="service-item p-4">
                            <div class="service-icon mb-4">
                                <i class="fa fa-chalkboard-teacher fa-2x"></i> <!-- Ícone de quadro para simbolizar aulas teóricas -->
                            </div>
                            <h5 class="mb-3">Diversas Aulas</h5>
                            <p class="mb-0 text-justify">Oferecemos aulas teóricas e práticas variadas, adaptadas às necessidades
                                de cada aluno.
                            </p>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4 wow fadeInUp" data-wow-delay="0.5s">
                        <div class="service-item p-4">
                            <div class="service-icon mb-4">
                                <i class="fa fa-user-tie fa-2x"></i> <!-- Ícone de profissional para simbolizar instrutores -->
                            </div>
                            <h5 class="mb-3">Instrutores Profissionais</h5>
                            <p class="mb-0 text-justify">Equipe de instrutores altamente qualificados e certificados, prontos para
                                oferecer a melhor orientação.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>        
        <!-- Services End -->

        <!-- Car categories Start -->
        <div class="container-fluid categories pb-5">
            <div class="container pb-5">
                <div class="text-center mx-auto pb-5 wow fadeInUp" data-wow-delay="0.1s" style="max-width: 800px;">
                    <h1 class="display-5 text-capitalize mb-3">
                        <span class="text-primary">Categorias</span> Disponíveis
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
        <!-- Car Steps End -->

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
                            <a target="_blank" href="https://wa.me/5597999022498?text=Olá,%20gostaria%20de%20mais%20informações%20sobre%20a%20matrícula!" class="btn btn-secondary rounded-pill py-3 px-4 px-md-5 me-2">Whatsapp</a>
                            <a href="https://www.instagram.com/_autoescola_dinamica?igsh=dGQybjdxcHZ5ejMx" class="btn btn-primary rounded-pill py-3 px-4 px-md-5 ms-2">Instagram</a>
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
    

    <script>
        var myCarousel = document.getElementById('carouselId');
        var carousel = new bootstrap.Carousel(myCarousel, {
        interval: 2500, // Tempo entre os slides (3 segundos)
        wrap: true      // Ativa o loop
        });
    </script>
    
    <!-- Template Javascript -->
    <script src="js/main.js"></script>
    </body>

</html>