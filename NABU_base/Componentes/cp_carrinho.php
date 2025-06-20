<?php
include_once ("cp_intro_carrinho.php");
?>

<main class="body_index">

    <section class="mb-4">
    <div>
        <h1 class="verde_escuro">A sua cestinha</h1>
        <p class="verde">Finalize a sua compra</p>
    </div>
    <?php

    require_once '../Connections/connection.php';

    if (!isset($_SESSION['id_user'])) {
        // Se não estiver logado, redireciona pro login
        header("../Paginas/login.php");

    } else {


    $id_user = $_SESSION['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT anuncios.id_anuncio, anuncios.nome_produto, medidas.abreviatura , anuncios.capa, carrinho.quantidade, carrinho.valor  FROM anuncios INNER JOIN medidas ON ref_medida = id_medida INNER JOIN carrinho ON anuncios_id_anuncio = id_anuncio WHERE carrinho.ref_user = ?";

    $capa = "default-image.jpg"; // imagem padrão caso não tenha capa
    ?>



        <?php


        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $id_user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $medida, $capa , $quantidade, $valor);

            while(mysqli_stmt_fetch($stmt)) { ?>

        <!-- Card 1 -->

        <div class="card mb-3 cards_homepage overflow-hidden" style="height: 15vh;">
            <div class="row g-0 h-100">

                <div class="col-4">
                    <div class="h-100 w-100">
                        <img src="../uploads/capas/<?php echo htmlspecialchars($capa); ?>"
                             alt="<?php echo htmlspecialchars($capa); ?>"
                             class="img-fluid h-100 w-100 object-fit-cover rounded-start">
                    </div>
                </div>

                <div class="col-7 d-flex align-items-center">
                    <div class="card-body py-2">
                        <h2 class="verde_escuro fw-bold mb-1"><?php echo htmlspecialchars($nome_produto); ?></h2>
                        <p class="card-text verde mb-0"><small>vendedor</small></p>
                    </div>
                </div>

                <div class="col-1 d-flex align-items-start justify-content-end pe-2 pt-2">
                    <img src="../Imagens/img_cp/close_24dp_004D40_FILL0_wght400_GRAD0_opsz24.svg"
                         alt="Remover"
                         class="icone-x">
                </div>

            </div>
        </div>

        <?php
        }



        mysqli_stmt_close($stmt);
    }


    mysqli_close($link);
}
?>


        <!-- Botão Finalizar -->
        <div class="top-buttons">
            <button type="button" class="btn botao_carrinho" data-bs-toggle="modal" data-bs-target="#pedidoModal">
                Finalizar Pedido
            </button>
        </div>

    </section>


    <!-- Modal Overlay -->
    <div class="modal fade" id="pedidoModal" tabindex="-1" aria-labelledby="pedidoModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered ">
            <div class="modal-content text-center">
                <div class="modal-header border-0">
                    <h5 class="modal-title w-100" id="pedidoModalLabel">Pedido realizado com sucesso!</h5>
                </div>
                <div class="modal-body">
                    <p>Aguarde confirmação do vendedor.</p>
                </div>
                <div class="modal-footer justify-content-center border-0">
                    <a href="../Paginas/encomendas.php" class="btn btn-success">Ver Pedido</a>
                    <a href="../Paginas/carrinho.php" class="btn btn-outline-secondary">Voltar ao Carrinho</a>
                </div>
            </div>
        </div>
    </div>



</main>


