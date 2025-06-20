<?php

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

$query = "SELECT a.nome_produto, a.descricao, a.preco, c.nome_categoria, u.nome, u.id_user, a.localizacao, a.capa, a.data_insercao, m.descricao AS medida_desc, m.abreviatura 
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

// Atualiza bind_result incluindo o novo campo u.id_user
mysqli_stmt_bind_result($stmt, $nome_produto, $descricao, $preco, $nome_categoria, $nome_user, $id_user, $localizacao, $capa, $data_insercao, $medida_desc, $medida_abr);
$existe = mysqli_stmt_fetch($stmt);

if($medida_abr == "UN") {
    $min_medida = 1;
} else {
    $min_medida = 0.05;
}

if (!$existe) {
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    echo "<p class='body_index text-center'>Produto não encontrado.</p>";
    include_once "cp_footer.php";
    exit;
}
mysqli_stmt_close($stmt);
mysqli_close($link);
?>


<div class="w-100 caixa-imagem">

    <img src="../uploads/capas/<?= htmlspecialchars($capa) ?>" alt="<?= htmlspecialchars($nome_produto) ?>" class="w-100"
         style="max-height: 100vh; object-fit: cover;" />
</div>
<?php
if (isset($_SESSION['mensagem_sistema'])) {
$mensagem = $_SESSION['mensagem_sistema'];
$tipo_mensagem = $_SESSION['tipo_mensagem'];

echo '<div class="container mt-3">
    <div class="alert alert-' . ($tipo_mensagem === 'sucesso' ? 'success' : 'danger') . ' alert-dismissible fade show" role="alert">'
        . htmlspecialchars($mensagem) . '
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
    </div>
</div>';

unset($_SESSION['mensagem_sistema']);
unset($_SESSION['tipo_mensagem']);
}
?>
<main class="container mb-13">
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
            <button id="toggleDescricao" class="ver-mais-btn d-none">Ver mais</button>
        </div>

        <?php if ($id_user != $_SESSION['id_user']): ?>
            <div>
                <div>
                    <h3 class="verde_escuro fw-bold my-3 fs-4">Quantidade desejada</h3>
                    <input type="number" id="quantidade" name="quantidade" class="input-quantidade rounded-3 p-3" placeholder="Ex: 1 kilo, 1 unidade..." min="<?= htmlspecialchars($min_medida) ?>" required />
                </div>

                <h3 class="verde_escuro fw-bold my-3 fs-4">Localização</h3>
                <div class="d-flex">
                    <button class="nome_localizacao rounded fs-5 p-3 verde_escuro">
                        <img src="../Imagens/localizacao_simbolo.svg" alt="Localização" class="icone-localizacao" />
                        <?= htmlspecialchars($localizacao) ?>
                    </button>
                </div>

                <div class="d-flex">
                    <button id="open-cart-modal" class="contactar me-1 fs-6 p-2 bg-white rounded">Adicionar ao carrinho</button>
                    <button class="contactar ms-1 fs-6 p-2 bg-white rounded" onclick="window.location.href='../Paginas/mensagens.php'">Contactar</button>
                </div>
                <div class="d-flex">
                    <button class="comprar p-3 fs-6 rounded" onclick="window.location.href='../Paginas/carrinho.php'">Comprar</button>
                </div>
            </div>
        <?php else: ?>
            <h3 class="verde_escuro fw-bold my-3 fs-4">Localização</h3>
            <div class="d-flex">
                <button class="nome_localizacao rounded fs-5 p-3 verde_escuro">
                    <img src="../Imagens/localizacao_simbolo.svg" alt="Localização" class="icone-localizacao" />
                    <?= htmlspecialchars($localizacao) ?>
                </button>
            </div>
            <div class="d-flex gap-2 mt-3">
                <form method="POST" action="cp_editar_produto.php" class="w-100">
                    <input type="hidden" name="id_anuncio" value="<?= htmlspecialchars($id_anuncio) ?>">
                    <button type="submit" class="btn btn-success w-100 py-3 fs-6 rounded">Editar anúncio</button>
                </form>
                <form method="POST" action="../scripts/sc_eliminar_anuncio.php" onsubmit="return confirm('Tens a certeza que queres eliminar este anúncio?')" class="w-100">
                    <input type="hidden" name="id_anuncio" value="<?= htmlspecialchars($id_anuncio) ?>">
                    <button type="submit" class="btn btn-danger w-100 py-3 fs-6 rounded">Eliminar anúncio</button>
                </form>
            </div>
        <?php endif; ?>
    </div>


    <!-- Modal Bootstrap -->
    <div class="modal fade" id="cartModal" tabindex="-1" aria-labelledby="cartModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="cartModalLabel">Produto Adicionado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Produto adicionado ao carrinho com sucesso!
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="../Paginas/carrinho.php" class="btn btn-success">Ver Carrinho</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continuar a Comprar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para produto já no carrinho -->
    <div class="modal fade" id="alreadyInCartModal" tabindex="-1" aria-labelledby="alreadyInCartLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="alreadyInCartLabel">Produto Já no Carrinho</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Este produto já está no seu carrinho.
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="../Paginas/carrinho.php" class="btn btn-primary">Ver Carrinho</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continuar Comprando</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Quantidade Inválida -->
    <div class="modal fade" id="quantidadeInvalidaModal" tabindex="-1" aria-labelledby="quantidadeInvalidaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header">
                    <h5 class="modal-title" id="quantidadeInvalidaModalLabel">Quantidade Inválida</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>
                <div class="modal-body">
                    Por favor, insira uma quantidade válida.
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>


