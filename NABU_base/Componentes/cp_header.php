<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once '../Functions/function_notificacao.php';
?>


<header class="cabecalho fixed-top container-fluid">
    <div class="row py-3 align-items-center justify-content-between">
        <a href="../Paginas/index.php" class="col-auto">
            <img src="../Imagens/img_cp/logo_nabu.svg" alt="Logo" class="logo-img">
        </a>
        <a href="../Paginas/carrinho.php" class="col-auto">
            <img src="../Imagens/img_cp/shopping_bag_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Carrinho" class="carrinho-img">
        </a>
        <?php
        if (isset($_SESSION['id_user'])) {
            require_once '../Connections/connection.php';
            $link = new_db_connection();

            $stmt = mysqli_stmt_init($link);
            $query = "SELECT COUNT(*) FROM notificacoes WHERE users_id_user = ? AND lida = FALSE";
            if (mysqli_stmt_prepare($stmt, $query)) {
                mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_user']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $noti_nao_lidas);
                mysqli_stmt_fetch($stmt);
                mysqli_stmt_close($stmt);
            }
            mysqli_close($link);
        }
        ?>

        <a href="../Paginas/notificacoes.php" class="col-auto position-relative">
            <img src="../Imagens/icons/notifications_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Notificações" class="carrinho-img">
            <?php if (!empty($noti_nao_lidas) && $noti_nao_lidas > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
            <?= $noti_nao_lidas ?>
        </span>
            <?php endif; ?>
        </a>
    </div>
</header>
