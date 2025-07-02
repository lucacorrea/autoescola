function enviarMensagem() {
    var numero = '+5597999022498'; // Seu número de telefone (inclua o código do país)
    var mensagem = 'Olá! Ainda não sou aluno, mas estou interessado em saber mais sobre os serviços da autoescola. Poderia me fornecer mais informações? Obrigado!'; // Sua mensagem

    // Construa o link para o WhatsApp com o número e a mensagem
    var url = 'https://api.whatsapp.com/send?phone=' + numero + '&text=' + encodeURIComponent(mensagem);

    // Abre em uma nova janela no desktop
    var novaAba = window.open(url, '_blank');
}

function renovarParcela() {
    var numero = '+5597999022498'; // Seu número de telefone (inclua o código do país)
    var mensagem = 'Olá, gostaria de renovar minha parcela e preciso de mais informações sobre o processo. Poderiam me ajudar com isso?'; // Sua mensagem

    // Construa o link para o WhatsApp com o número e a mensagem
    var url = 'https://api.whatsapp.com/send?phone=' + numero + '&text=' + encodeURIComponent(mensagem);

    // Abre em uma nova janela no desktop
    var novaAba = window.open(url, '_blank');
}