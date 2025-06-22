<?php
require_once '../Connections/connection.php';
include_once '../Functions/function_tempo.php';
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
    mysqli_stmt_bind_result($stmt, $id, $conteudo, $data_envio, $lida);

    while (mysqli_stmt_fetch($stmt)) {
        $notificacoes[] = [
            'id' => $id,
            'conteudo' => $conteudo,
            'data' => $data_envio,
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
                    <span><?= htmlspecialchars($noti['conteudo']) ?></span>
                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($noti['data'])) ?></small>
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
    function tempoDecorrido(dataEnvio) {
        const data = new Date(dataEnvio);
        const agora = new Date();
        const diff = Math.floor((agora - data) / 1000); // em segundos

        if (diff < 60) return 'agora mesmo';
        if (diff < 3600) return `${Math.floor(diff / 60)} min`;
        if (diff < 86400) return `${Math.floor(diff / 3600)} h`;
        return data.toLocaleDateString();
    }
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
    function atualizarListaNotificacoes() {
        fetch('../Functions/ajax_buscar_notificacoes.php')
            .then(res => res.json())
            .then(notificacoes => {
                const lista = document.querySelector('ul.list-group');
                lista.innerHTML = ''; // limpa lista

                // Se quiser mostrar todas, inclusive lidas, teria que alterar o AJAX para buscar todas as notificações.
                // Aqui mostra só as não lidas, como o ajax_buscar_notificacoes.php faz

                if (notificacoes.length === 0) {
                    lista.innerHTML = '<li class="list-group-item text-center text-muted">Sem notificações novas</li>';
                    return;
                }

                notificacoes.forEach(noti => {
                    const li = document.createElement('li');
                    li.className = 'my-1 py-2 px-3 border rounded list-group-item-warning verde_claro_bg';
                    li.innerHTML = `
                    <div class="d-flex justify-content-between">
                        <span>${noti.conteudo}</span>
                        <small class="text-muted">${new Date(noti.data).toLocaleString('pt-PT')}</small>
                    </div>
                `;
                    lista.appendChild(li);
                });
            })
            .catch(console.error);
    }

    // Atualiza a lista a cada 15 segundos (ou outro intervalo desejado)
    setInterval(atualizarListaNotificacoes, 15000);

    // Também executa quando carrega a página para mostrar já as notificações atuais
    document.addEventListener('DOMContentLoaded', atualizarListaNotificacoes);

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