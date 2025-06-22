<?php
require_once '../Connections/connection.php';
include_once '../Functions/function_tempo.php';
session_start();

if (!isset($_SESSION['id_user']) || !isset($_GET['id_anuncio']) || !isset($_GET['id_outro_user'])) {
    http_response_code(400);
    echo json_encode(["error" => "Parâmetros em falta."]);
    exit();
}

$id_user = $_SESSION['id_user'];
$id_anuncio = (int) $_GET['id_anuncio'];
$id_outro_user = (int) $_GET['id_outro_user'];

$link = new_db_connection();
$stmt = mysqli_stmt_init($link);

$query = "SELECT ref_remetente, mensagem, data_envio 
          FROM mensagens 
          WHERE ref_produto = ? AND 
                ((ref_remetente = ? AND ref_destinatario = ?) OR 
                 (ref_remetente = ? AND ref_destinatario = ?))
          ORDER BY data_envio ASC";

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, "iiiii", $id_anuncio, $id_user, $id_outro_user, $id_outro_user, $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $remetente, $mensagem, $data);

    $mensagens = [];
    while (mysqli_stmt_fetch($stmt)) {
        $mensagens[] = [
            'remetente' => $remetente,
            'mensagem' => $mensagem,
            'data' => $data
        ];
    }

    echo json_encode($mensagens);
} else {
    echo json_encode(["error" => "Erro na query."]);
}
?>

