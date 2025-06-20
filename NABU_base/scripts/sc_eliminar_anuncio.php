<?php


session_start();
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
header("Location: ../Paginas/login.php");
exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio']) && is_numeric($_POST['id_anuncio'])) {
$id_anuncio = (int) $_POST['id_anuncio'];
$id_user = $_SESSION['id_user'];

$link = new_db_connection();

// Primeiro, buscar o nome da imagem (capa)
$query_select = "SELECT capa FROM anuncios WHERE id_anuncio = ? AND ref_user = ?";
$stmt_select = mysqli_prepare($link, $query_select);
mysqli_stmt_bind_param($stmt_select, 'ii', $id_anuncio, $id_user);
mysqli_stmt_execute($stmt_select);
mysqli_stmt_bind_result($stmt_select, $capa);
mysqli_stmt_fetch($stmt_select);
mysqli_stmt_close($stmt_select);

// Caminho da imagem
$upload_dir = "../uploads/capas/";
$default_image = "default-image.jpg";

// Eliminar a imagem se não for a default
if ($capa && $capa !== $default_image && file_exists($upload_dir . $capa)) {
unlink($upload_dir . $capa);
}

// Agora apagar o anúncio
$query = "DELETE FROM anuncios WHERE id_anuncio = ? AND ref_user = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, 'ii', $id_anuncio, $id_user);

if (mysqli_stmt_execute($stmt)) {
mysqli_stmt_close($stmt);
mysqli_close($link);
header("Location: ../Paginas/meus_anuncios.php?msg=anuncio_eliminado");
exit();
}

mysqli_stmt_close($stmt);
mysqli_close($link);
header("Location: ../Paginas/meus_anuncios.php?erro=nao_foi_possivel_eliminar");
exit();
} else {
header("Location: ../Paginas/meus_anuncios.php?erro=dados_invalidos");
exit();
}

?>