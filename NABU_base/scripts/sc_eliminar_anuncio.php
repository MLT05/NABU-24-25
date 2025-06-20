<?php
session_start();
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    // Utilizador não autenticado
    header("Location: ../Paginas/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio']) && is_numeric($_POST['id_anuncio'])) {
    $id_anuncio = (int) $_POST['id_anuncio'];
    $id_user = $_SESSION['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Só permite apagar se o anúncio for do utilizador autenticado
    $query = "DELETE FROM anuncios WHERE id_anuncio = ? AND ref_user = ?";

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'ii', $id_anuncio, $id_user);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../Paginas/meus_anuncios.php?msg=anuncio_eliminado");
            exit();
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
    header("Location: ../Paginas/meus_anuncios.php?erro=nao_foi_possivel_eliminar");
    exit();

} else {
    // Requisição inválida
    header("Location: ../Paginas/meus_anuncios.php?erro=dados_invalidos");
    exit();
}
