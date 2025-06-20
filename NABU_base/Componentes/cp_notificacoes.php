<?php
require_once '../Connections/connection.php';

// Redirecionar se não estiver logado
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}


$id_user = $_SESSION['id_user'];


// Criar ligação
$link = new_db_connection();
$stmt = mysqli_stmt_init($link);

$notificacoes = [];

$query = "SELECT id_notificacao, conteudo, data, lida FROM notificacoes WHERE users_id_user = ? ORDER BY data DESC";

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $id, $conteudo, $data, $lida);

    while (mysqli_stmt_fetch($stmt)) {
        $notificacoes[] = [
            'id' => $id,
            'conteudo' => $conteudo,
            'data' => $data,
            'lida' => $lida
        ];
    }

    mysqli_stmt_close($stmt);
}
mysqli_close($link);
?>

<?php include "../Componentes/cp_head.php"; ?>
<?php include "../Componentes/cp_header.php"; ?>

<main class="body_index">
    <h3 class="mb-3 me-3 text-end">Notificações</h3>
    <div class="d-none">
        <button id="btnTestNoti" class="btn btn-primary mb-4">Testar Notificação Push</button>
        <a href="index.php" id="btnAjaxNoti" class="btn btn-success mb-3">Criar notificação via AJAX</a>
        <button id="btnLimparNotificacoes" class="btn btn-danger mb-3">Eliminar notificações lidas</button>
    </div>
    <hr class="mb-2">
    <ul class="list-group mt-2">

        <?php foreach ($notificacoes as $noti): ?>

            <li class="my-1 py-2 px-3 border rounded <?= (!empty($noti['lida']) && $noti['lida'] == 1) ? '' : 'list-group-item-warning' ?> verde_claro_bg">
                <div class="d-flex justify-content-between">
                    <span><?= htmlspecialchars($noti['conteudo']) ?></span>
                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($noti['data'])) ?></small>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</main>




<script>
    // Marcar notificações como lidas ao entrar na página
    document.addEventListener('DOMContentLoaded', () => {
        fetch('../Functions/ajax_marcar_notificacoes_lidas.php', { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    atualizarBadge(); // já definida no footer
                }
            })
            .catch(console.error);
    });

    // Botão: Eliminar notificações lidas
    document.getElementById('btnLimparNotificacoes').addEventListener('click', () => {
        if (confirm('Tens a certeza que queres eliminar todas as notificações lidas? Esta ação não pode ser desfeita.')) {
            fetch('../Functions/ajax_eliminar_notificacoes_lidas.php', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'sucesso') {
                        alert(data.mensagem + " Total eliminadas: " + data.apagadas);
                        location.reload();
                    } else {
                        alert("Erro: " + (data.mensagem || "Erro desconhecido"));
                    }
                })
                .catch(err => {
                    console.error("Erro AJAX:", err);
                    alert("Erro na comunicação com o servidor.");
                });
        }
    });
</script>