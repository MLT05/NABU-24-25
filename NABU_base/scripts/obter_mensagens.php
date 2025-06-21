<?php
session_start();
require_once '../Connections/connection.php';
$conn = new_db_connection();
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;
$ref_produto = (int) (isset($_GET['ref_produto']) ? $_GET['ref_produto'] : 0);
$outro_usuario = (int) (isset($_GET['outro_usuario']) ? $_GET['outro_usuario'] : 0);

if (!$ref_produto || !$outro_usuario) {
    echo json_encode([]);
    exit;
}

$sql = "SELECT m.id_mensagem, m.mensagem, m.data_envio, m.is_read, m.ref_remetente, u.nome AS remetente_nome 
        FROM mensagens m 
        JOIN users u ON m.ref_remetente = u.id_user 
        WHERE m.ref_produto = ? 
          AND ((m.ref_remetente = ? AND m.ref_destinatario = ?) OR (m.ref_remetente = ? AND m.ref_destinatario = ?))
        ORDER BY m.data_envio ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiiii", $ref_produto, $user_id, $outro_usuario, $outro_usuario, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$mensagens = [];
while ($row = $result->fetch_assoc()) {
    $mensagens[] = $row;
}

echo json_encode($mensagens);
?>
