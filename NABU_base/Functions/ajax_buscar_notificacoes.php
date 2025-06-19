<?php
session_start();
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    echo json_encode([]);
    exit;
}

$link = new_db_connection();
$stmt = mysqli_stmt_init($link);

$query = "SELECT id_notificacao, conteudo, data FROM notificacoes WHERE users_id_user = ? AND lida = FALSE ORDER BY data DESC";
$notificacoes = [];

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_user']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $conteudo, $data);

    while (mysqli_stmt_fetch($stmt)) {
        $notificacoes[] = [
            'id_notificacao' => $id, // ← nome consistente com o JS
            'conteudo' => $conteudo,
            'data' => $data
        ];
    }
    mysqli_stmt_close($stmt);
}
mysqli_close($link);

echo json_encode($notificacoes);
?>
