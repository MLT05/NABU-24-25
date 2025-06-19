<?php
require_once '../Connections/connection.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$link = new_db_connection();
$stmt = mysqli_stmt_init($link);

$query = "SELECT id_notificacao, conteudo, data, lida FROM notificacoes WHERE users_id_user = ? ORDER BY data DESC";

$notificacoes = [];

if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_user']);
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

    // Marcar como lidas
    $update = mysqli_prepare($link, "UPDATE notificacoes SET lida = TRUE WHERE users_id_user = ?");
    mysqli_stmt_bind_param($update, "i", $_SESSION['id_user']);
    mysqli_stmt_execute($update);
    mysqli_stmt_close($update);
}

mysqli_close($link);
?>

<?php include "../Componentes/cp_head.php"; ?>
<?php include "../Componentes/cp_header.php"; ?>

<main class="container mt-5 pt-5">
    <h3 class="mb-3">Notificações</h3>

    <button id="btnTestNoti" class="btn btn-primary mb-4">Testar Notificação Push</button>
    <button id="btnAjaxNoti" class="btn btn-success mb-3">Criar notificação via AJAX</button>

    <ul class="list-group">
        <?php foreach ($notificacoes as $noti): ?>
            <li class="list-group-item <?= $noti['lida'] ? '' : 'list-group-item-warning' ?>">
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
    const notificacoesBD = <?= json_encode(array_filter($notificacoes, fn($n) => !$n['lida'])) ?>;

    function showNotification(title, body) {
        // Push notification (só aparece se estiver fora da aba ativa)
        if ("Notification" in window && Notification.permission === "granted" && document.visibilityState !== 'visible') {
            new Notification(title, {
                body: body,
                icon: "../Imagens/icons/notifications_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg"
            });
        }

        // In-page visual notification (ex: toast)
        showInPageToast(title, body);
    }

    // Exemplo de toast com Bootstrap (ou podes fazer algo personalizado)
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

        // Auto remove depois de 5 segundos
        setTimeout(() => {
            toast.remove();
        }, 5000);
    }

    // Permissão
    if ("Notification" in window && Notification.permission !== "granted") {
        Notification.requestPermission();
    }

    // Mostrar notificações
    notificacoesBD.forEach(n => {
        showNotification("Nova Notificação", n.conteudo);
    });

    // Botão de teste
    document.getElementById('btnTestNoti').addEventListener('click', () => {
        showNotification("Notificação de teste", "Isto é uma notificação de teste.");
    });

    document.getElementById('btnAjaxNoti').addEventListener('click', () => {
        const mensagem = "Notificação criada com AJAX às " + new Date().toLocaleTimeString();

        fetch('../Functions/ajax_adicionar_notificacao.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'mensagem=' + encodeURIComponent(mensagem)
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    showNotification("Nova Notificação", data.mensagem);
                } else {
                    alert("Erro: " + data.mensagem);
                }
            })
            .catch(err => {
                console.error("Erro na requisição:", err);
            });
    });
</script>
