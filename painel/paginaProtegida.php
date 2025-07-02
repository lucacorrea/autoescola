
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Página Protegida</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos para o modal */
        .modal {
            display: none; /* Ocultar inicialmente o modal */
            position: fixed; /* Ficar posicionado de forma fixa na tela */
            z-index: 1; /* Colocar o modal acima de todo o conteúdo */
            left: 0;
            top: 0;
            width: 100%; /* Largura total */
            height: 100%; /* Altura total */
            overflow: auto; /* Adicionar rolagem quando necessário */
            background-color: rgba(0,0,0,0.7); /* Cor de fundo com transparência */
        }

        /* Conteúdo do modal */
        .modal-content {
            background-color: #fffff0;
            margin: 15% auto; /* Centralizar verticalmente */
            padding: 20px;
            border: 1px solid #22874c;
            border-radius: 10px;
            width: 50%; /* Largura do modal */
            height: 120px;
            text-align: center; /* Centralizar o conteúdo */
        }

        /* Botão de fechar */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }

        p {
            font-size: 21px;
            font-weight: 700;
        }

        /* Estilos para o botão OK */
        #redirectButton {
            background-color: #b80505;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 15px;
        }

        #redirectButton:hover {
            background-color: red;
        }
    </style>
</head>
<body>
    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p>Você não tem permissão para acessar esta página.</p>
            <button id="redirectButton">OK</button>
        </div>
    </div>

    <script>
        // Função para redirecionar o usuário para a página anterior
        function redirectToPreviousPage() {
            if (document.referrer) {
                window.location.href = document.referrer;
            } else {
                window.location.href = "home.php";
            }
        }

        // Obter o modal
        var modal = document.getElementById("myModal");

        // Obter o botão de fechar
        var span = document.getElementsByClassName("close")[0];

        // Obter o botão de redirecionamento
        var redirectButton = document.getElementById("redirectButton");

        // Quando o usuário clicar em qualquer lugar fora do modal, feche-o
        window.onclick = function(event) {
            if (event.target == modal) {
                redirectToPreviousPage();
            }
        }

        // Mostrar o modal
        modal.style.display = "block";

        // Fechar o modal quando o usuário clicar no botão de fechar ou no botão "OK"
        span.onclick = redirectToPreviousPage;
        redirectButton.onclick = redirectToPreviousPage;
    </script>
</body>
</html>
