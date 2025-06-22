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

<main class="body_index">

    <div class="d-flex justify-content-between align-items-center mb-3 me-3">
        <h3 class="mb-0">Notificações</h3>
        <button id="btnLimparNotificacoes" class="border-0 bg-transparent p-0 m-0" title="Eliminar notificações lidas">
            <span class="material-symbols-outlined text-danger">delete</span>
        </button>
    </div>

    <div class="d-none">
        <button id="btnTestNoti" class="btn btn-primary mb-4">Testar Notificação Push</button>
        <a href="index.php" id="btnAjaxNoti" class="btn btn-success mb-3">Criar notificação via AJAX</a>
    </div>

    <hr class="mb-2">
    <ul class="list-group mt-2">
        <?php foreach ($notificacoes as $noti): ?>
            <li class="my-1 py-2 px-3 border rounded <?= (!empty($noti['lida']) && $noti['lida'] == 1) ? '' : 'list-group-item-warning' ?> verde_claro_bg">
                <div class="d-flex justify-content-between">
                    <div class="noti-conteudo text-truncate-custom"><?= htmlspecialchars($noti['conteudo']) ?></div>
                    <small class="text-muted text-end"><?= date('d/m/Y H:i', strtotime($noti['data'])) ?></small>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</main>
<!-- Modal de Confirmação -->
<div class="modal fade" id="confirmarEliminarModal" tabindex="-1" aria-labelledby="confirmarEliminarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmarEliminarModalLabel">Confirmar Eliminação</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body">
                Tens a certeza que queres eliminar todas as notificações lidas? Esta ação não pode ser desfeita.
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button id="confirmarEliminarBtn" type="button" class="btn btn-danger">Eliminar</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Sucesso -->
<div class="modal fade" id="sucessoEliminarModal" tabindex="-1" aria-labelledby="sucessoEliminarModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-header">
                <h5 class="modal-title" id="sucessoEliminarModalLabel">Notificações Eliminadas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
            </div>
            <div class="modal-body" id="sucessoEliminarMensagem">
                <!-- Mensagem de sucesso será inserida aqui via JS -->
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-success" data-bs-dismiss="modal" onclick="location.reload()">Ok</button>
            </div>
        </div>
    </div>
</div>



<script>
        document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('ul.list-group > li').forEach(item => {
            const conteudo = item.querySelector('.noti-conteudo');
            if (!conteudo) return;

            item.style.cursor = 'pointer';

            item.addEventListener('click', () => {
                conteudo.classList.toggle('expandido');
            });
        });
    });

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
        const confirmarModal = new bootstrap.Modal(document.getElementById('confirmarEliminarModal'));
        confirmarModal.show();

        const confirmarBtn = document.getElementById('confirmarEliminarBtn');

        // Remove event listeners anteriores para evitar múltiplas chamadas
        confirmarBtn.replaceWith(confirmarBtn.cloneNode(true));
        document.getElementById('confirmarEliminarBtn').addEventListener('click', () => {
            fetch('../Functions/ajax_eliminar_notificacoes_lidas.php', { method: 'POST' })
                .then(res => res.json())
                .then(data => {
                    const sucessoModal = new bootstrap.Modal(document.getElementById('sucessoEliminarModal'));
                    document.getElementById('sucessoEliminarMensagem').innerText =
                        (data.status === 'sucesso')
                            ? `${data.mensagem} Total eliminadas: ${data.apagadas}`
                            : `Erro: ${data.mensagem || "Erro desconhecido"}`;
                    sucessoModal.show();
                })
                .catch(err => {
                    console.error("Erro AJAX:", err);
                    alert("Erro na comunicação com o servidor.");
                });

            // Fecha o modal de confirmação
            confirmarModal.hide();
        });
    });
</script>