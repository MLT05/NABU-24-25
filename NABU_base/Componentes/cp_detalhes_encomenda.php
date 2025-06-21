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
} else {
$id_user = $_SESSION['id_user'];

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id_anuncio = intval($_GET['id']);
} else {
    die("ID do anúncio não foi fornecido ou é inválido.");
}

$nome = $email = $contacto = '';
$titulo = $descricao = $preco = $ref_categoria = $ref_user = $localizacao = $capa = $data_insercao = $ref_medida = '';

$link = new_db_connection();

$stmt = mysqli_stmt_init($link);
$query = "SELECT nome_produto, descricao, preco, ref_categoria, ref_user, localizacao, capa, data_insercao, ref_medida FROM anuncios WHERE id_anuncio = ?";
if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $titulo, $descricao, $preco, $ref_categoria, $ref_user, $localizacao, $capa, $data_insercao, $ref_medida);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    die("Erro na query do produto: " . mysqli_error($link));
}

$stmt = mysqli_stmt_init($link);
$query_user = "SELECT nome, email, contacto FROM users WHERE id_user = ?";
if (mysqli_stmt_prepare($stmt, $query_user)) {
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nome, $email, $contacto);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
}

mysqli_close($link);
var_dump($nome);

?>

<div class="order-tracker mt-5">
    <h5 class="fw-bold fs-3 verde_escuro mb-0">Estado do Produto</h5>
    <p>Pedido: #SDGT1254FD</p>

    <div class="progress-container">
        <div class="step active">
            <div class="icon">🛒</div>
            <p>Pedido Feito</p>
        </div>
        <div class="step">
            <div class="icon">✅</div>
            <p>Aceite</p>
        </div>

        <div class="step">
            <div class="icon">🚚</div>
            <p>A Caminho</p>
        </div>
        <div class="step">
            <div class="icon">📬</div>
            <p>Entregue</p>
        </div>
    </div>

    <div class="text-center">
        <button type="button" class="btn verde_escuro_bg btn-success" onclick="nextStep()">Avançar Etapa</button>
    </div>
</div>

<div class="order-tracker">
     <form method="post" enctype="multipart/form-data" action="../scripts/sc_add_produto.php">
                <div>
                    <h5 class="fw-bold fs-3 verde_escuro mb-0">Produto</h5>
                    <p class="verde_escuro">Detalhes do produto</p>

                    <!-- Upload Imagem -->
                    <label for="pfp" class="form-label verde_escuro fw-semibold">Imagem*</label>
                    <div class="text-center mb-4">
                        <p><?= htmlspecialchars($capa) ?></p>
                    </div>

                    <!-- Título -->
                    <div class="mb-3">
                        <label for="titulo" class="form-label verde_escuro fw-semibold">Título do Anúncio*</label>
                        <p> <strong> <?= htmlspecialchars($titulo)?></strong></p>
                    </div>

                    <!-- Preço -->
                    <div class="mb-3">
                        <label for="preco" class="form-label fw-semibold verde_escuro">Preço*</label>
                        <p><?= htmlspecialchars($preco)?></p>
                    </div>

                    <!-- Medida -->
                    <div class="mb-3">
                        <label for="medida" class="form-label fw-semibold verde_escuro">Unidade de medida*</label>
                        <p><?= htmlspecialchars($ref_medida)?></p>
                    </div>

                    <!-- Categoria -->
                    <div class="mb-3">
                        <label for="categoria" class="form-label fw-semibold verde_escuro">Categoria*</label>
                        <p><?= htmlspecialchars($ref_categoria)?></p>

                    </div>

                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="descricao" class="form-label fw-semibold verde_escuro">Descrição*</label>
                        <p><?= htmlspecialchars($descricao)?></p>
                    </div>

                    <!-- Localização -->
                    <div class="mb-3 d-flex align-items-center">
                    <span class="bg-success bg-opacity-25 border-0 p-2 me-2">
                        <i class="bi bi-geo-alt-fill verde_escuro"></i>
                    </span>
                        <p><?= htmlspecialchars($localizacao)?></p>
                    </div>

                    <!-- Contactos -->
                    <h6 class="fw-bold mt-4 verde_escuro fs-4">Contactos do vendedor</h6>

                    <!-- Nome -->
                    <div class="mb-3">
                        <label class="form-label fw-bold verde_escuro">Nome*</label>
                        <p><?= htmlspecialchars($nome) ?></p>
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label class="form-label fw-bold verde_escuro">Email*</label>
                        <p><?= htmlspecialchars($email) ?></p>
                    </div>

                    <!-- Contacto -->
                    <div class="mb-4">
                        <label class="form-label fw-bold verde_escuro">Contacto telefónico*</label>
                        <p><?= htmlspecialchars($contacto) ?></p>
                    </div>

            </form>

        <script>
            const previewImage = (event) => {
                const files = event.target.files;
                if (files.length > 0) {
                    const imageUrl = URL.createObjectURL(files[0]);
                    const imageElement = document.getElementById("preview-selected-image");
                    imageElement.src = imageUrl;
                }
            };
        </script>

        <?php
    }
    ?>

</div>

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