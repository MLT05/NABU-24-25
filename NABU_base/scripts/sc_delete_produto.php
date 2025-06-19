<?php
require_once '../Connections/connection.php';
if (isset($_POST['id_anuncio'])) {
    $id_anuncio = (int) $_POST['id_anuncio'];
    $ref_user = $_SESSION['id_user']; // Para garantir que está autenticado

    if ($ref_user === 0) {
        die("Erro: Utilizador não autenticado.");
    }

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Só apaga se o anúncio pertencer ao utilizador autenticado
    $query = "DELETE FROM anuncios WHERE id_anuncio = ? AND ref_user = ?";

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "ii", $id_anuncio, $ref_user);

        if (!mysqli_stmt_execute($stmt)) {
            echo "Erro ao apagar anúncio: " . mysqli_stmt_error($stmt);
        } else {
            // Apagado com sucesso - redirecionar
            header("Location: ../Paginas/meus_anuncios.php");
            exit();
        }
    } else {
        echo "Erro na preparação da query: " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    echo "ID do anúncio não fornecido.";
}
?>
