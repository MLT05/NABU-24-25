<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$pagina_atual = basename($_SERVER['PHP_SELF']);
?>

<header class="cabecalho fixed-top container-fluid">
    <div class="row py-3 align-items-center justify-content-between">
        <!-- Ícone Voltar -->
        <div class="col-auto">
            <a href="javascript:history.back()" class="d-flex align-items-center"
               style="text-decoration: none; <?php echo ($pagina_atual === 'index.php') ? 'visibility: hidden;' : ''; ?>">
                <img src="../Imagens/img_cp/arrow_back_ios_new_24dp_FFFFFF_FILL0_wght200_GRAD0_opsz24.svg"
                     alt="Voltar" style="height: 5vh;">
            </a>
        </div>

        <!-- Logo Central -->
        <div class="col text-center">
            <a href="../Paginas/index.php" class="text-decoration-none text-white">
                <img src="../Imagens/app/NABU-LOGO.png" style="height: 6vh;">
            </a>
        </div>

        <!-- Ícone Carrinho -->
        <div class="col-auto">
            <a href="../Paginas/carrinho.php">
                <img src="../Imagens/img_cp/shopping_bag_35dp_FFFFFF_FILL0_wght300_GRAD-25_opsz48.svg"
                     alt="Carrinho" class="carrinho-img" style="height: 5vh;">
            </a>
        </div>
    </div>
</header>
