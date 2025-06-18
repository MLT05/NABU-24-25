<div class="mt-3 mb-2 overflow-auto categorias-wrapper">
    <div class="d-flex flex-nowrap verde_escuro fw-normal">
        <?php
        require_once '../Connections/connection.php';
        $link_categorias = new_db_connection();
        $stmt_categorias = mysqli_stmt_init($link_categorias);
        $query = "SELECT id_categoria, nome_categoria FROM categorias WHERE id_categoria >= 1";

        if (mysqli_stmt_prepare($stmt_categorias, $query)) {
            mysqli_stmt_execute($stmt_categorias);
            mysqli_stmt_bind_result($stmt_categorias, $id_categoria, $nome_categoria);

            while (mysqli_stmt_fetch($stmt_categorias)) {
                echo '<a href="cp_pesquisacategoria.php?id_categoria=' . $id_categoria . '" class="categoria-item fs-6 text-decoration-none me-3 verde_escuro">'
                    . htmlspecialchars($nome_categoria) . '</a>';
            }

            mysqli_stmt_close($stmt_categorias);
        } else {
            echo "Erro: " . mysqli_error($link_categorias);
        }

        mysqli_close($link_categorias);
        ?>
    </div>
</div>
