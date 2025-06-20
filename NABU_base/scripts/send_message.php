<?php
session_start();
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user']) || $_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['receiver_id'])) {
    http_response_code(403);
    exit();
}

$link = new_db_connection();
$sender_id = $_SESSION['id_user'];
$sender_username = $_SESSION['username'];
$receiver_id = $_POST['receiver_id'];
$message_content = trim(isset($_POST['message_content']) ? $_POST['message_content'] : '');
$message_type = 'text';

if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
} elseif (isset($_FILES['audio_file']) && $_FILES['audio_file']['error'] == 0) {
}

if (empty($message_content)) {
    exit();
}

mysqli_begin_transaction($link);
try {


    $query_msg = "INSERT INTO mensagens (ref_remetente, ref_destinatario, mensagem, message_type) VALUES (?, ?, ?, ?)";
    $stmt_msg = mysqli_prepare($link, $query_msg);
    mysqli_stmt_bind_param($stmt_msg, "iiss", $sender_id, $receiver_id, $message_content, $message_type);
    mysqli_stmt_execute($stmt_msg);
    mysqli_stmt_close($stmt_msg);

    $notification_content = "Tem uma nova mensagem de " . htmlspecialchars($sender_username) . ".";
    $notification_type = 'message';
    $related_id = $sender_id;

    /*
    $query_notif = "INSERT INTO notificacoes (user_id, type, conteudo, related_id) VALUES (?, ?, ?, ?)";
    $stmt_notif = mysqli_prepare($link, $query_notif);
    mysqli_stmt_bind_param($stmt_notif, "issi", $receiver_id, $notification_type, $notification_content, $related_id);
    mysqli_stmt_execute($stmt_notif);
    mysqli_stmt_close($stmt_notif);
    */

    mysqli_commit($link);
    header('Content-Type: application/json');
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    mysqli_rollback($link);
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Falha ao guardar os dados.']);
} finally {
    mysqli_close($link);
}

