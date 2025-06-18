<div class="w-100">
    <img src="../Imagens/produtos/tomates.svg" alt="Tomates Cacho" class="img-fluid w-100">

</div>
<main class="container">
    <div class="mx-2">
        <h3 class="verde_escuro fw-bold my-3 fs-1">Tomates - Cacho</h3>
        <p class=" verde">Rosa Silva</p>

        <div>
            <div class="row">
                <div class="col-6">
                    <span class="etiqueta">Fruta</span>
                    <p class="text-warning">⭐ 4,9 <span class="verde_claro">(229)</span></p>
                </div>
                <div class="col-6 text-end">
                    <p class="fs-2 fw-bold verde_escuro">1,00€ <span>/kg</span></p>
                </div>
            </div>
        </div>
        <h2 class="verde_escuro fw-bold my-3 fs-4">Descrição do Produto</h2>
        <div>
            <p class="descricao" id="descricao">
                Tomates frescos, colhidos no próprio dia, diretamente da horta.
                Produzidos de forma natural, sem recurso a agrotóxicos, preservando
                todo o sabor e qualidade. Ideais para saladas, molhos ou para consumir ao natural.
            </p>
            <button id="toggleDescricao" class="ver-mais-btn">Ver mais</button>
        </div>

    <div>
        <h3 class="verde_escuro fw-bold my-3 fs-4"> Quantidade desejada</h3>
        <input type="number" id="quantidade" name="quantidade" class="input-quantidade rounded-3 p-3" placeholder="Ex: 1 kilo, 1 unidade...">
    </div>

    <h3 class="verde_escuro fw-bold my-3 fs-4">Localização</h3>
    <div class="d-flex">
        <button class="nome_localizacao rounded fs-5 p-3 verde_escuro">
            <img src="../Imagens/localizacao_simbolo.svg" alt="Localização" class="icone-localizacao" />
            Quinta da fonte - Lousã
        </button>
    </div>

        <div class="d-flex">
            <button class="contactar me-1 fs-6 p-2 bg-white rounded" onclick="window.location.href='../Paginas/carrinho.php'">Carrinho</button>
            <button class="contactar ms-1 fs-6 p-2 bg-white rounded" onclick="window.location.href='../Paginas/mensagens.php'">Contactar</button>
        </div>
        <div class="d-flex">
            <button class="comprar p-3 fs-6 rounded" onclick="window.location.href='../Paginas/carrinho.php'">Comprar</button>
        </div>
    </div>
</main>
<script>
    const botao = document.getElementById('toggleDescricao');
    const descricao = document.getElementById('descricao');

    botao.addEventListener('click', () => {
        descricao.classList.toggle('expandida');
        botao.textContent = descricao.classList.contains('expandida') ? 'Ver menos' : 'Ver mais';
    });
</script>