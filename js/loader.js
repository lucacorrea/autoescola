// Controle do Loader
window.addEventListener('load', function () {
    const loader = document.querySelector('.loader-full');

    // Esconde o loader após 1.5 segundos (1500 ms)
    setTimeout(function () {
        loader.classList.add('hidden');
        
        // Redireciona para outra página
        window.location.href = "inicio.php";
    }, 1500);
});