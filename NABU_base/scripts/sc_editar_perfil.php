<?php
session_start();
require_once '../connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit;
}

if (!isset($_POST['nome'], $_POST['email'], $_POST['telefone'])) {
    header("Location: ../Paginas/perfil_details.php?msg=campos_vazios");
    exit;
}

$nome = $_POST['nome'];
$email = $_POST['email'];
$contacto = $_POST['telefone'];
$id_user = $_SESSION['id_user'];

$link = new_db_connection();
$query = "UPDATE users SET nome = ?, email = ?, contacto = ? WHERE id_user = ?";

$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "sssi", $nome, $email, $contacto, $id_user);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../Paginas/perfil_details.php?msg=sucesso");
    exit;
} else {
    header("Location: ../Paginas/perfil_details.php?msg=erro_bd");
    exit;
}
?>