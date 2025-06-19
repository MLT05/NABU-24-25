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
        <div class="carousel-item <?= $first ? 'active' : '' ?>">
            <a href="../Paginas/produto.php?id=<?= $id_anuncio ?>" class="text-decoration-none text-dark">
                <div class="cards_homepage card-body rounded-4 shadow-sm bg-light-green">
                    <div class="imagem_card_homepage">
                        <img class="img_hp_card" src="../uploads/capas/<?= htmlspecialchars($capa) ?: 'default-image.jpg' ?>" alt="<?= htmlspecialchars($nome_produto) ?>">
                    </div>
                    <div class="p-3">
                        <h2 class="verde_escuro fw-bold mb-1"><?= htmlspecialchars($nome_produto) ?></h2>
                        <p class="text-muted mb-2">Recomendado para si</p>
                        <hr>
                        <div class="d-flex justify-content-end">
                            <h3 class="verde_escuro fw-semibold mb-0"><?= number_format($preco, 2) ?> € / <?= $medida ?></h3>
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
<?php
