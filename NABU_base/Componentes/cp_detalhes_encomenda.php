<?php
require_once '../Connections/connection.php';


if (!isset($_SESSION['id_user'])) {
    ?>
    <!-- Modal de login obrigatório -->
    <div class="modal fade show" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-modal="true" role="dialog" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-none">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="loginModalLabel">Login necessário</h5>
                </div>
                <div class="modal-body text-center">
                    <p>Para criar um anúncio é necessário ter login.</p>
                </div>
                <div class="modal-footer border-0">
                    <a href="../Paginas/login.php" class="btn btn-success verde_escuro_bg">Fazer Login</a>
                    <a href="../Paginas/index.php" class="btn btn-secondary">Fechar</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'), {
                backdrop: 'static',
                keyboard: false
            });
            loginModal.show();
        });
    </script>
    <?php
    exit;
}

$id_user = $_SESSION['id_user'];

// Aqui espera-se que se receba o id da encomenda via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_encomenda = intval($_GET['id']);
} else {
    die("ID da encomenda não foi fornecido ou é inválido.");
}

$link = new_db_connection();

$stmt = mysqli_stmt_init($link);
$query = "SELECT 
    encomendas.id_encomenda,
    anuncios.id_anuncio, 
    anuncios.nome_produto, 
    medidas.abreviatura, 
    anuncios.capa, 
    estados.estado, 
    encomendas.quantidade, 
    encomendas.preco,
    estados.descricao
FROM 
    anuncios 
INNER JOIN 
    medidas ON anuncios.ref_medida = medidas.id_medida 
INNER JOIN 
    encomendas ON anuncios.id_anuncio = encomendas.ref_anuncio 
INNER JOIN 
    estados ON encomendas.ref_estado = estados.id_estado 
WHERE 
    encomendas.id_encomenda = ?";

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $id_encomenda);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_encomenda, $id_anuncio, $nome_produto, $abreviatura, $capa, $estado, $quantidade, $preco,$descricao_estado);

    if (!mysqli_stmt_fetch($stmt)) {
        die("Encomenda não encontrada.");
    }

    mysqli_stmt_close($stmt);
} else {
    die("Erro na query da encomenda: " . mysqli_error($link));
}

// Buscar dados do vendedor (ref_user) do anúncio
$stmt = mysqli_stmt_init($link);
$query_user = "SELECT nome, email, contacto FROM users WHERE id_user = (SELECT ref_user FROM anuncios WHERE id_anuncio = ?)";
if (mysqli_stmt_prepare($stmt, $query_user)) {
    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nome, $email, $contacto);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    die("Erro na query do usuário: " . mysqli_error($link));
}

mysqli_close($link);
?>
<main class="body_index">

<div class="order-tracker mt-5">
    <h5 class="fw-bold fs-3 verde_escuro mb-0">Estado do Produto</h5>
    <p class="verde_escuro"> <strong> Pedido:</strong> #<?= htmlspecialchars($id_encomenda) ?></p>
    <p class="verde_escuro"> <strong>Estado da Encomenda:</strong> <?= htmlspecialchars($estado) ?></p>
    <div class="verde_claro_bg border">
    <p class="verde_escuro mb-0 text-center"> <strong><?= htmlspecialchars($descricao_estado) ?></strong></p>
    </div>
</div>

    <div class="order-tracker">
    <div class="mb-13">
        <h5 class="fw-bold fs-3 verde_escuro mb-0">Detalhes do Produto</h5>

        <!-- Imagem -->
        <label class="form-label verde_escuro fw-semibold">Imagem*</label>
        <div class="mb-4">
            <img src="../uploads/capas/<?= htmlspecialchars($capa) ?>" class="w-100" style="max-height: 100vh; object-fit: cover;">
        </div>

        <!-- Título -->
        <div class="mb-3">
            <label class="form-label verde_escuro fw-semibold"><strong>Título do Anúncio*</strong></label>
            <p class="verde_escuro"><?= htmlspecialchars($nome_produto) ?></p>
        </div>

        <!-- Quantidade -->
        <div class="mb-3">
            <label class="form-label verde_escuro fw-semibold"><strong>Quantidade*</strong></label>
            <p class="verde_escuro"><?= htmlspecialchars($quantidade) ?> <?= htmlspecialchars($abreviatura) ?></p>
        </div>

        <!-- Preço -->
        <div class="mb-3">
            <label class="form-label verde_escuro fw-semibold"><strong>Preço*</strong></label>
            <p class="verde_escuro"><?= htmlspecialchars(number_format($preco, 2, ',', '.')) ?> €</p>
        </div>

<hr class="verde_escuro">
        <!-- Contactos do vendedor -->
        <h6 class="fw-bold mt-4 verde_escuro fs-4">Contactos do vendedor</h6>

        <div class="mb-3">
            <label class="form-label fw-bold verde_escuro"><strong>Nome*</strong></label>
            <p class="verde_escuro"><?= htmlspecialchars($nome) ?></p>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold verde_escuro"><strong>Email*</strong></label>
            <p class="verde_escuro"><?= htmlspecialchars($email) ?></p>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold verde_escuro"><strong>Contacto telefónico*</strong></label>
            <p class="verde_escuro"><?= htmlspecialchars($contacto) ?></p>
        </div>
    </div>
</main>

<script>
    let currentStep = 1;
    const steps = document.querySelectorAll(".step");

    function nextStep() {
        if (currentStep < steps.length) {
            steps[currentStep].classList.add("active");
            currentStep++;
        }
    }

</script>
