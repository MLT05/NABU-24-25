<?php

// Conexão à base de dados
require_once '../Connections/connection.php';
$link = new_db_connection();


?>

<main class="body_index">
<div class="mt-3">

    <!-- 🔍 Pesquisa -->
    <?php
    require_once '../Componentes/cp_intro_pesquisa.php';
    ?>

    <!-- 📁 Categorias com scroll horizontal -->
    <div class="mt-3 mb-2 overflow-auto categorias-wrapper">
        <div class="d-flex flex-nowrap verde_escuro fw-normal">
            <span class="fw-bold text-decoration-underline categoria-item fs-6">Todos</span>
            <span class="categoria-item fs-6">Vegetais</span>
            <span class="categoria-item fs-6">Frutas</span>
            <span class="categoria-item fs-6">Ovos e laticínios</span>
            <span class="categoria-item fs-6">Produtos Apícolas</span>
            <span class="categoria-item fs-6">Plantas</span>
        </div>
    </div>

    <!-- 🧺 Produtos -->
    <div class="row g-3">
        <?php
        // Código PHP para ir buscar os produtos à base de dados
        $stmt = mysqli_stmt_init($link);
        $query = "SELECT anuncios.nome_produto, anuncios.preco, anuncios.id_anuncio FROM anuncios ";

        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $nome, $preco, $id_anuncio);

            while (mysqli_stmt_fetch($stmt)) {
                ?>
                <div class="col-6">
                    <a href="../Paginas/produto.php?id=<?= $id_anuncio ?>" style="text-decoration: none">
                        <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                            <!-- Ícone de favorito no canto superior direito -->
                            <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                    <span class="material-symbols-outlined verde_escuro">
                        favorite
                    </span>
                            </div>

                            <!-- Imagem -->
                            <div class="imagem_card_pesquisa">
                                <img src="../Imagens/produtos/<?php echo $id_anuncio; ?>" class="card-img-top rounded-4 img_hp_card" alt="<?php echo $nome; ?>">
                            </div>

                            <!-- Conteúdo -->
                            <div class="card-body m-2 pt-2 px-2 pb-0">
                                <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3"><?php echo $nome; ?></h6>
                            </div>

                            <hr class="linha-card verde_escuro">

                            <div class="card-body m-2 pt-0 pb-2 px-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="verde_escuro fw-bolder fs-5">
                                        <i class="bi bi-star-fill"></i> <?php echo number_format($avaliacao, 1); ?>
                                    </small>
                                    <small class="fw-bolder verde_escuro fs-5"><?php echo number_format($preco, 2); ?> €</small>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
                <?php
            }
            mysqli_stmt_close($stmt);
        }
        mysqli_close($link);
        ?>
    </div>


</main>