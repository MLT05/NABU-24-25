<?php
function criarNotificacao($id_user, $titulo, $corpo) {
    require_once '../Connections/connection.php';

    // Montar o conteúdo da notificação combinando título e corpo
    $conteudo = $titulo . ': ' . $corpo;

    $link = new_db_connection();
    $stmt = mysqli_prepare($link, "INSERT INTO notificacoes (conteudo, users_id_user) VALUES (?, ?)");

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "si", $conteudo, $id_user);
        $executou = mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($link);

        return $executou;
    } else {
        mysqli_close($link);
        return false;
    }
}
//$id_user = 123; // ID do usuário que vai receber a notificação
//$titulo = "Nova Mensagem";
//$corpo = "Você recebeu uma mensagem de João.";
//
//$sucesso = criarNotificacao($id_user, $titulo, $corpo);
//
//if ($sucesso) {
//    echo "Notificação criada com sucesso!";
//} else {
//    echo "Erro ao criar notificação.";
//}