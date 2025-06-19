
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

<?php


require_once '../Connections/connection.php';
$link = new_db_connection();

$stmt = mysqli_stmt_init($link);
$query = "SELECT id_anuncio, nome_produto, preco, abreviatura, capa
          FROM anuncios
          INNER JOIN medidas ON anuncios.ref_medida = medidas.id_medida
          ORDER BY data_insercao DESC
          LIMIT 3"; // Por agora, iguais a novidades

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $medida, $capa);

    $first = true;
    while (mysqli_stmt_fetch($stmt)) {
        ?>
        <!-- Item 1 -->
        <div class="carousel-item <?= $first ? 'active' : '' ?>">
            <a href="../Paginas/produto.php" class="text-decoration-none text-dark">
                <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                    <div class="imagem_card_homepage">
                        <img class="img_hp_card" src="../Imagens/produtos/tomates.svg" alt="Tomates">
                    </div>
                    <div class="p-3">
                        <h2 class="verde_escuro fw-bold mb-1"><?php echo htmlspecialchars($nome_produto); ?></h2>
                        <p class="text-muted mb-2">Rosa - Quinta da Fonte</p>
                        <hr>
                        <div class="d-flex justify-content-end">
                            <h3 class="verde_escuro fw-semibold mb-0">2€</h3>
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


</main>
