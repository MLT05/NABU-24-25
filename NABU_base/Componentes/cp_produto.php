<?php
require_once '../Connections/connection.php';
require_once '../Functions/function_favorito.php';
include_once '../api/geocode_anuncios.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['toggle_favorito'])) {
    $mensagem_favorito = toggle_favorito_post();
}

if (!isset($_GET['id']) || empty($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<p class='body_index'>produto not found</p>\n";
    include_once "../Componentes/cp_footer.php";
    exit();
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

mysqli_stmt_bind_result($stmt, $nome_produto, $descricao, $preco, $nome_categoria, $nome_user, $id_user, $localizacao, $capa, $data_insercao, $medida_desc, $medida_abr);
$existe = mysqli_stmt_fetch($stmt);

if ($medida_abr == "UN") {
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

// Definir classe do ícone favorito
if (isset($_SESSION['id_user'])) {
    $id_user_session = $_SESSION['id_user'];
    $link_check = new_db_connection();

    $query_check = "SELECT 1 FROM favoritos WHERE users_id_user = ? AND anuncios_id_anuncio = ?";
    $stmt_check = mysqli_stmt_init($link_check);
    mysqli_stmt_prepare($stmt_check, $query_check);
    mysqli_stmt_bind_param($stmt_check, "ii", $id_user_session, $id_anuncio);
    mysqli_stmt_execute($stmt_check);
    mysqli_stmt_store_result($stmt_check);

    if (mysqli_stmt_num_rows($stmt_check) > 0) {
        $icon_class = "material-symbols-filled"; // favorito
    } else {
        $icon_class = "material-symbols-outlined"; // não favorito
    }
    mysqli_stmt_close($stmt_check);
    mysqli_close($link_check);
} else {
    $icon_class = "material-symbols-outlined"; // não logado = não favorito
}

$link_feedback = new_db_connection();
$stmt_feedback = mysqli_stmt_init($link_feedback);

$query_feedback = "SELECT AVG(classificacao), COUNT(*) FROM feedback WHERE ref_user = ?";
if (mysqli_stmt_prepare($stmt_feedback, $query_feedback)) {
    mysqli_stmt_bind_param($stmt_feedback, "i", $id_user);
    mysqli_stmt_execute($stmt_feedback);
    mysqli_stmt_bind_result($stmt_feedback, $media_classificacao, $total_classificacoes);
    mysqli_stmt_fetch($stmt_feedback);
    mysqli_stmt_close($stmt_feedback);
    mysqli_close($link_feedback);

    // Arredonda para 1 casa decimal
    $media_classificacao = round($media_classificacao, 1);
} else {
    $media_classificacao = null;
    $total_classificacoes = 0;
}

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
        <div class="d-flex align-items-center justify-content-between position-relative">
            <h3 class="verde_escuro fw-bold my-3 fs-1 mb-0"><?= htmlspecialchars($nome_produto) ?></h3>
            <div class="pt-3">
                <div class="position-relative d-flex justify-content-center align-items-center" style="width: 48px; height: 48px;">
                    <!-- Círculo de fundo -->
                    <div class="position-absolute top-0 start-0 w-100 h-100 rounded-circle favorite-circle verde_claro_bg"></div>

                    <!-- Ícone de favorito -->
                    <span
                            class="<?= $icon_class ?> verde_escuro btn-favorito mt-0 fs-4"
                            data-id="<?= htmlspecialchars($id_anuncio) ?>"
                            role="button"
                            style="cursor:pointer; font-size: 2rem; z-index: 1;"
                            aria-label="Favoritar produto">
                        favorite
                    </span>
                </div>
            </div>
        </div>
        <a href="../Paginas/perfil_outro.php?id_user=<?= htmlspecialchars($id_user) ?>" style="text-decoration: none"><p class="verde"><?= htmlspecialchars($nome_user) ?></p></a>

        <div>
            <div class="row">
                <div class="col-6">
                    <span class="etiqueta"><?= htmlspecialchars($nome_categoria) ?></span>
                    <h4 id="avalia" class="d-flex align-items-center gap-1 verde_escuro">
    <span class="material-symbols-filled verde_escuro" style="font-size: 1.5rem;">
        star
    </span>
                        <?= $media_classificacao !== null ? htmlspecialchars($media_classificacao) : 'N/A' ?>
                        <span class="verde_claro ms-1">(<?= htmlspecialchars($total_classificacoes) ?>)</span>
                    </h4>

                </div>
                <div class="col-6 text-end">
                    <p class="fs-2 fw-bold verde_escuro"><?= number_format($preco, 2, ',', '.') ?>€ <span>/<?= htmlspecialchars($medida_abr) ?></span></p>
                </div>
            </div>
        </div>

        <h2 class="verde_escuro fw-bold my-3 fs-4">Descrição do Produto</h2>

        <p id="descricao" class="descricao">
            <?= htmlspecialchars($descricao) ?>
        </p>
        <button id="toggleDescricao" class="ver-mais-btn d-none verde_escuro text-decoration-underline">Ver mais</button>

        <?php if ($id_user != (isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0)): ?>
            <div>
                <div>
                    <h3 class="verde_escuro fw-bold my-3 fs-4">Quantidade desejada</h3>
                    <input type="number" id="quantidade" name="quantidade" class="input-quantidade rounded-3 p-3" placeholder="Ex: 1 kilo, 1 unidade..." min="<?= htmlspecialchars($min_medida) ?> " max="99.99" required />
                </div>

                <h3 class="verde_escuro fw-bold my-3 fs-4">Localização</h3>
                <div class="d-flex">
                    <a href="../Paginas/mapa.php?id=<?= $id_anuncio ?>" class="nome_localizacao rounded fs-5 p-3 verde_escuro d-inline-flex align-items-center text-decoration-none">
                        <img src="../Imagens/localizacao_simbolo.svg" alt="Localização" class="icone-localizacao me-2" />
                        <?= htmlspecialchars($localizacao) ?>
                    </a>

                </div>

                <div class="d-flex">
                    <button id="open-cart-modal" class="contactar me-1 fs-6 p-2 bg-white rounded">Adicionar ao carrinho</button>
                    <a href="../Paginas/mensagens_details.php?id_anuncio=<?= htmlspecialchars($id_anuncio) ?>&id_outro_user=<?= htmlspecialchars($id_user) ?>"
                       class="contactar ms-1 fs-6 p-2 bg-white rounded text-decoration-none bg-white verde_escuro text-center">
                        Contactar
                    </a>
                </div>
                <div class="d-flex">
                    <button class="comprar p-3 fs-6 rounded" ">Comprar</button>
                </div>
            </div>
        <?php else: ?>
            <h3 class="verde_escuro fw-bold my-3 fs-4">Localização</h3>
            <div class="d-flex">
                <a href="../Paginas/mapa.php?id=<?= $id_anuncio ?>" class="nome_localizacao rounded fs-5 p-3 verde_escuro d-inline-flex align-items-center text-decoration-none">
                    <img src="../Imagens/localizacao_simbolo.svg" alt="Localização" class="icone-localizacao me-2" />
                    <?= htmlspecialchars($localizacao) ?>
                </a>
            </div>

            <div class="d-flex gap-2 mt-3">
                <form method="POST" action="../Paginas/editar_produto.php" class="w-100">
                    <input type="hidden" name="id" value="<?= htmlspecialchars($id_anuncio) ?>">
                    <button type="submit" class="btn btn-success w-100 py-3 fs-6 rounded">Editar anúncio</button>
                </form>
                <form method="POST" action="../scripts/sc_eliminar_anuncio.php" class="w-100 eliminar-anuncio-form">
                    <input type="hidden" name="id_anuncio" value="<?= htmlspecialchars($id_anuncio) ?>">
                    <button type="button" class="btn btn-danger w-100 py-3 fs-6 rounded" data-bs-toggle="modal" data-bs-target="#modalConfirmarEliminar" data-anuncio="<?= $id_anuncio ?>">
                        Eliminar anúncio
                    </button>
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
                <div class="modal-body border-0">
                    Produto adicionado ao carrinho com sucesso!
                </div>
                <div class="modal-footer border-0justify-content-center">
                    <a href="../Paginas/carrinho.php" class="btn btn-success verde_escuro_bg">Ver Carrinho</a>
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
                <div class="modal-body border-0">
                    Este produto já está no seu carrinho.
                </div>
                <div class="modal-footer justify-content-center">
                    <a href="../Paginas/carrinho.php" class="btn btn-primary verde_escuro_bg">Ver Carrinho</a>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continuar Comprando</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Quantidade Inválida -->
    <div class="modal fade" id="quantidadeInvalidaModal" tabindex="-1" aria-labelledby="quantidadeInvalidaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="quantidadeInvalidaModalLabel">Quantidade Inválida</h5>
                </div>
                <div class="modal-body">
                    Por favor, insira uma quantidade válida.
                </div>
                <div class="modal-footer border-0 justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Overlay -->
    <div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100" id="pedidoModalLabel">Pedido realizado com sucesso!</h5>
                </div>
                <div class="modal-body">
                    <p>Aguarde confirmação do vendedor.</p>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <a href="../Paginas/encomendas.php" class="btn btn-success">Ver Pedidos</a>
                    <a href="../Paginas/index.php" class="btn btn-outline-secondary">Continuar a comprar</a>
                </div>
            </div>
        </div>
    </div>

</main>
<!-- Modal de Confirmação -->
<div class="modal fade" id="modalConfirmarEliminar" tabindex="-1" aria-labelledby="modalConfirmarEliminarLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmarEliminarLabel">Confirmar Eliminação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tens a certeza que queres eliminar este anúncio? Esta ação é irreversível.
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" id="btnConfirmarEliminar" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<script>
    window.addEventListener('DOMContentLoaded', () => {
        // Apenas dispara a execução do script, sem esperar resposta
        fetch('../api/geocode_anuncios.php').catch(() => {});
    });
</script>

<script>

        let formASubmeter = null;

        document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('modalConfirmarEliminar');
        const btnConfirmar = document.getElementById('btnConfirmarEliminar');

        // Quando abre o modal, identificar o formulário do botão clicado
        modal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        formASubmeter = button.closest('form');
    });

        // Submeter o formulário ao clicar em "Eliminar"
        btnConfirmar.addEventListener('click', () => {
        if (formASubmeter) {
        formASubmeter.submit();
    }
    });
    });

