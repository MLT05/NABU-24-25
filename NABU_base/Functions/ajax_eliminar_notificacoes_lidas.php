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
    // Apagar notificações já marcadas como lidas
    $stmt = mysqli_prepare($link, "DELETE FROM notificacoes WHERE users_id_user = ? AND lida = 1");
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    $rowsDeleted = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);

    mysqli_close($link);

    echo json_encode([
        'status' => 'sucesso',
        'mensagem' => "Notificações eliminadas com sucesso.",
        'apagadas' => $rowsDeleted
    ]);
} catch (mysqli_sql_exception $e) {
    echo json_encode(['status' => 'erro', 'mensagem' => $e->getMessage()]);
    exit;
}

