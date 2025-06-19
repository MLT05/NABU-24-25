<script>
function buscarNotificacoes() {
    fetch('notificacoes.php')
    .then(res => res.json())
        .then(data => {
        const lista = document.getElementById("lista-notificacoes");
        const badge = document.getElementById("notificacao-badge");

        lista.innerHTML = "";
        let total = data.length;

            data.forEach(n => {
            let item = document.createElement("li");
                item.textContent = n.conteudo;
                lista.appendChild(item);
            });

            badge.textContent = total;
            badge.style.display = total > 0 ? "inline-block" : "none";
        });
}

// Atualiza a cada 5 segundos
setInterval(buscarNotificacoes, 5000);

// Primeira busca
document.addEventListener("DOMContentLoaded", buscarNotificacoes);
</script>