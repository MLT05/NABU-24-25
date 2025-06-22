<?php
require_once '../Connections/connection.php';
include_once 'function_tempo.php'; // se precisares de converter data em texto no JS, podes ignorar isto

session_start();
if (!isset($_SESSION['id_user'])) {
    http_response_code(403);
    exit();
}

$id_user = $_SESSION['id_user'];

$link = new_db_connection();
$stmt = mysqli_stmt_init($link);

$query = "
SELECT 
    m1.ref_produto,
    a.nome_produto,
    a.capa,
    u.id_user AS id_outro_user,
    u.nome AS nome_outro_user,
    u.pfp AS pfp_outro_user,
    m1.mensagem,
    m1.data_envio
FROM mensagens m1
JOIN (
    SELECT 
        MAX(id_mensagem) AS ultima_msg_id
    FROM mensagens
    WHERE ref_remetente = ? OR ref_destinatario = ?
    GROUP BY ref_produto,
             CASE 
                 WHEN ref_remetente = ? THEN ref_destinatario
                 ELSE ref_remetente
             END
) ultimas ON m1.id_mensagem = ultimas.ultima_msg_id
JOIN anuncios a ON a.id_anuncio = m1.ref_produto
JOIN users u ON u.id_user = 
    CASE 
        WHEN m1.ref_remetente = ? THEN m1.ref_destinatario
        ELSE m1.ref_remetente
    END
ORDER BY m1.data_envio DESC";

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, 'iiii', $id_user, $id_user, $id_user, $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id_produto, $nome_produto, $capa,
        $id_outro_user, $nome_outro_user, $pfp_outro_user,
        $mensagem, $data_envio);

    $conversas = [];
    while (mysqli_stmt_fetch($stmt)) {
        $conversas[] = [
            'id_produto' => $id_produto,
            'nome_produto' => $nome_produto,
            'capa' => $capa,
            'id_outro_user' => $id_outro_user,
            'nome_outro_user' => $nome_outro_user,
            'pfp_outro_user' => $pfp_outro_user,
            'mensagem' => $mensagem,
            'data_envio' => $data_envio,
        ];
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);

    header('Content-Type: application/json');
    echo json_encode($conversas);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erro ao preparar query']);
}