document.querySelector(".comprar")?.addEventListener("click", function () {
        const quantidadeInput = document.getElementById("quantidade");
        const quantidade = parseFloat(quantidadeInput.value);
        const id_anuncio = <?= $id_anuncio ?>;

        // Verifica se a quantidade é um número válido maior que 0
        if (isNaN(quantidade) || quantidade <= 0) {
            const modalInvalido = new bootstrap.Modal(document.getElementById('quantidadeInvalidaModal'));
            modalInvalido.show();
            return;
        }

        // Envia os dados via POST
        fetch("../scripts/sc_comprar_produto.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded",
            },
            body: `id_anuncio=${encodeURIComponent(id_anuncio)}&quantidade=${encodeURIComponent(quantidade)}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const modalPedido = new bootstrap.Modal(document.getElementById('pedidoModal'));
                    modalPedido.show();
                } else {
                    alert("Erro: " + (data.mensagem || "Erro desconhecido."));
                }
            })
            .catch(error => {
                console.error("Erro na requisição:", error);
                alert("Erro na requisição.");
            });
    });



    window.addEventListener('DOMContentLoaded', () => {
        const descricao = document.getElementById('descricao');
        const botao = document.getElementById('toggleDescricao');

        if (!descricao || !botao) {
            console.warn('Elemento descricao ou botao não encontrado');
            return;
        }

        botao.classList.add('d-none');

        const lineHeight = parseFloat(window.getComputedStyle(descricao).lineHeight);
        const maxLinesHeight = lineHeight * 2;

        if (descricao.scrollHeight > maxLinesHeight + 1) {
            botao.classList.remove('d-none');
        }

        botao.addEventListener('click', () => {
            if (descricao.classList.contains('expandida')) {
                descricao.classList.remove('expandida');
                botao.textContent = 'Ver mais';
            } else {
                descricao.classList.add('expandida');
                botao.textContent = 'Ver menos';
            }
        });
    });

    const openCartModalBtn = document.getElementById('open-cart-modal');
    if (openCartModalBtn) {
        openCartModalBtn.addEventListener('click', function(event) {
            event.preventDefault();

            const quantidadeInput = document.getElementById('quantidade');
            const quantidade = parseFloat(quantidadeInput.value);
            const id_anuncio = <?= $id_anuncio ?>;

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
                body: `id_anuncio=${encodeURIComponent(id_anuncio)}&quantidade=${encodeURIComponent(quantidade)}`
            })
                .then(response => {
                    if (response.status === 401) {
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
    }

    // Script para alternar favorito com AJAX
    document.querySelectorAll(".btn-favorito").forEach(btn => {
        const toggleFavorito = function(event) {
            event.preventDefault(); // evita ação padrão se existir

            const idAnuncio = this.getAttribute("data-id");

            fetch("../Functions/ajax_favorito.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `id_anuncio_favorito=${idAnuncio}`
            })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    if (data.includes("⚠️")) {
                        alert(data);  // Mensagem de erro, tipo "Necessita estar logado"
                        return;
                    }

                    // Alterna o ícone só se não houve erro
                    if (this.classList.contains("material-symbols-outlined")) {
                        this.classList.remove("material-symbols-outlined");
                        this.classList.add("material-symbols-filled");
                    } else {
                        this.classList.remove("material-symbols-filled");
                        this.classList.add("material-symbols-outlined");
                    }
                })
                .catch(error => {
                    console.error("Erro no AJAX:", error);
                    alert("Erro ao tentar alterar favoritos. Tenta novamente.");
                });
        };

        btn.addEventListener("click", toggleFavorito);
        btn.addEventListener("touchstart", toggleFavorito);
    });
</script>
