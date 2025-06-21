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
        <h1 class="verde_escuro mb-0">Recomendações</h1>
        <p class="verde_claro">Recomendacões com base nas suas preferências</p>
        <div id="carouselrecomendacoes" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">

                <?php
                require_once '../Connections/connection.php';
                require_once '../Functions/function_favorito.php';
                $link = new_db_connection();

                $stmt = mysqli_stmt_init($link);
                $query = "SELECT anuncios.id_anuncio,anuncios.nome_produto, anuncios.preco, anuncios.capa,anuncios.localizacao, users.nome,categorias.nome_categoria,anuncios.data_insercao
                    FROM anuncios
                    INNER JOIN users ON anuncios.ref_user = users.id_user
                    INNER JOIN categorias ON anuncios.ref_categoria = categorias.id_categoria
                    ORDER BY data_insercao ASC
                    LIMIT 6";



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
        <h1 class="verde_escuro mb-0">Novidades</h1>
        <p class="verde_claro">Descubra as novidades adicionadas recentemente</p>
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
        <h1 class="verde_escuro mb-0">Favoritos</h1>
        <p class="verde_claro">Os seus favoritos</p>
        <div class="row g-3">
            <?php
            if (isset($_SESSION['id_user'])) {
                require_once '../Connections/connection.php';
                $link = new_db_connection();
                $stmt = mysqli_stmt_init($link);

                $query = "
                    SELECT a.id_anuncio, a.nome_produto, a.preco, a.capa, c.nome_categoria
                    FROM favoritos f
                    INNER JOIN anuncios a ON f.anuncios_id_anuncio = a.id_anuncio
                    LEFT JOIN categorias c ON a.ref_categoria = c.id_categoria
                    WHERE f.users_id_user = ?
                    ORDER BY f.data_insercao DESC
                    LIMIT 4
                ";

                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_user']);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $capa, $nome_categoria); // ✅ certo
                    $tem_favoritos = false;
                    while (mysqli_stmt_fetch($stmt)) {
                        $tem_favoritos = true;
                        $icon_class = "material-symbols-filled";
                        ?>
                        <div class="col-6 col-md-4 col-lg-3">
                            <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                                <!-- Ícone de favorito -->
                                <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
            <span
                    class="<?= $icon_class ?> verde_escuro btn-favorito mt-0 fs-4"
                    data-id="<?= htmlspecialchars($id_anuncio) ?>"
                    role="button"
                    style="cursor:pointer;"
                    aria-label="Favoritar produto"
            >
                favorite
            </span>
                                </div>

                                <!-- Link do produto -->
                                <a href="../Paginas/produto.php?id=<?= htmlspecialchars($id_anuncio) ?>" style="text-decoration: none">

                                    <!-- Imagem -->
                                    <div class="imagem_card_pesquisa">
                                        <img
                                                src="../uploads/capas/<?= htmlspecialchars($capa) ?>"
                                                alt="<?= htmlspecialchars($nome_produto) ?>"
                                                class="card-img-top rounded-4 img_hp_card"
                                        >
                                    </div>

                                    <!-- Título e categoria -->
                                    <div class="card-body m-2 pt-2 px-2 pb-0">
                                        <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3 text-truncate">
                                            <?= htmlspecialchars($nome_produto) ?>
                                        </h6>
                                        <small class="text-muted"><?= htmlspecialchars($nome_categoria) ?></small>
                                    </div>
                                </a>

                                <hr class="linha-card verde_escuro">

                                <!-- Rodapé com avaliação e preço -->
                                <div class="card-body m-2 pt-0 pb-2 px-2">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small class="verde_escuro fw-bolder fs-5">
                                            <i class="bi bi-star-fill"></i> 4,9
                                        </small>
                                        <small class="fw-bolder verde_escuro fs-5">
                                            <?= number_format($preco, 2, ',', ' ') ?> €
                                        </small>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <?php
                    } if (!$tem_favoritos) {
                        echo '<p class="text-center verde_escuro fs-4">Ainda não tem favoritos adicionados.</p>';
                    }
                    mysqli_stmt_close($stmt);
                }
                mysqli_close($link);
            } else {
                echo "<p class='text-muted'>É necessário fazer <a href='../Paginas/login.php' class='verde_escuro'><strong>Login</strong></a> para visualizar os seus favoritos.</p>";
            }
            ?>
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
