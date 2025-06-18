<main class="body_index">
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
            } 
            // Preparar e executar a consulta
            if (mysqli_stmt_prepare($stmt, $query)) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $id_produto, $produto, $id_encomenda, $encomenda, $pagamentos_forma, $quantidade, $data_pedido, $data_aprovada, $estado, $vendedor_nome, $vendedor_email, $comprador_nome, $comprador_email, $estado_aprovacaoo, $categoria);
                echo $id;}
                while (mysqli_stmt_fetch($stmt)) {

                    echo "
    <div class="container py-4">
        <div class="text-center mb-4">
            <img src="../Imagens/pfp/mulhercampo.jpeg" alt="Foto de perfil" class="rounded-circle border border-success imagempfp"  style="object-fit: cover;">
            <h2 class="mt-2 verde_escuro"> <?= $nome ?></h2>
            
        </div>

        <div class="card border-0 shadow-sm ">
            <div class="list-group  list-group-flush">

                <a href="..\mudar_perfil.php" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/slide_library_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Alterar Imagem de Perfil
                </a>

                <a href="#" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/orders_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Mudar Nome
                </a>

                <a href="#" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/tune_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Mudar nome de Utilizador
                </a>

                <a href="#" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/account_balance_wallet_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Data de Nascimento
                </a>

                <a href="#" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/settings_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3">
                    Biografia
                </a>

                <a href="" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/favorite_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Alterar Email
                </a>

                <a href="#" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center text-danger verde_claro_bg">
                    <img src="../Imagens/icons/logout_24dp_DC4C64_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Alterar Palavra-passe
                </a>
            </div>
        </div>
    </div>
';}?>
</main>
