<?php
require_once '../Connections/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_encomenda'])) {
    $id_encomenda = $_POST['id_encomenda'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "UPDATE encomendas SET ref_estado = 3 WHERE id_encomenda = ?";

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_encomenda);
        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true, 'novo_estado' => 'Entregue']);
        } else {
            echo json_encode(['success' => false, 'error' => 'Falha na execução da query']);
        }
        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'error' => 'Erro na preparação da query']);
    }

    mysqli_close($link);
} else {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
}

