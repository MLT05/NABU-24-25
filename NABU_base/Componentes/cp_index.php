<!-- Overlay de carregamento -->
<div id="app-loader">
    <img src="../Imagens/app/NABU-LOGO.png" alt="Carregando...">
</div>

<style>
    #app-loader {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background-color: #5c7f4e;
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
        transition: opacity 0.5s ease;
    }

    #app-loader img {
        width: 100px;
        height: auto;
    }
</style>

<main class="body_index">

    <?php require_once "cp_intro_index.php" ?>

    <!-- RECOMENDAÇÕES -->
    <section class="mb-5">
        <h1 class="verde_escuro">Recomendações</h1>
        <p class="verde">Recomendados para si</p>
        <div id="carouselrecomendacoes" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                <?php
                require_once '../Connections/connection.php';
                $link = new_db_connection();

                $stmt = mysqli_stmt_init($link);
                $query = "SELECT anuncios.id_anuncio,anuncios.nome_produto, anuncios.preco, anuncios.capa,anuncios.localizacao, users.nome,categorias.nome_categoria,anuncios.data_insercao
                    FROM anuncios
                    INNER JOIN users ON anuncios.ref_user = users.id_user
                    INNER JOIN categorias ON anuncios.ref_categoria = categorias.id_categoria";

                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $capa, $localizacao, $nome_user,$nome_categoria,$data_insercao);

                    $first = true;
                    while (mysqli_stmt_fetch($stmt)) {
                        ?>
                        <div class="carousel-item <?= $first ? 'active' : '' ?>">
                            <a href="../Paginas/produto.php?id=<?= $id_anuncio ?>" class="text-decoration-none text-dark">
                                <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                                    <div class="imagem_card_homepage">
                                        <img class="img_hp_card rounded-4" src="../uploads/capas/<?= htmlspecialchars($capa) ?>">
                                    </div>
                                    <div class="p-3">
                                        <h2 class="verde_escuro fw-bold mb-1"><?= htmlspecialchars($nome_produto); ?></h2>
                                        <p class="text-muted mb-2"> <strong><?= htmlspecialchars($localizacao); ?></strong> </p>
                                        <br>
                                        <p class="text-muted mb-2"><?= date('d/m/Y', strtotime($data_insercao)); ?></p>
                                        <hr>
                                        <div class="d-flex justify-content-end">
                                            <h3 class="verde_escuro fw-semibold mb-0"><?= number_format($preco, 2, ',', ' '); ?> €</h3>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
>
                        <?php
                        $first = false;
                    }
                    mysqli_stmt_close($stmt);
                }
                ?>

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

    <!-- NOVIDADES -->
    <section class="mb-5">
        <h1 class="verde_escuro">Novidades</h1>
        <div id="carouselnovidades" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
                <?php
                require_once '../Connections/connection.php';
                $link = new_db_connection();

                $stmt = mysqli_stmt_init($link);
                $query = "SELECT anuncios.id_anuncio, anuncios.nome_produto, anuncios.preco, anuncios.capa, anuncios.localizacao, users.nome, categorias.nome_categoria, anuncios.data_insercao
                FROM anuncios
                INNER JOIN users ON anuncios.ref_user = users.id_user
                INNER JOIN categorias ON anuncios.ref_categoria = categorias.id_categoria
                ORDER BY data_insercao DESC
                LIMIT 6"; // Mostra os 6 mais recentes

                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $capa, $localizacao, $nome_user, $nome_categoria, $data_insercao);

                    $first = true;
                    while (mysqli_stmt_fetch($stmt)) {
                        ?>
                        <div class="carousel-item <?= $first ? 'active' : '' ?>">
                            <a href="../Paginas/produto.php?id=<?= $id_anuncio ?>" class="text-decoration-none text-dark">
                                <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                                    <div class="imagem_card_homepage">
                                        <img class="img_hp_card rounded-4" src="../uploads/capas/<?= htmlspecialchars($capa) ?>">
                                    </div>
                                    <div class="p-3">
                                        <h2 class="verde_escuro fw-bold mb-1"><?= htmlspecialchars($nome_produto); ?></h2>
                                        <p class="text-muted mb-2"><strong><?= htmlspecialchars($localizacao); ?></strong></p>
                                        <br>
                                        <p class="text-muted mb-2"><?= date('d/m/Y', strtotime($data_insercao)); ?></p>
                                        <hr>
                                        <div class="d-flex justify-content-end">
                                            <h3 class="verde_escuro fw-semibold mb-0"><?= number_format($preco, 2, ',', ' '); ?> €</h3>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                        $first = false;
                    }
                    mysqli_stmt_close($stmt);
                }
                ?>
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

    <!-- FAVORITOS -->
    <section class="mb-5">
        <h1 class="verde_escuro">Favoritos</h1>
        <div class="row g-3">

            <!-- Produto 1 -->
            <div class="col-6">
                <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">
                    <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                        <span class="material-symbols-outlined verde_escuro">favorite</span>
                    </div>
                    <a href="../Paginas/produto.php" style="text-decoration: none; color: inherit;">
                        <div class="imagem_card_pesquisa">
                            <img src="../Imagens/produtos/tomates.svg" class="card-img-top rounded-4 img_hp_card" alt="Tomates">
                        </div>
                        <div class="card-body m-2 pt-2 px-2 pb-0">
                            <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3">Tomates/kg</h6>
                        </div>
                        <hr class="linha-card verde_escuro">
                        <div class="card-body m-2 pt-0 pb-2 px-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="verde_escuro fw-bolder fs-5"><i class="bi bi-star-fill"></i> 4,9</small>
                                <small class="fw-bolder verde_escuro fs-5">1 €</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Produto 2 -->
            <div class="col-6">
                <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">
                    <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                        <span class="material-symbols-outlined verde_escuro">favorite</span>
                    </div>
                    <a href="#" style="text-decoration: none; color: inherit;">
                        <div class="imagem_card_pesquisa">
                            <img src="../Imagens/produtos/ovos.jpg" class="card-img-top rounded-4 img_hp_card" alt="Cesta de ovos">
                        </div>
                        <div class="card-body m-2 pt-2 px-2 pb-0">
                            <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3">Ovos/uni</h6>
                        </div>
                        <hr class="linha-card verde_escuro">
                        <div class="card-body m-2 pt-0 pb-2 px-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="verde_escuro fw-bolder fs-5"><i class="bi bi-star-fill"></i> 5,0</small>
                                <small class="fw-bolder verde_escuro fs-5">0,20 €</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Produto 3 -->
            <div class="col-6">
                <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">
                    <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                        <span class="material-symbols-outlined verde_escuro">favorite</span>
                    </div>
                    <a href="#" style="text-decoration: none; color: inherit;">
                        <div class="imagem_card_pesquisa">
                            <img src="../Imagens/produtos/alface.jpg" class="card-img-top rounded-4 img_hp_card" alt="Alface">
                        </div>
                        <div class="card-body m-2 pt-2 px-2 pb-0">
                            <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3">Alface/un</h6>
                        </div>
                        <hr class="linha-card verde_escuro">
                        <div class="card-body m-2 pt-0 pb-2 px-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="verde_escuro fw-bolder fs-5"><i class="bi bi-star-fill"></i> 3,2</small>
                                <small class="fw-bolder verde_escuro fs-5">5,39 €</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Produto 4 -->
            <div class="col-6">
                <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">
                    <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                        <span class="material-symbols-outlined verde_escuro">favorite</span>
                    </div>
                    <a href="#" style="text-decoration: none; color: inherit;">
                        <div class="imagem_card_pesquisa">
                            <img src="../Imagens/produtos/laranjas.jpg" class="card-img-top rounded-4 img_hp_card" alt="Laranjas">
                        </div>
                        <div class="card-body m-2 pt-2 px-2 pb-0">
                            <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3">Laranjas/kg</h6>
                        </div>
                        <hr class="linha-card verde_escuro">
                        <div class="card-body m-2 pt-0 pb-2 px-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="verde_escuro fw-bolder fs-5"><i class="bi bi-star-fill"></i> 4,5</small>
                                <small class="fw-bolder verde_escuro fs-5">2,00 €</small>
                            </div>
                        </div>
                    </a>
                </div>
            </div>

        </div>
    </section>

</main>

<script>
    window.addEventListener('load', function () {
        // Verifica se já foi mostrado antes nesta sessão
        if (!sessionStorage.getItem('loaderShown')) {
            const loader = document.getElementById('app-loader');
            if (loader) {
                setTimeout(() => {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 500);
                }, 1500);
                // Marca que o loader já foi mostrado
                sessionStorage.setItem('loaderShown', 'true');
            }
        } else {
            // Esconde imediatamente se já foi mostrado
            const loader = document.getElementById('app-loader');
            if (loader) {
                loader.style.display = 'none';
            }
        }
    });
</script>