</main>

<script>
    const botao = document.getElementById('toggleDescricao');
    const descricao = document.getElementById('descricao');

    // Mostrar o botão só se o conteúdo estiver cortado
    window.addEventListener('DOMContentLoaded', () => {
        const isOverflowing = descricao.scrollHeight > descricao.clientHeight;

        if (isOverflowing) {
            botao.classList.remove('d-none');
        }
    });

    botao.addEventListener('click', () => {
        descricao.classList.toggle('expandida');
        botao.textContent = descricao.classList.contains('expandida') ? 'Ver menos' : 'Ver mais';
    });

    document.getElementById('open-cart-modal').addEventListener('click', function (event) {
        event.preventDefault();

        const quantidadeInput = document.getElementById('quantidade');
        const quantidade = parseFloat(quantidadeInput.value);
        const id_anuncio = <?= $id_anuncio ?>;
        const preco = <?= $preco ?>;

        if (!quantidade || quantidade <= 0) {
            const quantidadeInvalidaModal = new bootstrap.Modal(document.getElementById('quantidadeInvalidaModal'));
            quantidadeInvalidaModal.show();
            return;
        }

        fetch('../Scripts/adicionar_ao_carrinho.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id_anuncio=${encodeURIComponent(id_anuncio)}&quantidade=${encodeURIComponent(quantidade)}&preco=${encodeURIComponent(preco)}`
        })
            .then(response => {
                if (response.status === 401) {
                    // Não autenticado - redirecionar para login
                    window.location.href = '../Paginas/login.php';
                    throw new Error('Redirecionando para login...');
                }
                return response.text();
            })
            .then(data => {
                data = data.trim();

                if (data === 'ok') {
                    const cartModal = new bootstrap.Modal(document.getElementById('cartModal'));
                    cartModal.show();
                } else if (data === 'produto_existente') {
                    const alreadyModal = new bootstrap.Modal(document.getElementById('alreadyInCartModal'));
                    alreadyModal.show();
                } else {
                    alert("Erro: " + data);
                }
            })
            .catch(error => {
                if (error.message !== 'Redirecionando para login...') {
                    alert("Erro inesperado: " + error);
                }
            });
    });
</script>


