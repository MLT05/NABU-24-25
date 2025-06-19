<?php
session_start();

if (!isset($_SESSION['id_user']) || !isset($_POST['mensagem'])) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Dados em falta']);
    exit();
}

require_once '../Connections/connection.php';

$link = new_db_connection();
$stmt = mysqli_prepare($link, "INSERT INTO notificacoes (conteudo, users_id_user) VALUES (?, ?)");
mysqli_stmt_bind_param($stmt, "si", $_POST['mensagem'], $_SESSION['id_user']);

if (mysqli_stmt_execute($stmt)) {
    echo json_encode([
        'status' => 'sucesso',
        'id' => mysqli_insert_id($link),
        'mensagem' => $_POST['mensagem'],
        'data' => date('Y-m-d H:i:s')
    ]);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao inserir']);
}

mysqli_stmt_close($stmt);
mysqli_close($link);