<?php
?>

<main class="body_index">
<div class="container mt-3">

    <!-- 🔍 Pesquisa -->
    <div class="mb-2">
        <input type="text" class="form-control rounded-3" placeholder="🔍︎ Pesquisar...">
    </div>

    <!-- 📁 Categorias com scroll horizontal -->
    <div class="mb-3 overflow-auto categorias-wrapper">
        <div class="d-flex flex-nowrap gap-2">
            <span class="fw-bold text-decoration-underline categoria-item">Todos</span>
            <span class="px-2 categoria-item">Vegetais</span>
            <span class="px-2 categoria-item">Frutas</span>
            <span class="px-2 categoria-item">Ovos e laticínios</span>
            <span class="px-2 categoria-item">Produtos Apícolas</span>
            <span class="px-2 categoria-item">Plantas</span>
            <span class="px-2 categoria-item">Raízes</span>
            <span class="px-2 categoria-item">Sucos</span>
        </div>
    </div>

    <!-- 🧺 Produtos -->
    <div class="row g-3">
        <!-- Exemplo de cartão -->
        <div class="col-6">
            <div class="card rounded-4 shadow-sm border-0 position-relative" style="background-color: #e8f1e5;">

                <!-- Ícone de favorito no canto superior direito -->
                <i class="bi bi-heart position-absolute top-0 end-0 m-2 text-muted" style="font-size: 1.2rem;"></i>

                <!-- Imagem -->
                <img src="../Imagens/produtos/tomates.svg" class="card-img-top rounded-top-4" alt="Repolho">

                <!-- Conteúdo -->
                <div class="card-body p-2">
                    <h6 class="card-title mb-1 fw-semibold text-success">Repolho/pé</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="bi bi-star-fill text-success"></i> 4,9</small>
                        <small class="fw-semibold text-dark">2,00 €</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card rounded-4 shadow-sm border-0 position-relative" style="background-color: #e8f1e5;">

                <!-- Ícone de favorito no canto superior direito -->
                <i class="bi bi-heart position-absolute top-0 end-0 m-2 text-muted" style="font-size: 1.2rem;"></i>

                <!-- Imagem -->
                <img src="../Imagens/produtos/tomates.svg" class="card-img-top rounded-top-4" alt="Repolho">

                <!-- Conteúdo -->
                <div class="card-body p-2">
                    <h6 class="card-title mb-1 fw-semibold text-success">Repolho/pé</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="bi bi-star-fill text-success"></i> 4,9</small>
                        <small class="fw-semibold text-dark">2,00 €</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card rounded-4 shadow-sm border-0 position-relative" style="background-color: #e8f1e5;">

                <!-- Ícone de favorito no canto superior direito -->
                <i class="bi bi-heart position-absolute top-0 end-0 m-2 text-muted" style="font-size: 1.2rem;"></i>

                <!-- Imagem -->
                <img src="../Imagens/produtos/tomates.svg" class="card-img-top rounded-top-4" alt="Repolho">

                <!-- Conteúdo -->
                <div class="card-body p-2">
                    <h6 class="card-title mb-1 fw-semibold text-success">Repolho/pé</h6>
                    <div class="d-flex justify-content-between align-items-center">
                        <small class="text-muted"><i class="bi bi-star-fill text-success"></i> 4,9</small>
                        <small class="fw-semibold text-dark">2,00 €</small>
                    </div>
                </div>
            </div>
        </div>
        <!-- Repete para os outros produtos -->
    </div>

</div>
</main>