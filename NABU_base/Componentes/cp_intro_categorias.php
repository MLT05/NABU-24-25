<div class="mt-3 mb-2 overflow-auto categorias-wrapper">
    <div class="d-flex flex-nowrap verde_escuro fw-normal">
        <?php
        require_once '../Connections/connection.php';
        $link = new_db_connection();
        $stmt = mysqli_stmt_init($link);
        $query = "SELECT id_categoria, nome_categoria FROM categorias WHERE id_categoria >= 1";

        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id_categoria, $nome_categoria);

            while (mysqli_stmt_fetch($stmt)) {
                echo '<a href="cp_pesquisacategoria.php?id_categoria=' . $id_categoria . '" class="categoria-item fs-6 text-decoration-none me-3 verde_escuro">'
                    . htmlspecialchars($nome_categoria) . '</a>';

            }

            mysqli_stmt_close($stmt);
        } else {
            // Erro na preparação da query
            echo "Erro: " . mysqli_error($link);
        }

        mysqli_close($link);
        ?>
    </div>
</div>
