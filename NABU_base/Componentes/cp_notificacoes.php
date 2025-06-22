<?php
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Buscar notificações
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

    <hr class="mb-2">
    <div id="notificacoes-container" class="d-flex flex-column gap-2 mt-2">
        <?php foreach ($notificacoes as $noti): ?>
            <div
                    class="notificacao p-3 border rounded verde_claro_bg <?= ($noti['lida'] == 0 ? 'bg-warning-subtle' : '') ?>"
                    data-id="<?= $noti['id'] ?>"
                    style="cursor: pointer"
            >
                <div class="d-flex justify-content-between">
                    <div class="noti-conteudo text-truncate-custom"><?= htmlspecialchars($noti['conteudo']) ?></div>
                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($noti['data'])) ?></small>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
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
    // Função para ativar clique que expande/colapsa o conteúdo da notificação
    // e marca a notificação como lida ao clicar
    function ativarCliqueNasNotificacoes() {
        document.querySelectorAll('.notificacao').forEach(item => {
            const conteudo = item.querySelector('.noti-conteudo');
            if (!conteudo) return;

            item.addEventListener('click', () => {
                conteudo.classList.toggle('expandido');

                const idNotificacao = item.getAttribute('data-id');
                if (!idNotificacao) return;

                if (!item.classList.contains('bg-warning-subtle')) return;

                fetch('../Functions/ajax_marcar_notificacoes_lidas.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id_notificacao: idNotificacao })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.status === 'sucesso') {
                            item.classList.remove('bg-warning-subtle');
                            atualizarBadge();
                        }
                    })
                    .catch(console.error);
            });
        });
    }

    // Atualizar badge das notificações não lidas
    function atualizarBadge() {
        fetch('../Functions/ajax_contar_notificacoes.php')
            .then(r => r.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    const badgeFooter = document.getElementById('noti-badge-footer');
                    const badgePerfil = document.getElementById('noti-badge-perfil');

                    [badgeFooter, badgePerfil].forEach(badge => {
                        if (badge) {
                            badge.textContent = data.quantidade;
                            if (data.quantidade > 0) {
                                badge.classList.remove('d-none');
                            } else {
                                badge.classList.add('d-none');
                            }
                        }
                    });
                }
            })
            .catch(e => console.error("Erro ao buscar badge:", e));
    }

    // Função para atualizar a lista via AJAX (sem marcar todas como lidas)
    function atualizarListaNotificacoes() {
        fetch('../Functions/ajax_buscar_notificacoes.php')
            .then(res => res.json())
            .then(notificacoes => {
                const lista = document.querySelector('ul.list-group');
                lista.innerHTML = ''; // limpa lista

                if (notificacoes.length === 0) {
                    lista.innerHTML = '<li class="list-group-item text-center text-muted">Sem notificações novas</li>';
                    atualizarBadge();
                    return;
                }

                notificacoes.forEach(noti => {
                    const li = document.createElement('li');
                    // Adiciona o data-id para identificar a notificação
                    // Aplica a classe 'list-group-item-warning' só se a notificação não estiver lida (lida === 0)
                    li.className = `my-1 py-2 px-3 border rounded verde_claro_bg ${noti.lida == 0 ? 'list-group-item-warning' : ''}`;
                    li.setAttribute('data-id', noti.id_notificacao || noti.id);
                    li.innerHTML = `
                        <div class="d-flex justify-content-between">
                            <span class="noti-conteudo">${noti.conteudo}</span>
                            <small class="text-muted">${new Date(noti.data).toLocaleString('pt-PT')}</small>
                        </div>
                    `;
                    lista.appendChild(li);
                });

                ativarCliqueNasNotificacoes();
                atualizarBadge();
            })
            .catch(console.error);
    }

    document.addEventListener('DOMContentLoaded', () => {
        ativarCliqueNasNotificacoes();
        atualizarListaNotificacoes();
    });

    // Atualiza a lista e o badge a cada 15 segundos
    setInterval(atualizarListaNotificacoes, 15000);

    // Botão: Eliminar notificações lidas
    document.getElementById('btnLimparNotificacoes').addEventListener('click', () => {
        const confirmarModal = new bootstrap.Modal(document.getElementById('confirmarEliminarModal'));
        confirmarModal.show();

        const confirmarBtn = document.getElementById('confirmarEliminarBtn');

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

                    atualizarListaNotificacoes();
                })
                .catch(err => {
                    console.error("Erro AJAX:", err);
                    alert("Erro na comunicação com o servidor.");
                });

            confirmarModal.hide();
        });
    });
</script>


