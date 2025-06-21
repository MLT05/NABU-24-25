<?php
require_once '../Connections/connection.php';
$link = new_db_connection();
$stmt = mysqli_stmt_init($link);
if (isset($_GET['id_categoria'])) {
    $id_categoria = $_GET['id_categoria'];

    ?>
    <main class="body_index">

        <div class="mt-3">
            <?php
            require_once '../Componentes/cp_intro_pesquisa.php';
            require_once '../Componentes/cp_intro_categorias.php';


            ?>

            <div class="row g-3">
                <?php
                $query = "SELECT anuncios.id_anuncio, anuncios.nome_produto, anuncios.preco, medidas.abreviatura, anuncios.capa FROM anuncios
                        INNER JOIN 
                        categorias ON anuncios.ref_categoria = categorias.id_categoria
                        INNER JOIN 
                        medidas ON anuncios.ref_medida = medidas.id_medida
                        WHERE 
                     categorias.id_categoria = ?";

                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_bind_param($stmt, "i", $id_categoria);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_store_result($stmt);


                    if (mysqli_stmt_num_rows($stmt) > 0) {
                        mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $medida, $capa);
                        while (mysqli_stmt_fetch($stmt)) {

                            ?>

                            <div class="col-6">
                                <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                                    <!-- Ícone de favorito -->
                                    <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                                        <span class="material-symbols-outlined verde_escuro">favorite</span>
                                    </div>

                                    <!-- Link do produto -->
                                    <a href="../Paginas/produto.php?id=<?= $id_anuncio ?>" style="text-decoration: none">
                                        <!-- Imagem -->
                                        <div class="imagem_card_pesquisa">
                                            <img src="../uploads/capas/<?php echo htmlspecialchars($capa); ?>" class="card-img-top rounded-4 img_hp_card" alt="<?= htmlspecialchars($nome_produto) ?>">
                                        </div>

                                        <!-- Título -->
                                        <div class="card-body m-2 pt-2 px-2 pb-0">
                                            <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3 text-truncate">
                                                <?= htmlspecialchars($nome_produto) ?>
                                            </h6>
                                        </div>
                                    </a>

                                    <hr class="linha-card verde_escuro">

                                    <!-- Rodapé -->
                                    <div class="card-body m-2 pt-0 pb-2 px-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="verde_escuro fw-bolder fs-5">
                                                <i class="bi bi-star-fill"></i>
                                            </small>
                                            <small class="fw-bolder verde_escuro fs-5">
                                                <?= number_format($preco, 2) ?> € / <?= htmlspecialchars($medida) ?>
                                            </small>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        echo '<p class="text-center mt-4">Não há produtos correspondentes a esta categoria.</p>';
                    }

                    mysqli_stmt_close($stmt);
                } else {
                    echo '<p class="text-danger">Erro na preparação da query.</p>';
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
