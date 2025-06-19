<?php
require_once '../Connections/connection.php';
session_start();

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

<main class="container mt-5 pt-5">
    <h3 class="mb-3">Notificações</h3>

    <button id="btnTestNoti" class="btn btn-primary mb-4">Testar Notificação Push</button>
    <a href="index.php" id="btnAjaxNoti" class="btn btn-success mb-3">Criar notificação via AJAX</a>
    <button id="btnLimparNotificacoes" class="btn btn-danger mb-3">Eliminar notificações lidas</button>

    <ul class="list-group">
        <?php foreach ($notificacoes as $noti): ?>
            <li class="list-group-item <?= (!empty($noti['lida']) && $noti['lida'] == 1) ? '' : 'list-group-item-warning' ?>">
                <div class="d-flex justify-content-between">
                    <span><?= htmlspecialchars($noti['conteudo']) ?></span>
                    <small class="text-muted"><?= date('d/m/Y H:i', strtotime($noti['data'])) ?></small>
                </div>
            </li>
        <?php endforeach; ?>
    </ul>
</main>

<?php include "../Componentes/cp_footer.php"; ?>

<script>
    // Mostrar notificação toast + push
    function showNotification(title, body) {
        if ("Notification" in window && Notification.permission === "granted" && document.visibilityState !== 'visible') {
            new Notification(title, {
                body: body,
                icon: "../Imagens/icons/notifications_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg"
            });
        }
        showInPageToast(title, body);
    }

    function showInPageToast(title, body) {
        const toast = document.createElement("div");
        toast.className = "toast align-items-center text-bg-primary border-0 show";
        toast.setAttribute("role", "alert");
        toast.setAttribute("aria-live", "assertive");
        toast.setAttribute("aria-atomic", "true");
        toast.style.position = "fixed";
        toast.style.bottom = "1rem";
        toast.style.right = "1rem";
        toast.style.zIndex = "9999";

        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong><br>${body}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        document.body.appendChild(toast);

        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    // Pedir permissão para notificações push se ainda não tiver
    if ("Notification" in window && Notification.permission !== "granted") {
        Notification.requestPermission();
    }

    // Buscar notificações (sem marcar como lidas aqui)
    function fetchNotificacoes() {
        fetch('../Functions/ajax_buscar_notificacoes.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sucesso' && Array.isArray(data.notificacoes)) {
                    // Obter notificações já mostradas da sessão
                    let mostradas = sessionStorage.getItem('notificacoesMostradas');
                    mostradas = mostradas ? JSON.parse(mostradas) : [];

                    data.notificacoes.forEach(n => {
                        if (!mostradas.includes(n.id_notificacao)) {
                            // Mostrar notificação
                            showNotification("Nova Notificação", n.conteudo);

                            // Guardar que mostramos esta notificação
                            mostradas.push(n.id_notificacao);
                        }
                    });

                    // Atualizar o sessionStorage
                    sessionStorage.setItem('notificacoesMostradas', JSON.stringify(mostradas));
                }
            })
            .catch(err => console.error("Erro ao buscar notificações:", err));
    }

    // Atualizar badge do número de notificações não lidas
    function atualizarBadge() {
        fetch('../Functions/ajax_contar_notificacoes.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    const badge = document.getElementById('noti-badge');
                    if (badge) {
                        badge.textContent = data.quantidade;
                        if (data.quantidade > 0) {
                            badge.classList.remove('d-none');
                        } else {
                            badge.classList.add('d-none');
                        }
                    }
                }
            })
            .catch(console.error);
    }
    document.getElementById('btnLimparNotificacoes').addEventListener('click', () => {
        if (confirm('Tens a certeza que queres eliminar todas as notificações lidas? Esta ação não pode ser desfeita.')) {
            fetch('../Functions/ajax_marcar_notificacoes_lidas.php', {
                method: 'POST'
            })
                .then(res => res.json())
                .then(data => {
                    if (data.status === 'sucesso') {
                        alert(data.mensagem + " Total eliminadas: " + data.apagadas);
                        location.reload(); // Atualiza a página para refletir alterações
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


    document.addEventListener('DOMContentLoaded', () => {
        fetchNotificacoes();

        // Marcar notificações como lidas ao entrar na página
        fetch('../Functions/ajax_marcar_notificacoes_lidas.php', { method: 'POST' })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    atualizarBadge(); // Atualizar badge após marcar lidas
                }
            })
            .catch(console.error);

        // Opcional: Atualizar notificações e badge a cada 15 segundos
        setInterval(() => {
            fetchNotificacoes();
            atualizarBadge();
        }, 15000);
    });
</script>
