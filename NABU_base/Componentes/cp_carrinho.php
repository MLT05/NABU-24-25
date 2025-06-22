<?php
include_once ("cp_intro_carrinho.php");
?>

<main class="body_index">

    <section class="mb-4">
    <div>
        <h1 class="verde_escuro">A sua cestinha</h1>
        <p class="verde">Finalize a sua compra aqui! Adicione produtos à cestinha </p>
    </div>
    <?php

    require_once '../Connections/connection.php';
    $valor_total = 0;
    if (!isset($_SESSION['id_user'])) {
        // Se não estiver logado, redireciona pro login
        header("../Paginas/login.php");

    } else {


    $id_user = $_SESSION['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

        $query = "
SELECT 
    anuncios.id_anuncio, 
    anuncios.nome_produto,
    anuncios.preco, 
    medidas.abreviatura, 
    anuncios.capa, 
    carrinho.quantidade,
    users.nome AS nome_vendedor
FROM anuncios 
INNER JOIN medidas ON anuncios.ref_medida = medidas.id_medida 
INNER JOIN carrinho ON carrinho.anuncios_id_anuncio = anuncios.id_anuncio 
INNER JOIN users ON users.id_user = anuncios.ref_user
WHERE carrinho.ref_user = ?
";



    $capa = "default-image.jpg"; // imagem padrão caso não tenha capa
        $tem_produtos = false; // flag para saber se há produtos

        ?>



        <?php


        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $id_user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $medida, $capa , $quantidade, $nome_vendedor);



            while(mysqli_stmt_fetch($stmt)) {
                $tem_produtos = true;
                $valor = $preco * $quantidade;

                ?>

        <!-- Card 1 -->

        <div class="card mb-3 cards_homepage overflow-hidden" style="height: 15vh;"  id="card-<?php echo $id_anuncio; ?>">
            <div class="row g-0 h-100">

                <div class="col-4">
                    <div class="h-100 w-100">
                        <img src="../uploads/capas/<?php echo htmlspecialchars($capa); ?>"
                             alt="<?php echo htmlspecialchars($capa); ?>"
                             class="img-fluid h-100 w-100 object-fit-cover rounded-start">
                    </div>
                </div>

                <div class="col-7 d-flex align-items-stretch">
                    <div class="card-body d-flex flex-column justify-content-between w-100 py-2">
                        <div>
                            <h2 class="verde_escuro fw-bold mb-1 text-truncate"><?php echo htmlspecialchars($nome_produto); ?></h2>
                            <p class="card-text verde mb-0 text-truncate"><small><?php echo htmlspecialchars($nome_vendedor); ?></small></p>
                        </div>

                        <div class="d-flex justify-content-between">
                            <h3 class="verde_escuro  mb-1"><?php echo htmlspecialchars($preco); ?>€/<?php echo htmlspecialchars($medida); ?></h3>

                        </div>
                    </div>
                </div>


                <div class="col-1 position-relative pe-2 pt-2">
                    <!-- Ícone no topo -->
                    <img src="../Imagens/img_cp/close_24dp_004D40_FILL0_wght400_GRAD0_opsz24.svg"
                         alt="Remover"
                         class="icone-x btn-remover"
                         data-id="<?php echo $id_anuncio; ?>"
                         style="cursor: pointer;">

                    <!-- Preço fixado embaixo -->
                    <h2 class="verde_escuro fw-bold mb-2 px-2 position-absolute bottom-0 end-0">
                        <?php echo htmlspecialchars($valor); ?>€
                    </h2>
                </div>




            </div>
        </div>

        <?php

                $valor_total = $valor_total + $valor;
        }
            // Se não houver produtos, mostrar mensagem
            if (!$tem_produtos) {
                echo '<div class="text-center mt-5">';
                echo '<h2 class="verde_escuro">A sua cestinha está vazia</h2>';
                echo "<p class='text-muted'>Explore os nossos produtos disponíveis <a href='../Paginas/pesquisa.php' class='verde_escuro'><strong>aqui</strong></a> </p>";
                echo '</div>';
            }



        mysqli_stmt_close($stmt);
    }


    mysqli_close($link);
}


?>
        <?php if ($valor_total > 0): ?>
            <div class="d-flex justify-content-end mb-3">
                <h3 class="verde_escuro fw-bold">Total: <?php echo number_format($valor_total, 2, ',', '.'); ?>€</h3>
            </div>


        <!-- Botão Finalizar -->
        <div class="top-buttons">
                <button type="button" class="btn botao_carrinho">
                    Finalizar Pedido
                </button>
        </div>
        <?php endif; ?>
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
                    <a href="../Paginas/encomendas.php" class="btn btn-success">Ver Pedidos</a>
                    <a href="../Paginas/index.php" class="btn btn-outline-secondary">Continuar a comprar</a>
                </div>
            </div>
        </div>
    </div>



</main>
<script>
    document.querySelector(".botao_carrinho")?.addEventListener("click", function () {
        fetch("../scripts/sc_add_encomenda.php", {
            method: "POST"
        })
            .then(async res => {
                const data = await res.json();
                if (res.ok && data.success) {
                    // Só entra aqui se o servidor respondeu com 200 e sucesso
                    const modal = new bootstrap.Modal(document.getElementById('pedidoModal'));
                    modal.show();

                    // Limpar interface
                    document.querySelectorAll(".cards_homepage").forEach(card => card.remove());
                    document.querySelector(".d-flex.justify-content-end")?.remove();
                    document.querySelector(".top-buttons")?.remove();
                } else {
                    alert("Erro: " + (data.mensagem || "Erro desconhecido."));
                }
            })
            .catch(err => {
                alert("Erro de rede ao finalizar o pedido.");
                console.error(err);
            });
    });

    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".btn-remover").forEach(function (btn) {
            btn.addEventListener("click", function () {
                const idAnuncio = this.getAttribute("data-id");
                const card = document.getElementById("card-" + idAnuncio);

                // Encontrar o preço do produto no próprio card
                const valorTexto = card.querySelector("h2.fw-bold.px-2").innerText.replace('€', '').replace(',', '.');
                const valor = parseFloat(valorTexto);

                fetch("../scripts/sc_remover_carrinho.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded"
                    },
                    body: "id_anuncio=" + encodeURIComponent(idAnuncio)
                })
                    .then(response => {
                        if (!response.ok) throw new Error("Erro ao remover.");
                        return response.text();
                    })
                    .then(data => {
                        card.remove(); // Remove o card

                        // Atualiza o total
                        const totalSpan = document.getElementById("valor-total");
                        let totalAtual = parseFloat(totalSpan.innerText.replace('€', '').replace('.', '').replace(',', '.'));
                        let novoTotal = totalAtual - valor;

                        // Formata para "0,00€"
                        if (novoTotal <= 0) {
                            // Remove o total
                            const totalContainer = totalSpan.closest(".d-flex");
                            if (totalContainer) totalContainer.remove();

                            // Remove o botão de finalizar
                            const botaoFinalizar = document.querySelector(".top-buttons");
                            if (botaoFinalizar) botaoFinalizar.remove();
                        } else {
                            totalSpan.innerText = novoTotal.toFixed(2).replace('.', ',') + "€";
                        }
                    })
                    .catch(err => {
                        alert("Erro ao remover o produto.");
                        console.error(err);
                    });
            });
        });
    });
</script>


