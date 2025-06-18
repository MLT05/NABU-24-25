<main class="body_index">
    <div class="mt-5">
        <div class="card shadow-lg">
            <div class="card-header verde_claro_bg text-white text-center">
                <h1 class="verde_escuro fw-bold ">Detalhes do Pedido</h1>
            </div>

            <?php
            //Ligação á base de dados
            include_once '.\Connections\connectionDB.php';
             //Ligação á base de dados
            include_once '.\Connections\connection.php';
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $link = new_db_connections();
                $stmt = mysqli_stmt_init($link);

                $query = "SELECT id_produtos.produtos,  , url_trailer, url_imdb FROM produtos INNER JOIN generos ON generos.id_generos = filmes.ref_generos WHERE id_filmes = ?";
            } else {
                // Se não houver ID, redireciona para a página de erro ou lista
                header("Location: error_page.php");
                exit();
            }

            // Preparar e executar a consulta
            if (mysqli_stmt_prepare($stmt, $query)) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $id_produto, $produto, $id_encomenda, $encomenda, $pagamentos_forma, $quantidade, $data_pedido, $data_aprovada, $estado, $vendedor_nome, $vendedor_email, $comprador_nome, $comprador_email, $estado_aprovacaoo, $categoria);
                echo $id;
                while (mysqli_stmt_fetch($stmt)) {

                    echo "<div class= 'alert alert-success' role='alert'>
                <h4 class='alert-heading'> Detalhes do Pedido     </h4>
                <p>Pedido ID:  <?= $id_encomendas ?> </p>
                <hr>
                <p class='mb-0'>Produto: <?= $produto ?> </p>
            <div class="card - body">
                <div class="row mb - 4">
                    <!-- Coluna da imagem -->
                    <div class="col - sm - 4 text - center mb - 3 mb - sm - 0">
                        <img src=" ./Imagens / produtos / $id_produto" alt="Imagem do Produto" class="img - fluid rounded shadow - sm">
                    </div>
                    <!-- Coluna de informações do produto -->
            <div class="col - sm - 8">
                        <p><strong class="verde_escuro">categoria:</strong> <?= $categoria ?> </p>
                        <p><strong class="verde_escuro">Produto:</strong> <?= $produto ?> </p>
                        <p><strong  class="verde_escuro">Quantidade:</strong> <?= $quantidade ?> </p>
                        <p><strong  class="verde_escuro">Método de Pagamento:</strong> <?= $pagamentos_forma ?> </p>
                        <p><strong  class="verde_escuro">Data do Pedido:</strong> <?= $data_pedido ?></p>
                        <p><strong  class="verde_escuro">Data de Aprovação:</strong> <em>$<?= $estado_aprovação/data_aprovada ?></em></p>
                        <p><strong  class="verde_escuro">Estado Atual:</strong>
                            <span class="badge bg - warning text - dark"> <?= $estado! ?></span>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col - sm - 6">
                        <h1 class="verde_escuro fw - bold mb - 4">Vendedor</h1>
                        <p><strong>Nome:</strong> <?= $vendedor_nome ?> </p>
                        <p><strong>Email:</strong> <?= $vendedor_email ?> </p>
                    </div>
                    <div class="col - sm - 6"> <h5>Comprador</h5>
                        <p><strong>Nome:</strong> <?= $comprador_nome ?> </p>
                        <p><strong>Email:</strong> <?= $comprador_email ?> </p>
                    </div>
                </div>
            </div>
            <div class="card - footer text - center">
                <small class="text - muted">Estado do pedido: <?= $estado ?> </small>
            </div>
        </div>
    </div>";
}
                    echo ("Error description:") . mysqli_error($link);
                }
                mysqli_stmt_close($stmt);
                mysqli_close($link);
            ?>