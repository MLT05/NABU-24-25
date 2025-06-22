<?php
session_start(); // <- Isto é obrigatório para usar $_SESSION

require_once '../Connections/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {

    echo json_encode(['status' => 'erro', 'mensagem' => 'Utilizador não autenticado']);
    exit();
}

$id_user = $_SESSION['id_user'];
$link = new_db_connection();

$stmt = mysqli_prepare($link, "SELECT COUNT(*) FROM notificacoes WHERE users_id_user = ? AND lida = FALSE");
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $count);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
mysqli_close($link);

echo json_encode(['status' => 'sucesso', 'quantidade' => $count]);
