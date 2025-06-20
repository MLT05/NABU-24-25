<?php
session_start();
require_once '../Connections/connection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_user'])) {
    echo json_encode(['status' => 'erro', 'mensagem' => 'Utilizador não autenticado']);
    exit;
}

$id_user = $_SESSION['id_user'];
$link = new_db_connection();

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    // 1. Marcar notificações não lidas como lidas
    $stmt = mysqli_prepare($link, "UPDATE notificacoes SET lida = 1 WHERE users_id_user = ? AND lida = 0");
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // 2. Apagar notificações lidas
    $stmt2 = mysqli_prepare($link, "DELETE FROM notificacoes WHERE users_id_user = ? AND lida = 1");
    mysqli_stmt_bind_param($stmt2, "i", $id_user);
    mysqli_stmt_execute($stmt2);
    $rowsDeleted = mysqli_stmt_affected_rows($stmt2);
    mysqli_stmt_close($stmt2);

    mysqli_close($link);

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => "Notificações marcadas como lidas e eliminadas com sucesso.",
        'apagadas' => $rowsDeleted
    ]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    exit;
}
