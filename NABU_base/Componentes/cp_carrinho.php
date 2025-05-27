<?php
include_once ("cp_intro_carrinho.php");
?>

<main class="body_index">
    <section class="mb-4">
        <div>
            <h1 class="verde_escuro">A sua cestinha</h1>
            <p class="verde">Finalize a sua compra</p>
        </div>

        <!-- Card 1 -->

        <div class="card mb-3 cards_homepage">
            <div class="row g-0 align-items-center">
                <div class="col-4">
                    <img src="../Imagens/produtos/tomates.svg" class="img-fluid imagem_card_homepage rounded-start" alt="Tomates">
                </div>
                <div class="col-7">
                    <div class="card-body py-2">
                        <h2 class="verde fw-bold mb-1" >Tomates - Cacho</h2>
                        <p class="card-text mb-0"><small class="text-muted">Rosa Silva</small></p>
                    </div>
                </div>
                <div class="col-1 text-end pe-2">
                    <img src="../Imagens/img_cp/close_24dp_004D40_FILL0_wght400_GRAD0_opsz24.svg" alt="Remover" class="icone-x">

                </div>
            </div>
        </div>


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


