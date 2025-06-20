<?php
session_start();
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user']) || !isset($_POST['id_anuncio'])) {
    http_response_code(400);
    echo "Parâmetros inválidos.";
    exit;
}

$id_user = $_SESSION['id_user'];
$id_anuncio = $_POST['id_anuncio'];

$link = new_db_connection();
$stmt = mysqli_stmt_init($link);

$query = "DELETE FROM carrinho WHERE ref_user = ? AND anuncios_id_anuncio = ?";

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, 'ii', $id_user, $id_anuncio);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
        echo "Removido com sucesso.";
    } else {
        http_response_code(404);
        echo "Item não encontrado.";
    }

    mysqli_stmt_close($stmt);
} else {
    http_response_code(500);
    echo "Erro ao preparar a query.";
}

mysqli_close($link);
?>
