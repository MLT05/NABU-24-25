<?php
session_start();
require_once '../Connections/connection.php';
require_once '../Functions/function_favorito.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favorito'])) {
    $mensagem_favorito = toggle_favorito_post();
}


if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "produto not found";
}

$id_anuncio = (int)$_GET['id'];
$link = new_db_connection();

$stmt = mysqli_stmt_init($link);

$query = "SELECT a.nome_produto, a.descricao, a.preco, c.nome_categoria, u.nome, a.localizacao, a.capa, a.data_insercao, m.descricao AS medida_desc, m.abreviatura 
          FROM anuncios a
          INNER JOIN categorias c ON a.ref_categoria = c.id_categoria
          INNER JOIN users u ON a.ref_user = u.id_user
          INNER JOIN medidas m ON a.ref_medida = m.id_medida
          WHERE a.id_anuncio = ?";

if (!mysqli_stmt_prepare($stmt, $query)) {
    echo "Erro na preparação da query.";
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $nome_produto, $descricao, $preco, $nome_categoria, $nome_user, $localizacao, $capa, $data_insercao, $medida_desc, $medida_abr);
$existe = mysqli_stmt_fetch($stmt);

if (!$existe) {
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    echo "<p>Produto não encontrado.</p>";
    exit;
}
mysqli_stmt_close($stmt);
mysqli_close($link);
?>


<div class="w-100">
    <!-- <img src="../Imagens/produtos/<?= htmlspecialchars($capa) ?>" alt="<?= htmlspecialchars($nome_produto) ?>" class="img-fluid w-100" /> -->
    NABU-24-25/NABU_base/
    <img src="../Imagens/produtos/alface.jpg" alt="<?= htmlspecialchars($nome_produto) ?>" class="img-fluid w-100" />
</div>

<main class="container">
    <div class="mx-2">
        <h3 class="verde_escuro fw-bold my-3 fs-1"><?= htmlspecialchars($nome_produto) ?></h3>
        <p class="verde"><?= htmlspecialchars($nome_user) ?></p>

        <div>
            <div class="row">
                <div class="col-6">
                    <span class="etiqueta"><?= htmlspecialchars($nome_categoria) ?></span>
                    <p class="text-warning">⭐ 4,9 <span class="verde_claro">(229)</span></p>
                    <form method="post" action="" style="display:inline;">
                        <input type="hidden" name="id_anuncio_favorito" value="<?= htmlspecialchars($id_anuncio) ?>">
                        <button type="submit" name="toggle_favorito" style="border: none; background: none; cursor: pointer;">
                            ❤️ <!-- ou ícone -->
                        </button>
                        <?php if (isset($mensagem_favorito)): ?>
                            <div class="alert alert-info"><?= $mensagem_favorito ?></div>
                        <?php endif; ?>
                    </form>
                </div>
                <div class="col-6 text-end">
                    <p class="fs-2 fw-bold verde_escuro"><?= number_format($preco, 2, ',', '.') ?>€ <span>/<?= htmlspecialchars($medida_abr) ?></span></p>
                </div>
            </div>
        </div>

        <h2 class="verde_escuro fw-bold my-3 fs-4">Descrição do Produto</h2>
        <div>
            <p class="descricao" id="descricao"><?= nl2br(htmlspecialchars($descricao)) ?></p>
            <button id="toggleDescricao" class="ver-mais-btn">Ver mais</button>
        </div>

        <div>
            <h3 class="verde_escuro fw-bold my-3 fs-4">Quantidade desejada</h3>
            <input type="number" id="quantidade" name="quantidade" class="input-quantidade rounded-3 p-3" placeholder="Ex: 1 kilo, 1 unidade..." min="1" />
        </div>

        <h3 class="verde_escuro fw-bold my-3 fs-4">Localização</h3>
        <div class="d-flex">
            <button class="nome_localizacao rounded fs-5 p-3 verde_escuro">
                <img src="../Imagens/localizacao_simbolo.svg" alt="Localização" class="icone-localizacao" />
                <?= htmlspecialchars($localizacao) ?>
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

