


<footer class="footer-menu fixed-bottom verde_bg ">
    <div class="d-flex w-100 h-100">
        <a class="menu-item flex-fill text-center text-white text-decoration-none" href="../Paginas/index.php">
            <img src="../Imagens/icons/home_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Início" class="menu-icon">
            <p class="menu-label">Início</p>
        </a>
        <a class="menu-item flex-fill text-center text-white text-decoration-none" href="../Paginas/pesquisa.php">
            <img src="../Imagens/icons/search_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Pesquisa" class="menu-icon ">
            <p class="menu-label">Pesquisa</p>
        </a>
        <a class="menu-item flex-fill text-center text-white text-decoration-none" href="../Paginas/add_produto.php">
            <img src="../Imagens/icons/add_circle_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Vender" class="menu-icon">
            <p class="menu-label">Vender</p>
        </a>
        <a class="menu-item flex-fill text-center text-white text-decoration-none" href="../Paginas/mensagens.php">
            <img src="../Imagens/icons/chat_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Mensagens" class="menu-icon">
            <p class="menu-label">Mensagens</p>
        </a>
        <a class="menu-item flex-fill text-center text-white text-decoration-none" href="../Paginas/perfil.php">
            <?php


            if (isset($_SESSION['id_user'])) {
                require_once '../Connections/connection.php';
                $link = new_db_connection();
                $stmt = mysqli_stmt_init($link);
                $query = "SELECT pfp FROM users WHERE id_user = ?";
                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_user']);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $pfp_db);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);

                    if (!empty($pfp_db)) {
                        $pfp_path = '../uploads/pfp/' . $pfp_db;
                        echo '<img src="' . htmlspecialchars($pfp_path) . '" alt="Perfil" class="menu-icon rounded-circle" style=" object-fit:cover;">';
                    } else {
                        // Se não tem foto, mostrar o icon padrão
                        echo '<img src="../Imagens/icons/person_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Perfil" class="menu-icon">';
                    }
                } else {
                    mysqli_close($link);
                    // Em caso de erro, mostrar icon padrão
                    echo '<img src="../Imagens/icons/person_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Perfil" class="menu-icon">';
                }
            } else {
                // Se não está logado, mostrar icon padrão
                echo '<img src="../Imagens/icons/person_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Perfil" class="menu-icon">';
            }
            ?>
            <p class="menu-label">Perfil</p>
        </a>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
