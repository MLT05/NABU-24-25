<?php

?>
<div class="body_index">
    <a href="javascript:history.back()" class=" text-decoration-none d-inline-flex " >
        <span class="material-icons verde_escuro" style="font-size: 2.5rem">arrow_back</span>

    </a>
    <h2 class="mb-4">Mensagens</h2>

    <div class="mb-4">
        <textarea id="mensagem" class="form-control mb-2" placeholder="Digite sua mensagem..."></textarea>
        <input type="number" id="ref_produto" class="form-control mb-2" placeholder="ID do produto">
        <input type="number" id="destinatario" class="form-control mb-2" placeholder="ID do destinatário">
        <button class="btn btn-primary" onclick="enviarMensagem()">Enviar</button>
    </div>

    <h4>Mensagens Recebidas</h4>
    <ul id="mensagens" class="list-group">
        <!-- mensagens via AJAX -->
    </ul>
</div>

<script>
    function carregarMensagens() {
        const ref_produto = document.getElementById('ref_produto').value;
        const destinatario = document.getElementById('destinatario').value;

        if (!ref_produto || !destinatario) {
            document.getElementById('mensagens').innerHTML = '<li class="list-group-item text-muted">Informe o produto e o destinatário para carregar as mensagens.</li>';
            return;
        }

        fetch(`../scripts/obter_mensagens.php?ref_produto=${ref_produto}&outro_usuario=${destinatario}`)
            .then(res => res.json())
            .then(data => {
                const lista = document.getElementById('mensagens');
                lista.innerHTML = '';
                data.forEach(msg => {
                    const li = document.createElement('li');
                    li.className = `list-group-item ${msg.is_read == 0 ? 'list-group-item-warning' : ''}`;
                    li.innerHTML = `<strong>De:</strong> ${msg.remetente_nome}<br>${msg.mensagem}<br><small>${msg.data_envio}</small>`;
                    li.onclick = () => marcarComoLida(msg.id_mensagem);
                    lista.appendChild(li);
                });
            });
    }

    function enviarMensagem() {
        const mensagem = document.getElementById('mensagem').value;
        const destinatario = document.getElementById('destinatario').value;
        const ref_produto = document.getElementById('ref_produto').value;

        if (!mensagem || !destinatario || !ref_produto) {
            alert('Por favor, preencha mensagem, destinatário e produto.');
            return;
        }

        fetch('../scripts/enviar_mensagens.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({mensagem, destinatario, ref_produto})
        }).then(() => {
            document.getElementById('mensagem').value = '';
            carregarMensagens();
        });
    }

    function marcarComoLida(id) {
        fetch('../scripts/marcar_lida.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({id})
        }).then(() => carregarMensagens());
    }

    carregarMensagens();
    setInterval(carregarMensagens, 5000);
</script>
</body>
</html>
