<?php
?>

<main class="body_index">
<div class="container mt-3">

    <!-- 🔍 Pesquisa -->
    <div class="mb-2 position-relative">
        <input type="text" class="form-control rounded-3 input-pesquisa ps-5" placeholder="Pesquisar...">
        <img src="../Imagens/icons/search_24dp_004D40_FILL0_wght400_GRAD0_opsz24.svg"
             class="position-absolute top-50 start-0 translate-middle-y ms-3"
             style="width: 20px; height: 20px;">
    </div>

    <!-- 📁 Categorias com scroll horizontal -->
    <div class="mt-3 mb-2 overflow-auto categorias-wrapper">
        <div class="d-flex flex-nowrap verde_escuro fw-normal">
            <span class="fw-bold text-decoration-underline categoria-item fs-6">Todos</span>
            <span class="categoria-item fs-6">Vegetais</span>
            <span class="categoria-item fs-6">Frutas</span>
            <span class="categoria-item fs-6">Ovos e laticínios</span>
            <span class="categoria-item fs-6">Produtos Apícolas</span>
            <span class="categoria-item fs-6">Plantas</span>
            <span class="categoria-item fs-6">Raízes</span>
            <span class="categoria-item fs-6">Sucos</span>
        </div>
    </div>

    <!-- 🧺 Produtos -->
    <div class="row g-3">
        <!-- Exemplo de cartão -->
        <div class="col-6">
            <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                <!-- Ícone de favorito no canto superior direito -->
                <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
            <span class="material-symbols-outlined">
                favorite
            </span>
                </div>

                <!-- Imagem -->
                <img src="../Imagens/produtos/tomates.svg" class="card-img-top rounded-4" alt="Repolho">

                <!-- Conteúdo -->
                <div class="card-body m-2 p-2">
                    <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-5">Repolho/pé</h6>
                </div>

                <hr class="linha-card">

                <div class="card-body m-2 pt-0 pb-2 px-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class=" verde_escuro fw-bold fs-5">
                            <i class="bi bi-star-fill"></i> 4,9
                        </small>
                        <small class="fw-bold verde_escuro fs-5">2,00 €</small>
                    </div>
                </div>
            </div>
        </div>
        <!-- Repete para os outros produtos -->
    </div>

</div>
</main>