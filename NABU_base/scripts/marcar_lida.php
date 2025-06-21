<?php
require_once '../Connections/connection.php';
$data = json_decode(file_get_contents("php://input"), true);
$id = (int) (isset($data['id']) ? $data['id'] : 0);
$conn = new_db_connection();
$stmt = $conn->prepare("UPDATE mensagens SET is_read = 1 WHERE id_mensagem = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->close();
?>
