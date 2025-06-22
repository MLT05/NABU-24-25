<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$pagina_atual = basename($_SERVER['PHP_SELF']);

?>


<header class="cabecalho fixed-top container-fluid">
    <div class="row py-3 align-items-center justify-content-between">
        <div class="col-auto">
            <a href="javascript:history.back()" class="d-flex align-items-center"
               style="text-decoration: none;<?php echo ($pagina_atual === 'index.php') ? 'visibility: hidden;' : ''; ?>">
                <span class="material-icons" style="color: white; font-size: 3rem;">arrow_back</span>
            </a>
        </div>

        <!-- Título NABU -->
        <div class="col text-center">
            <a href="../Paginas/index.php" class="text-decoration-none text-center text-white">
                <img src="../Imagens/app/NABU-LOGO.png" style="height: 5vh">
            </a>
        </div>
        <a href="../Paginas/carrinho.php" class="col-auto">
            <img src="../Imagens/img_cp/shopping_bag_35dp_FFFFFF_FILL0_wght300_GRAD-25_opsz48.svg" alt="Carrinho" class="carrinho-img" style="height: 5vh">
        </a>
    </div>
</header>
