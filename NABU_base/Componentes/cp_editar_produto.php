<?php
require_once '../Connections/connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] != 1) {
    header("Location: produtos.php");
    exit;
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: produtos.php");
    exit;
}
$id_user = $_GET['id'];
$link = new_db_connection();

$stmt = mysqli_stmt_init($link);
$query = "SELECT titulo, sinopse, ano, url_imdb, url_trailer, ref_generos, capa FROM filmes WHERE id_filmes = ?";
mysqli_stmt_prepare($stmt, $query);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $titulo, $sinopse, $ano, $url_imdb, $url_trailer, $ref_generos, $capa);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);