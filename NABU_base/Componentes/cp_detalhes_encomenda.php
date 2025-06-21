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

// Obter dados do utilizador autenticado
$nome = $email = $contacto = '';

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
?>
<main class="body_index">


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
                    </div>

                    <!-- Título -->
                    <div class="mb-3">
                        <label for="titulo" class="form-label verde_escuro fw-semibold">Título do Anúncio*</label>
                        <input type="text" class="form-control bg-success bg-opacity-25" id="titulo" name="titulo" required>
                    </div>

                    <!-- Preço -->
                    <div class="mb-3">
                        <label for="preco" class="form-label fw-semibold verde_escuro">Preço*</label>
                        <input type="number" step="0.01" min="0" class="form-control bg-success bg-opacity-25" id="preco" name="preco" required>
                    </div>

                    <!-- Medida -->
                    <div class="mb-3">
                        <label for="medida" class="form-label fw-semibold verde_escuro">Unidade de medida*</label>
                        <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="medida" name="medida" required>
                            <?php
                            $link = new_db_connection();
                            $stmt = mysqli_stmt_init($link);
                            $query = "SELECT id_medida, abreviatura FROM medidas";
                            if (mysqli_stmt_prepare($stmt, $query)) {
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_bind_result($stmt, $id_medida, $abreviatura);
                                while (mysqli_stmt_fetch($stmt)) {
                                    echo '<option value="' . $id_medida . '">' . htmlspecialchars($abreviatura) . '</option>';
                                }
                                mysqli_stmt_close($stmt);
                            }
                            mysqli_close($link);
                            ?>
                        </select>
                    </div>

                    <!-- Categoria -->
                    <div class="mb-3">
                        <label for="categoria" class="form-label fw-semibold verde_escuro">Categoria*</label>
                        <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="categoria" name="categoria" required>
                            <?php
                            $link = new_db_connection();
                            $stmt = mysqli_stmt_init($link);
                            $query = "SELECT id_categoria, nome_categoria FROM categorias";
                            if (mysqli_stmt_prepare($stmt, $query)) {
                                mysqli_stmt_execute($stmt);
                                mysqli_stmt_bind_result($stmt, $id_categoria, $nome_categoria);
                                while (mysqli_stmt_fetch($stmt)) {
                                    echo '<option value="' . $id_categoria . '">' . htmlspecialchars($nome_categoria) . '</option>';
                                }
                                mysqli_stmt_close($stmt);
                            }
                            mysqli_close($link);
                            ?>
                        </select>
                    </div>

                    <!-- Descrição -->
                    <div class="mb-3">
                        <label for="descricao" class="form-label fw-semibold verde_escuro">Descrição*</label>
                        <textarea class="form-control bg-success bg-opacity-25" id="descricao" name="descricao" rows="3" required></textarea>
                    </div>

                    <!-- Localização -->
                    <div class="mb-3 d-flex align-items-center">
                    <span class="bg-success bg-opacity-25 border-0 p-2 me-2">
                        <i class="bi bi-geo-alt-fill verde_escuro"></i>
                    </span>
                        <input type="text" class="form-control bg-success bg-opacity-25" id="localizacao" name="localizacao" placeholder="Localização" required>
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