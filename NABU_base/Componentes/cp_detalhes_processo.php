<main class="body_index">
    <div class="mt-5">
        <div class="card shadow-lg">
            <div class="card-header verde_claro_bg text-white text-center">
                <h1 class="verde_escuro fw-bold ">Detalhes do Pedido</h1>
            </div>

            <?php
             //Ligação á base de dados
            include_once '.\Connections\connectionDB.php';
            if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $link = new_db_connections(); $stmt = mysqli_stmt_init($link);

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
        mysqli_stmt_bind_result($stmt, $id_produto, $produto, $id_encomenda, $encomenda, $pagamentos_forma, $quantidade, $data_pedido, $data_aprovada, $estado, $vendedor_nome, $vendedor_email, $comprador_nome, $comprador_email);
        echo $id;
        while (mysqli_stmt_fetch($stmt)) {

            echo "<div class='alert alert-success' role='alert'>
                <h4 class='alert-heading'>Detalhes do Pedido</h4>
                <p>Pedido ID:"  <?= $id_encomendas ?> '</p>
                <hr>
                <p class='mb-0'>Produto: '<?= $produto_id?>' </p>
                <p class='mb-0'>Género: $tipo</p>
                <p class='mb-0'>Ano: $ano</p>
                <p class='mb-0'>Sinopse: $sinopse</p>

            <div class="card-body">

                <div class="row mb-4">
                    <!-- Coluna da imagem -->
                    <div class="col-sm-4 text-center mb-3 mb-sm-0">
                        <img src="../Imagens/produtos/<=?" alt="Imagem do Produto" class="img-fluid rounded shadow-sm">
                    </div>

                    <!-- Coluna de informações do produto -->
                    <div class="col-sm-8">
                        <p><strong class="verde_escuro">Produto:</strong> Tomates - Cacho</p>
                        <p><strong  class="verde_escuro">Quantidade:</strong> 2 unidades</p>
                        <p><strong  class="verde_escuro">Método de Pagamento:</strong> Cartão de Crédito</p>
                        <p><strong  class="verde_escuro">Data do Pedido:</strong> 27/05/2025 14:43</p>
                        <p><strong  class="verde_escuro">Data de Aprovação:</strong> <em>Aguardando aprovação...</em></p>
                        <p><strong  class="verde_escuro">Estado Atual:</strong>
                            <span class="badge bg-warning text-dark">A aguardar confirmação do vendedor...</span>
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-sm-6">
                        <h1 class="verde_escuro fw-bold mb-4">Vendedor</h1>
                        <p><strong>Nome:</strong> Rosa Silva</p>
                        <p><strong>Email:</strong> rosa.silva1954@gmail.com</p>
                    </div>
                    <div class="col-sm-6">
                        <h5>Comprador</h5>
                        <p><strong>Nome:</strong> Teresa Oliveira</p>
                        <p><strong>Email:</strong> t.oliveira@gmail.com</p>
                    </div>
                </div>

            </div>
            <div class="card-footer text-center">
                <small class="text-muted">Estado do pedido: Em processo...</small>
            </div>
        </div>
    </div>'"'
    
    } else {
        echo ("Error description:") . mysqli_error($link);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    echo "Error for not being clear about what film;";
}
?>
</main>