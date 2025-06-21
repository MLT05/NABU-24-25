<?php
if (isset($_POST['pesquisa'])) {
    $pesquisa = $_POST['pesquisa'];

    // Conexão à base de dados
    require_once '../Connections/connection.php';
    $link = new_db_connection();
    ?>

    <main class="body_index">
        <a href="javascript:history.back()" class=" text-decoration-none d-inline-flex " >
            <span class="material-icons verde_escuro" style="font-size: 2.5rem">arrow_back</span>

        </a>
        <div class="mt-3">

            <?php
            require_once '../Componentes/cp_intro_pesquisa.php';
            require_once '../Componentes/cp_intro_categorias.php';
            ?>


            <div class="row g-3">
                <?php
                $query = "SELECT nome_produto, preco, id_anuncio, capa FROM anuncios WHERE nome_produto LIKE CONCAT('%', ?, '%')";

                $stmt = mysqli_stmt_init($link);
                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_bind_param($stmt, "s", $pesquisa);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $nome, $preco, $id_anuncio, $capa);

                    while (mysqli_stmt_fetch($stmt)) {
                        ?>
                        <div class="col-6">
                            <a href="../Paginas/produto.php?id=<?= $id_anuncio ?>" style="text-decoration: none">
                                <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">
                                    <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                                        <span class="material-symbols-filled verde_escuro">favorite</span>
                                    </div>

                                    <div class="imagem_card_pesquisa">
                                        <img src="../Imagens/produtos/<?php echo htmlspecialchars($capa); ?>" class="card-img-top rounded-4 img_hp_card" alt="<?= htmlspecialchars($nome) ?>">
                                    </div>

                                    <div class="card-body m-2 pt-2 px-2 pb-0">
                                        <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3"><?= htmlspecialchars($nome) ?></h6>
                                    </div>

                                    <hr class="linha-card verde_escuro">

                                    <div class="card-body m-2 pt-0 pb-2 px-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="verde_escuro fw-bolder fs-5"><i class="bi bi-star-fill"></i> - </small>
                                            <small class="fw-bolder verde_escuro fs-5"><?= number_format($preco, 2) ?> €</small>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php
                    }
                    mysqli_stmt_close($stmt);
                } else {
                    echo "<p>Erro na pesquisa.</p>";
                }

                mysqli_close($link);
                ?>
            </div>
        </div>
    </main>

    <?php
} else {
    echo "<p>Não há produtos correspondentes à pesquisa</p>";
}
?>
