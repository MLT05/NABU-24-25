<?php
session_start();
require_once '../Connections/connection.php';
$conn = new_db_connection();

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Usuário não logado']);
    exit;
}

$user_id = $_SESSION['id_user'];

// Buscar mensagens não lidas, com nome do remetente e nome do produto
$sql = "SELECT m.id_mensagem, m.mensagem, a.nome_produto, u.nome AS remetente_nome
        FROM mensagens m
        JOIN users u ON m.ref_remetente = u.id_user
        JOIN anuncios a ON m.ref_produto = a.id_anuncio
        WHERE m.ref_destinatario = ? AND m.is_read = 0
        ORDER BY m.data_envio ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$mensagens = [];
while ($row = $result->fetch_assoc()) {
    $mensagens[] = $row;
}

echo json_encode($mensagens);