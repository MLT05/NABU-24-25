<?php
require_once '../Connections/connection.php';
session_start();

header('Content-Type: application/json');

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['id_user'])) {
    http_response_code(401);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Utilizador não autenticado']);
    exit();
}

// Verificar se a mensagem foi enviada
if (!isset($_POST['mensagem']) || empty(trim($_POST['mensagem']))) {
    http_response_code(400);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Mensagem não fornecida']);
    exit();
}

$id_user = $_SESSION['id_user'];
$mensagem = trim($_POST['mensagem']);

// Inserir notificação na BD
$link = new_db_connection();
$stmt = mysqli_prepare($link, "INSERT INTO notificacoes (conteudo, users_id_user) VALUES (?, ?)");

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "si", $mensagem, $id_user);
    if (mysqli_stmt_execute($stmt)) {
        echo json_encode(['status' => 'sucesso']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'erro', 'mensagem' => 'Erro ao inserir']);
    }
    mysqli_stmt_close($stmt);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'erro', 'mensagem' => 'Erro na query']);
}

mysqli_close($link);
