<?php
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    // Se não estiver logado, usar dados padrão
    $nome = "Convidado";
    $capa = "defaultpfp.png";
    $noti_nao_lidas = 0; // Garantir variável definida para o badge
} else {
    $id_user = $_SESSION['id_user'];
    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT nome, pfp FROM users WHERE id_user = ?";

    $capa = "defaultpfp.png"; // imagem padrão caso não tenha capa

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nome_db, $capa_db);

        if (mysqli_stmt_fetch($stmt)) {
            if (!empty($nome_db)) {
                $nome = $nome_db;
            }
            if (!empty($capa_db)) {
                $capa = $capa_db;
            }
        }
        mysqli_stmt_close($stmt);
    }

}
?>

<main class="body_index">


    <div class="text-center mb-4">
        <img src="../uploads/pfp/<?php echo htmlspecialchars($capa); ?>" alt="Foto de perfil" class="rounded-circle border border-success imagempfp" style="object-fit: cover;">
        <h2 class="mt-2 verde_escuro"><?php echo htmlspecialchars($nome); ?></h2>
    </div>

    <div class="card border-0 shadow-sm ">
        <div class="list-group list-group-flush">

            <?php if (!isset($_SESSION['id_user'])) { ?>
                <a href="../Paginas/login.php" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/login_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Login
                </a>
            <?php } ?>

            <?php if (isset($_SESSION['id_user'])) { ?>
                <a href="../Paginas/meus_anuncios.php" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/slide_library_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Os meus anúncios
                </a>

                <a href="../Paginas/encomendas.php" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/orders_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Meus pedidos
                </a>

                <a href="../Paginas/encomendas_recebidas.php" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/trolley_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Encomendas recebidas
                </a>

                <a href="../Paginas/perfil_details.php" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/tune_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Dados pessoais
                </a>


            <?php } ?>


            <?php if (isset($_SESSION['id_user'])): ?>
                <a href="../Paginas/notificacoes.php" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg position-relative">
                    <div class="me-3">
                        <span class="material-symbols-outlined text-dark">
                            Notifications
                        </span>
                    </div>
                    Notificações
                    <span id="noti-badge-perfil" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        0
                    </span>
                </a>

                <a href="../Paginas/favoritos.php" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                    <img src="../Imagens/icons/favorite_24dp_000000_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Favoritos
                </a>

                <a href="../scripts/sc_logout.php" class="verde_escuro list-group-item list-group-item-action d-flex align-items-center text-danger verde_claro_bg">
                    <img src="../Imagens/icons/logout_24dp_DC4C64_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" >
                    Logout
                </a>
            <?php endif; ?>
        </div>
    </div>

</main>
