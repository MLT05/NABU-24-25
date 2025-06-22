<?php
session_start();
require_once '../Connections/connection.php';
$conn = new_db_connection();
$mensagem = isset($_POST['mensagem']) ? $_POST['mensagem'] : '';
$destinatario = (int) (isset($_POST['ref_destinatario']) ? $_POST['ref_destinatario'] : 0);
$ref_produto = (int) (isset($_POST['ref_produto']) ? $_POST['ref_produto'] : 0);
$remetente = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 1;
$data_envio = date("Y-m-d H:i:s");

$stmt = $conn->prepare("INSERT INTO mensagens (mensagem, data_envio, ref_remetente, ref_destinatario, is_read, message_type, ref_produto) VALUES (?, ?, ?, ?, 0, 'text', ?)");
$stmt->bind_param("ssiii", $mensagem, $data_envio, $remetente, $destinatario, $ref_produto);
$stmt->execute();
$stmt->close();

// Criar notificação
$conteudo = "Nova mensagem recebida";
$stmt2 = $conn->prepare("INSERT INTO notificacoes (conteudo, users_id_user, lida, data) VALUES (?, ?, 0, ?)");
$stmt2->bind_param("sis", $conteudo, $destinatario, $data_envio);
$stmt2->execute();
$stmt2->close();
?>
