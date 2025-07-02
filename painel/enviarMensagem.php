<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dados = json_decode(file_get_contents('php://input'), true);
    $nome = $dados['nome'];
    $email = $dados['email'];
    $mensagem = $dados['mensagem'];

    $assunto = "Parabéns, $nome! 🎉";
    $mensagemEmail = "Olá $nome,\n\nParabéns pelo seu aniversário! 🎉\n\nA Autoescola Dinâmica deseja a você um dia repleto de alegrias e momentos especiais. Aproveitamos a oportunidade para expressar nossos votos de sucesso e prosperidade, tanto em sua jornada conosco quanto em suas futuras conquistas. Estamos felizes em tê-lo como parte da nossa equipe e contamos com seu empenho e dedicação para alcançar os melhores resultados.\n\nAtenciosamente,\nAutoescola Dinâmica";
    $headers = "From: Autoescola Dinâmica <no-reply@autoescola.com>\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    if (mail($email, $assunto, $mensagemEmail, $headers)) {
        atualizarStatusParabenizado($nome);
        echo json_encode(['sucesso' => true]);
    } else {
        echo json_encode(['sucesso' => false]);
    }
}

function atualizarStatusParabenizado($nome) {
    include 'conexao.php'; // Inclui a conexão com PDO

    try {
        // Atualiza o status para 'Parabenizado'
        $sql = "UPDATE login_aluno SET status_cadastro = 'Parabenizado' WHERE nome_aluno = :nome";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->execute();

        // Agenda a remoção do status
        agendarRemocaoStatus($nome, $conn);
    } catch (PDOException $e) {
        die("Erro ao atualizar status: " . $e->getMessage());
    }
}

function agendarRemocaoStatus($nome, $conn) {
    try {
        // Cria um evento para resetar o status após 1 dia
        $sql = "CREATE EVENT IF NOT EXISTS resetar_status_" . md5($nome) . "
            ON SCHEDULE AT CURRENT_TIMESTAMP + INTERVAL 1 DAY
            DO
            UPDATE login_aluno SET status_cadastro = NULL WHERE nome_aluno = :nome";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':nome', $nome, PDO::PARAM_STR);
        $stmt->execute();
    } catch (PDOException $e) {
        die("Erro ao agendar remoção de status: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Parabenizar Alunos</title>

        <link rel="stylesheet" href="../css/bootstrap.min.css" />
        <link rel="stylesheet" href="../css/fontawesome.css" />
        <link rel="stylesheet" href="../css/animate.css" />
        <link rel="stylesheet" href="../css/main.css" />
        <link rel="stylesheet" href="./css/painel.css" />
        <link rel="stylesheet" href="./css/dashboard.css">
        <link rel="stylesheet" href="./css/notification.css">
        <link rel="stylesheet" href="./css/formMensagem.css">
        <script src="https://cdn.emailjs.com/dist/email.min.js"></script>
    </head>


    <body>

        <section class="bg-menu">

            <div class="conteudo" style="margin-left: -240px;">
                <div class="menu-top">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 d-flex align-items-center mt-4">
                                <h1 class="title-page">
                                    <b>
                                        <i class="fas fa-envelope"></i>&nbsp; ENVIAR MENSAGEM
                                    </b>
                                </h1>
                                <div class="container-right">
                                    <a href="home.php" class="btn btn-white btn-sm">
                                        <i class="fas fa-sign-out-alt"></i>&nbsp; Sair
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <main class="main-container">

                    <div class="main-title">
                        <h2></h2>
                    </div>

                    <div class="conteudo-inner">
                        <div class="container">
                            <div class="notification" id="notification">Há alunos fazendo aniversário! Clique aqui para parabenizá-los.</div>

                            <label for="aniversariante" style="color: #000;">Escolha os aniversariantes:</label>
                            <select id="aniversariante" required>
                                <option value="" selected>Selecionar Aniversariantes</option>
                            </select>

                            <form id="email-form">
                                <input type="text" name="nome" id="nome" placeholder="Nome do Aluno" required readonly>
                                <input type="email" name="email" id="email" placeholder="E-mail do Aluno" required readonly>
                                <textarea name="mensagem" placeholder="Sua Mensagem" required>AUTOESCOLA DINÂMICA</textarea>
                                <button type="submit"><i class="fas fa-gift"></i>&nbsp;Parabenizar</button>
                            </form>

                        </div>
                    </div>
                </main>

            </div>

        </section>

        <script src="../js/jquery.min.js"></script>
        <script src="../js/jquery-ui.js"></script>
        <script type="text/javascript" src="../js/bootstrap.bundle.min.js"></script>
        <script src="./js/script.js"></script>
        <script src="./js/logout.js"></script>
        <script>

            document.addEventListener('DOMContentLoaded', function() {
                fetch('mensagemEmail.php')
                .then(response => response.json())
                .then(data => {
                    if (data.length) {
                        preencherSelect(data);
                    } else {
                        alert('Não há aniversariantes hoje.');
                    }
                });
            });

            function preencherSelect(aniversariantes) {
                const select = document.getElementById('aniversariante');
                aniversariantes.forEach(aluno => {
                    const option = document.createElement('option');
                    option.value = aluno.email;
                    option.textContent = aluno.nome;
                    option.dataset.nome = aluno.nome;
                    select.appendChild(option);
                });
            }

            document.getElementById('aniversariante').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                document.getElementById('nome').value = selectedOption.dataset.nome || "";
                document.getElementById('email').value = selectedOption.value || "";
            });

            document.getElementById('email-form').addEventListener('submit', function(event) {
                event.preventDefault();

                const nome = document.getElementById('nome').value.trim();
                const email = document.getElementById('email').value.trim();
                const mensagem = document.querySelector('textarea').value.trim();

                if (!nome || !email || !mensagem) {
                    alert('Por favor, preencha todos os campos.');
                    return;
                }

                alert('Enviando mensagem, por favor aguarde...');

                fetch('', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({nome, email, mensagem})
                })
                .then(res => res.json())
                .then(res => {
                    if (res.sucesso) {
                        alert('Mensagem enviada com sucesso!');
                    } else {
                        alert('Falha no envio: ' + (res.erro || 'Erro desconhecido.'));
                    }
                })
                .catch(() => {
                    alert('Mensagem enviada com sucesso!');
                });
            });

        </script>

    </body>

</html>