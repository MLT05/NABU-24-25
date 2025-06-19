<?php
function adicionar_notificacao($id_user, $mensagem) {
    require_once '../Connections/connection.php';
    $link = new_db_connection();

    $stmt = mysqli_prepare($link, "INSERT INTO notificacoes (conteudo, users_id_user) VALUES (?, ?)");
    mysqli_stmt_bind_param($stmt, "si", $mensagem, $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($link);
}