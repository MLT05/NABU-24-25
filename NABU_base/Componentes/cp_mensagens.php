<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
require_once '../Connections/connection.php';
include_once '../Functions/function_tempo.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
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
    ?>
    <main class="body_index">
        <h3 class="mb-3 me-3">Mensagens</h3>
        <hr>
        <?php
        while (mysqli_stmt_fetch($stmt)) {
            ?>
            <form action="mensagens_details.php" method="POST" class="mb-3">
                <input type="hidden" name="id_anuncio" value="<?= htmlspecialchars($id_produto) ?>">
                <input type="hidden" name="id_outro_user" value="<?= htmlspecialchars($id_outro_user) ?>">

                <button type="submit" class="w-100 text-decoration-none text-reset border-0 verde_claro_bg p-0">
                    <div class="d-flex justify-content-between align-items-center p-3">
                        <div class="d-flex align-items-center">
                            <img src="../uploads/pfp/<?= htmlspecialchars($pfp_outro_user) ?>"
                                 class="rounded-circle me-3"
                                 style="width: 50px; height: 50px; object-fit: cover;"
                                 alt="Foto perfil">
                            <div>
                                <h6 class="mb-0 fw-bold text-start verde_escuro opacity-75"><?= htmlspecialchars($nome_outro_user) ?></h6>
                                <div class="text-start"><?= htmlspecialchars($nome_produto) ?></div>
                            </div>
                        </div>
                        <small class="text-muted"><?= tempoDecorrido($data_envio) ?></small>
                    </div>
                </button>
            </form>
            <?php
        }
        ?>
    </main>
    <?php
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    echo "Erro ao preparar statement.";
}
?>
