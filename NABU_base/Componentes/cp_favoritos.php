<?php

require_once '../connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../paginas/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$link = new_db_connection();

$query = "
    SELECT a.id_anuncio, a.nome_produto, a.preco, a.capa, c.nome_categoria
    FROM favoritos f
    INNER JOIN capas a ON f.anuncios_id_anuncio = a.id_anuncio
    LEFT JOIN categorias c ON a.ref_categoria = c.id_categoria
    WHERE f.users_id_user = ?
    ORDER BY f.data_insercao DESC
";

$stmt = mysqli_stmt_init($link);
mysqli_stmt_prepare($stmt, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $capa, $nome_categoria);
?>

<section class="sec-favoritos body_index">
    <div class="px-lg-5">
        <h1 class="mb-4 text-center verde_escuro">Meus Favoritos</h1>
        <div class="row g-3">
            <?php
            $tem_favoritos = false;
            while (mysqli_stmt_fetch($stmt)) {
                $tem_favoritos = true;
                ?>
                <div class="col-6 col-md-4 col-lg-3">
                    <a href="../paginas/produto.php?id=<?= htmlspecialchars($id_anuncio) ?>" style="text-decoration: none">
                        <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">
                            <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                                <span class="material-symbols-outlined verde_escuro">
                                    favorite
                                </span>
                            </div>
                            <div class="imagem_card_pesquisa">
                                <!--<img src="../Imagens/produtos/<?= htmlspecialchars($capa) ?>" class="card-img-top rounded-4 img_hp_card" alt="<?= htmlspecialchars($nome_produto) ?>">-->
                                <img src="../Imagens/produtos/alface.jpg" alt="<?= htmlspecialchars($nome_produto) ?>" class="img-fluid w-100" />

                            </div>
                            <div class="card-body m-2 pt-2 px-2 pb-0">
                                <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3">
                                    <?= htmlspecialchars($nome_produto) ?>
                                </h6>
                                <small class="text-muted"><?= htmlspecialchars($nome_categoria) ?></small>
                            </div>
                            <hr class="linha-card verde_escuro">
                            <div class="card-body m-2 pt-0 pb-2 px-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="verde_escuro fw-bolder fs-5">
                                        <i class="bi bi-star-fill"></i> 4,9
                                    </small>
                                    <small class="fw-bolder verde_escuro fs-5"><?= number_format($preco, 2, ',', ' ') ?> €</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
            if (!$tem_favoritos) {
                echo '<p class="text-center verde_escuro fs-4">Ainda não tem favoritos adicionados.</p>';
            }
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            ?>
        </div>
    </div>
</section>
