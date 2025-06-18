<?php
function toggle_favorito_post() {
    if (!isset($_SESSION)) {
        session_start();
    }

    if (!isset($_SESSION['id_user'])) {
        return "⚠️ Necessita estar logado para gerir favoritos.";
    }

    if (!isset($_POST['id_anuncio_favorito']) || !is_numeric($_POST['id_anuncio_favorito'])) {
        return "⚠️ ID do anúncio inválido ou ausente.";
    }

    require_once '../connections/connection.php';
    $link = new_db_connection();

    $id_user = $_SESSION['id_user'];
    $id_anuncio = (int)$_POST['id_anuncio_favorito'];

    $stmt = mysqli_stmt_init($link);
    $query_check = "SELECT 1 FROM favoritos WHERE users_id_user = ? AND anuncios_id_anuncio = ?";
    if (mysqli_stmt_prepare($stmt, $query_check)) {
        mysqli_stmt_bind_param($stmt, 'ii', $id_user, $id_anuncio);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Remove dos favoritos
            mysqli_stmt_close($stmt);
            $stmt_del = mysqli_stmt_init($link);
            $query_del = "DELETE FROM favoritos WHERE users_id_user = ? AND anuncios_id_anuncio = ?";
            if (mysqli_stmt_prepare($stmt_del, $query_del)) {
                mysqli_stmt_bind_param($stmt_del, 'ii', $id_user, $id_anuncio);
                mysqli_stmt_execute($stmt_del);
                mysqli_stmt_close($stmt_del);
                mysqli_close($link);
                return "❌ Removido dos favoritos.";
            }
        } else {
            // Adiciona aos favoritos
            mysqli_stmt_close($stmt);
            $stmt_add = mysqli_stmt_init($link);
            $query_add = "INSERT INTO favoritos (data_insercao, users_id_user, anuncios_id_anuncio) VALUES (NOW(), ?, ?)";
            if (mysqli_stmt_prepare($stmt_add, $query_add)) {
                mysqli_stmt_bind_param($stmt_add, 'ii', $id_user, $id_anuncio);
                mysqli_stmt_execute($stmt_add);
                mysqli_stmt_close($stmt_add);
                mysqli_close($link);
                return "✅ Adicionado aos favoritos.";
            }
        }
    }

    mysqli_close($link);
    return "⚠️ Erro ao gerir favorito.";
}
?>
