
<main class="body_index">
<?php

require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    // Se não estiver logado, redireciona pro login
    header("../Paginas/login.php");

} else {


    $id_user = $_SESSION['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT id_anuncio, nome_produto, preco, abreviatura , capa FROM anuncios INNER JOIN medidas ON ref_medida = id_medida WHERE ref_user = ?";

$capa = "default-image.jpg"; // imagem padrão caso não tenha capa
?>
    <section class="mb-5">
        <h1 class="verde_escuro">Meus anúncios:</h1>
        <div class="row g-3">

<?php


    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $medida, $capa);

        while(mysqli_stmt_fetch($stmt)) { ?>

            <div class="col-6">
                <a href="../paginas/produto.php" style="text-decoration: none">
                    <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">




                        <!-- Imagem -->
                        <div class="imagem_card_pesquisa">
                            <img src="../uploads/anuncios/default-image.jpg" class="card-img-top rounded-4 img_hp_card" alt="Tomates">
                        </div>

                        <!-- Conteúdo -->
                        <div class="card-body m-2 pt-2 px-2 pb-0">
                            <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3"><?php echo htmlspecialchars($nome_produto); ?></h6>
                        </div>

                        <hr class="linha-card verde_escuro">

                        <div class="card-body m-2 pt-0 pb-2 px-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <small class=" verde_escuro fw-bolder fs-5">
                                    <?php echo htmlspecialchars($medida); ?>
            </small>
                                <small class="fw-bolder verde_escuro fs-5"><?php echo htmlspecialchars($preco); ?> €</small>
                            </div>
                        </div>
                </a>
            </div>
        </div>

            <?php
        }



        mysqli_stmt_close($stmt);
    }


    mysqli_close($link);
}
?>


        </div>

    </section>

</main>