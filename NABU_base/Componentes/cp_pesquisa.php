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
    require_once '../Componentes/cp_intro_categorias.php';
    ?>



    <!-- 🧺 Produtos -->
    <div class="row g-3">
        <?php
        // Código PHP para ir buscar os produtos à base de dados
        $stmt = mysqli_stmt_init($link);
        $query = "SELECT id_anuncio, nome_produto, preco, abreviatura , capa FROM anuncios INNER JOIN medidas ON ref_medida = id_medida WHERE ref_user = ?";
        $capa = "default-image.jpg"; // imagem padrão caso não tenha capa

        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $id_user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $medida, $capa);

            while(mysqli_stmt_fetch($stmt)) {
                ?>
                <div class="col-6">
                    <a href="../paginas/produto.php?id=<?php echo htmlspecialchars($id_anuncio); ?>" style="text-decoration: none">                        <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                            <!-- Ícone de favorito no canto superior direito -->
                            <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                    <span class="material-symbols-outlined verde_escuro">
                        favorite
                    </span>
                            </div>

                            <!-- Imagem -->
                            <div class="imagem_card_pesquisa">
                                <img src="../uploads/capas/default-image.jpg" class="card-img-top rounded-4 img_hp_card" alt="Tomates">
                            </div>

                            <!-- Conteúdo -->
                            <div class="card-body m-2 pt-2 px-2 pb-0">
                                <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3 text-truncate"><?php echo htmlspecialchars($nome_produto); ?></h6>
                            </div>
                    </a>

                            <hr class="linha-card verde_escuro">
                    <div class="card-body m-2 pt-0 pb-2 px-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <form action="../Paginas/editar_produto.php" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($id_anuncio); ?>">
                                <button type="submit" class="btn verde_escuro_bg rounded-circle d-flex align-items-center justify-content-center p-0" style="width: 40px; height: 40px;">
                                    <span class="material-icons text-white">edit</span>
                                </button>
                            </form>
                            <small class="verde_escuro fw-bolder fs-5">
                                <i class="bi bi-star-fill"></i>
                            </small>
                            <small class="fw-bolder verde_escuro fs-5"><?php echo htmlspecialchars($preco); ?> € /<?php echo htmlspecialchars($medida); ?></small>
                        </div>

                    </div>


                <?php
            }

        }

        ?>
    </div>


</main>