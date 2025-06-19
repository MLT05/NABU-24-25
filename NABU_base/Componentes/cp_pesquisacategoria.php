<?php
if (isset($_GET['id_categoria'])) {
    $id_categoria = $_GET['id_categoria'];

    // Conexão à base de dados
    require_once '../Connections/connection.php';
    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Query simples com filtro por ref_categoria
    $query= "SELECT nome_produto, preco, id_anuncio FROM capas  INNER JOIN categorias ON capas.ref_categorias = categorias.id_categorias WHERE categorias.id_categorias= ?";

    ?>
    <main class="body_index">
        <div class="mt-3">
            <?php
            require_once '../Componentes/cp_intro_pesquisa.php';
            require_once '../Componentes/cp_intro_categorias.php';
            ?>

            <div class="row g-3">
                <?php
                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_bind_param($stmt, "i", $id_categoria);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $nome, $preco, $id_anuncio);

                    $tem_resultados = false;
                    while (mysqli_stmt_fetch($stmt)) {
                        $tem_resultados = true;
                        ?>
                        <div class="col-6">
                            <a href="../Paginas/produto.php?id=<?= $id_anuncio ?>" style="text-decoration: none">
                                <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">
                                    <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                                        <span class="material-symbols-outlined verde_escuro">favorite</span>
                                    </div>

                                    <div class="imagem_card_pesquisa">
                                        <img src="../Imagens/produtos/<?= $id_anuncio ?>.jpg" class="card-img-top rounded-4 img_hp_card" alt="<?= htmlspecialchars($nome) ?>">
                                    </div>

                                    <div class="card-body m-2 pt-2 px-2 pb-0">
                                        <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3"><?= htmlspecialchars($nome) ?></h6>
                                    </div>

                                    <hr class="linha-card verde_escuro">

                                    <div class="card-body m-2 pt-0 pb-2 px-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="verde_escuro fw-bolder fs-5"><i class="bi bi-star-fill"></i></small>
                                            <small class="fw-bolder verde_escuro fs-5"><?= number_format($preco, 2) ?> €</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                    }

                    if (!$tem_resultados) {
                        echo '<p class="text-center mt-4">Nenhum produto encontrado nesta categoria.</p>';
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    echo '<p class="text-danger">Erro na preparação da query: ' . mysqli_error($link) . '</p>';
                }

                mysqli_close($link);
                ?>
            </div>
        </div>
    </main>
    <?php
} else {
    echo "<p class='text-danger'>ID de categoria inválido ou não fornecido.</p>";
}
?>
