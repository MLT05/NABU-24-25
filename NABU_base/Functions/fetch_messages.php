<?php
session_start();
require_once '../Connections/connection.php';
$link = new_db_connection();

if (!isset($_SESSION['id_user']) || !isset($_GET['partner_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Acesso negado']);
    exit;
}

$my_id = $_SESSION['id_user'];
$partner_id = $_GET['partner_id'];

$query = "SELECT id_mensagem, ref_remetente, mensagem, data_envio, message_type
          FROM mensagens
          WHERE (ref_remetente = ? AND ref_destinatario = ?) 
             OR (ref_remetente = ? AND ref_destinatario = ?)
          ORDER BY timestamp ASC";


$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "iiii", $my_id, $partner_id, $partner_id, $my_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

$messages = [];
while ($row = mysqli_fetch_assoc($result)) {
    $messages[] = $row;
}
mysqli_stmt_close($stmt);

// Marca as mensagens como lidas
$query_update = "UPDATE mensagens SET is_read = 1 WHERE ref_remetente = ? AND ref_destinatario = ? AND is_read = 0";
$stmt_update = mysqli_prepare($link, $query_update);
mysqli_stmt_bind_param($stmt_update, "ii", $partner_id, $my_id);
mysqli_stmt_execute($stmt_update);
mysqli_stmt_close($stmt_update);

header('Content-Type: application/json');
echo json_encode($messages);

mysqli_close($link);

