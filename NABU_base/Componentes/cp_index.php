<?php include_once("cp_intro_index.php"); ?>
<main class="body_index">

    <!-- Botões topo -->
    <div class="top-buttons mb-4 d-flex gap-2">
        <a class="responsive-button active">Para si</a>
        <a href="../Paginas/mapa.php" class="responsive-button">Mapa</a>
    </div>

    <!-- RECOMENDAÇÕES -->
    <section class="mb-5">
        <h1 class="verde_escuro">Recomendações</h1>
        <div id="carouselrecomendacoes" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <!-- Item 1 -->
                <div class="carousel-item active">
                    <a href="../Paginas/produto.php" class="text-decoration-none text-dark">
                        <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                            <div class="imagem_card_homepage">
                                <img class="img_hp_card" src="../Imagens/produtos/tomates.svg" alt="Tomates">
                            </div>
                            <div class="p-3">
                                <h2 class="verde_escuro fw-bold mb-1">Tomates / Cacho</h2>
                                <p class="text-muted mb-2">Rosa - Quinta da Fonte</p>
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <h3 class="verde_escuro fw-semibold mb-0">2€</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Item 2 -->
                <div class="carousel-item">
                    <a href="../Paginas/produto.php" class="text-decoration-none text-dark">
                        <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                            <div class="imagem_card_homepage">
                                <img class="img_hp_card" src="../Imagens/produtos/alface.jpg" alt="Alface">
                            </div>
                            <div class="p-3">
                                <h2 class="verde_escuro fw-bold mb-1">Alface/kg</h2>
                                <p class="text-muted mb-2">Alfredo Rocha - Quinta Coelho</p>
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <h3 class="verde_escuro fw-semibold mb-0">2,49 €</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Item 3 -->
                <div class="carousel-item">
                    <a href="../Paginas/produto.php" class="text-decoration-none text-dark">
                        <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                            <div class="imagem_card_homepage">
                                <img class="img_hp_card" src="../Imagens/produtos/ovos.jpg" alt="Ovos">
                            </div>
                            <div class="p-3">
                                <h2 class="verde_escuro fw-bold mb-1">Ovos / unidade</h2>
                                <p class="text-muted mb-2">Quim Roscas - Rua do Zeca Estacionamento Nr84</p>
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <h3 class="verde_escuro fw-semibold mb-0">0,20 €</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Controles -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselrecomendacoes" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselrecomendacoes" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Seguinte</span>
            </button>
        </div>
    </section>

    <section class="mb-5">
        <h1 class="verde_escuro">Novidades</h1>
        <div id="carouselnovidades" class="carousel slide" >
            <div class="carousel-inner">
                <!-- Item 1 -->
                <div class="carousel-item active">
                    <a href="../Paginas/produto.php" class="text-decoration-none text-dark">
                        <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                            <div class="imagem_card_homepage">
                                <img class="img_hp_card" src="../Imagens/produtos/tomates.svg" alt="Tomates">
                            </div>
                            <div class="p-3">
                                <h2 class="verde_escuro fw-bold mb-1">Tomates / Cacho</h2>
                                <p class="text-muted mb-2">Rosa - Quinta da Fonte</p>
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <h3 class="verde_escuro fw-semibold mb-0">2€</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Item 2 -->
                <div class="carousel-item">
                    <a href="../Paginas/produto.php" class="text-decoration-none text-dark">
                        <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                            <div class="imagem_card_homepage">
                                <img class="img_hp_card" src="../Imagens/produtos/alface.jpg" alt="Alface">
                            </div>
                            <div class="p-3">
                                <h2 class="verde_escuro fw-bold mb-1">Alface/kg</h2>
                                <p class="text-muted mb-2">Alfredo Rocha - Quinta Coelho</p>
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <h3 class="verde_escuro fw-semibold mb-0">2,49 €</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Item 3 -->
                <div class="carousel-item">
                    <a href="../Paginas/produto.php" class="text-decoration-none text-dark">
                        <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                            <div class="imagem_card_homepage">
                                <img class="img_hp_card" src="../Imagens/produtos/ovos.jpg" alt="Ovos">
                            </div>
                            <div class="p-3">
                                <h2 class="verde_escuro fw-bold mb-1">Ovos / unidade</h2>
                                <p class="text-muted mb-2">Quim Roscas - Rua do Zeca Estacionamento Nr84</p>
                                <hr>
                                <div class="d-flex justify-content-end">
                                    <h3 class="verde_escuro fw-semibold mb-0">0,20 €</h3>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Controles -->
            <button class="carousel-control-prev" type="button" data-bs-target="#carouselnovidades" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
                <span class="visually-hidden">Anterior</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#carouselnovidades" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
                <span class="visually-hidden">Seguinte</span>
            </button>
        </div>
    </section>

    <!-- Favoritos -->
    <section class="mb-5">
        <h1 class="verde_escuro">Favoritos</h1>
        <div class="row g-3">
            <!-- Exemplo de cartão -->
            <div class="col-6">
                <a href="../paginas/produto.php" style="text-decoration: none">
                    <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                        <!-- Ícone de favorito no canto superior direito -->
                        <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                    <span class="material-symbols-outlined verde_escuro">
                        favorite
                    </span>
                        </div>

                        <!-- Imagem -->
                        <div class="imagem_card_pesquisa">
                            <img src="../Imagens/produtos/tomates.svg" class="card-img-top rounded-4 img_hp_card" alt="Tomates">
                        </div>

                        <!-- Conteúdo -->
                        <div class="card-body m-2 pt-2 px-2 pb-0">
                            <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3">Tomates/kg</h6>
                        </div>

                        <hr class="linha-card verde_escuro">

                        <div class="card-body m-2 pt-0 pb-2 px-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class=" verde_escuro fw-bolder fs-5">
                                    <i class="bi bi-star-fill"></i> 4,9
                                </small>
                                <small class="fw-bolder verde_escuro fs-5">1 €</small>
                            </div>
                        </div>
                </a>
            </div>
        </div>
        <!-- Repete para os outros produtos -->
        <div class="col-6">
            <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                <!-- Ícone de favorito no canto superior direito -->
                <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                    <span class="material-symbols-outlined verde_escuro">
                        favorite
                    </span>
                </div>

                <!-- Imagem -->
                <div class="imagem_card_pesquisa">
                    <img src="../Imagens/produtos/ovos.jpg" class="card-img-top rounded-4 img_hp_card" alt="Cesta de ovos">
                </div>

                <!-- Conteúdo -->
                <div class="card-body m-2 pt-2 px-2 pb-0">
                    <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3">Ovos/uni</h6>
                </div>

                <hr class="linha-card">

                <div class="card-body m-2 pt-0 pb-2 px-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class=" verde_escuro fw-bolder fs-5">
                            <i class="bi bi-star-fill"></i> 5,0
                        </small>
                        <small class="fw-bolder verde_escuro fs-5">0,20 €</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                <!-- Ícone de favorito no canto superior direito -->
                <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                    <span class="material-symbols-outlined verde_escuro">
                        favorite
                    </span>
                </div>

                <!-- Imagem -->
                <div class="imagem_card_pesquisa">
                    <img src="../Imagens/produtos/alface.jpg" class="card-img-top rounded-4 img_hp_card" alt="Alface">
                </div>

                <!-- Conteúdo -->
                <div class="card-body m-2 pt-2 px-2 pb-0">
                    <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3">Alface/un</h6>
                </div>

                <hr class="linha-card">

                <div class="card-body m-2 pt-0 pb-2 px-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class=" verde_escuro fw-bolder fs-5">
                            <i class="bi bi-star-fill"></i> 3,2
                        </small>
                        <small class="fw-bolder verde_escuro fs-5">5,39 €</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6">
            <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                <!-- Ícone de favorito no canto superior direito -->
                <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                    <span class="material-symbols-outlined verde_escuro">
                        favorite
                    </span>
                </div>

                <!-- Imagem -->
                <div class="imagem_card_pesquisa">
                    <img src="../Imagens/produtos/laranjas.jpg" class="card-img-top rounded-4 img_hp_card" alt="Laranjas">
                </div>

                <!-- Conteúdo -->
                <div class="card-body m-2 pt-2 px-2 pb-0">
                    <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3">Laranjas/kg</h6>
                </div>

                <hr class="linha-card">

                <div class="card-body m-2 pt-0 pb-2 px-2">
                    <div class="d-flex justify-content-between align-items-center">
                        <small class=" verde_escuro fw-bolder fs-5">
                            <i class="bi bi-star-fill"></i> 4,5
                        </small>
                        <small class="fw-bolder verde_escuro fs-5">2,00 €</small>
                    </div>
                </div>
            </div>
        </div>

        </div>

    </section>

    <!-- FAVORITOS -->


</main>
