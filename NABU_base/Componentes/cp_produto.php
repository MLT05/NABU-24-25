
<main>

<div class="carrossel">
    <img src="../Imagens/produtos/tomates.svg" alt="Tomates Cacho">
    <button class="favorito">
        <img src="../Imagens/coracao.svg">
    </button>
</div>
    <div class="info-produto">
        <h3 class="h3.produto">Tomates - Cacho</h3>
        <p class="produtor">Rosa Silva</p>


        <div class="linha-preco-etiquetas">

        <div class="etiquetas">
            <div class="etiq">
                <span class="etiqueta">Fruta</span>


                <p class="avaliacao">⭐ 4,9 <span class="avaliacao_numero">(229)</span></p>
            </div>
            <div class="etiq">
                <p class="preco">1,00€ <span class="kg">/kg</span></p>
            </div>

        </div>


        </div>

        <h2 class="h2.produto">Descrição do Produto</h2>
        <div class="descricao-wrapper">
            <p class="descricao" id="descricao">
                Tomates frescos, colhidos no próprio dia, diretamente da horta.
                Produzidos de forma natural, sem recurso a agrotóxicos, preservando
                todo o sabor e qualidade. Ideais para saladas, molhos ou para consumir ao natural.
            </p>
            <button id="toggleDescricao" class="ver-mais-btn">Ver mais</button>
        </div>

    <div class="quantidade-wrapper">
        <h3 class="h2.produto"> Quantidade desejada</h3>
        <input type="text" id="quantidade" name="quantidade" class="input-quantidade" placeholder="Ex: 1 kilo, 1 unidade...">
    </div>

    <h3 class="h2.produto" >Localização</h3>
    <div class="botoes-localizacao">
        <button class="nome_localizacao">
            <img src="../Imagens/localizacao_simbolo.svg" alt="Localização" class="icone-localizacao" />
            Quinta da fonte - Lousã
        </button>
    </div>

        <div class="botoes">
            <button class="contactar" onclick="window.location.href='../Paginas/carrinho.php'">Carrinho</button>
            <button class="contactar" onclick="window.location.href='../Paginas/mensagens.php'">Contactar</button>
        </div>
            <div class="botoes">
                <button class="comprar" onclick="window.location.href='../Paginas/carrinho.php'">Comprar</button>
            </div>

        <p> ver se já está </p>

</main>
<script>
    const botao = document.getElementById('toggleDescricao');
    const descricao = document.getElementById('descricao');

    botao.addEventListener('click', () => {
        descricao.classList.toggle('expandida');
        botao.textContent = descricao.classList.contains('expandida') ? 'Ver menos' : 'Ver mais';
    });
</script>