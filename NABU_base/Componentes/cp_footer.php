


<footer class="footer-menu fixed-bottom verde_bg ">
    <div class="d-flex w-100 h-100">
        <a class="menu-item flex-fill text-center text-white text-decoration-none" href="../Paginas/index.php">
            <img src="../Imagens/icons/home_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Início" class="menu-icon">
            <p class="menu-label">Início</p>
        </a>
        <a class="menu-item flex-fill text-center text-white text-decoration-none" href="../Paginas/pesquisa.php">
            <img src="../Imagens/icons/search_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Pesquisa" class="menu-icon ">
            <p class="menu-label">Pesquisa</p>
        </a>
        <a class="menu-item flex-fill text-center text-white text-decoration-none" href="../Paginas/add_produto.php">
            <img src="../Imagens/icons/add_circle_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Vender" class="menu-icon">
            <p class="menu-label">Vender</p>
        </a>
        <a class="menu-item flex-fill text-center text-white text-decoration-none" href="../Paginas/mensagens.php">
            <img src="../Imagens/icons/chat_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Mensagens" class="menu-icon">
            <p class="menu-label">Mensagens</p>
        </a>
        <a class="menu-item flex-fill text-center text-white text-decoration-none position-relative" href="../Paginas/perfil.php">
            <div class="position-relative">
            <?php


            if (isset($_SESSION['id_user'])) {
                require_once '../Connections/connection.php';
                $link = new_db_connection();
                $stmt = mysqli_stmt_init($link);
                $query = "SELECT pfp FROM users WHERE id_user = ?";
                if (mysqli_stmt_prepare($stmt, $query)) {
                    mysqli_stmt_bind_param($stmt, "i", $_SESSION['id_user']);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_bind_result($stmt, $pfp_db);
                    mysqli_stmt_fetch($stmt);
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);

                    if (!empty($pfp_db)) {
                        $pfp_path = '../uploads/pfp/' . $pfp_db;
                        echo '<img src="' . htmlspecialchars($pfp_path) . '" alt="Perfil" class="menu-icon rounded-circle" style=" object-fit:cover;">';
                    } else {
                        // Se não tem foto, mostrar o icon padrão
                        echo '<img src="../Imagens/icons/person_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Perfil" class="menu-icon">';
                    }
                } else {
                    mysqli_close($link);
                    // Em caso de erro, mostrar icon padrão
                    echo '<img src="../Imagens/icons/person_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Perfil" class="menu-icon">';
                }
            } else {
                // Se não está logado, mostrar icon padrão
                echo '<img src="../Imagens/icons/person_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Perfil" class="menu-icon">';
            }
            ?>
                <span id="noti-badge-footer" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    0
                </span>
            </div>
            <p class="menu-label">Perfil</p>
        </a>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mostrar notificação desktop + toast na página
    function showNotification(title, body) {
        if ("Notification" in window && Notification.permission === "granted" && document.visibilityState !== 'visible') {
            new Notification(title, {
                body,
                icon: "../Imagens/icons/notifications_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg"
            });
        }
        showInPageToast(title, body);
    }

    function showInPageToast(title, body) {
        const toast = document.createElement("div");
        toast.className = "custom-toast show mt-5";
        toast.style.position = "fixed";
        toast.style.top = "1rem";
        toast.style.right = "1rem";
        toast.style.zIndex = "9999";

        toast.innerHTML = `
        <div class="d-flex align-items-center p-3 rounded shadow verde_claro_bg">
            <img src="../Imagens/app/NABU-LOGO.png" style="width: 24px; margin-right: 10px;">
            <div class="toast-body">
                <strong>${title}</strong><br>${body}
            </div>
            <button type="button" class="btn-close btn-close-white ms-3" onclick="this.parentElement.parentElement.remove()"></button>
        </div>
    `;

        toast.addEventListener("click", (e) => {
            if (!e.target.classList.contains("btn-close")) {
                window.location.href = "../Paginas/notificacoes.php";
            }
        });

        document.body.appendChild(toast);
        //setTimeout(() => toast.remove(), 6000);
    }

    // Pedir permissão para notificações push (se ainda não tiver)
    if ("Notification" in window && Notification.permission !== "granted") {
        Notification.requestPermission();
    }

    // Buscar notificações novas e mostrar (via AJAX, sem marcar como lidas)
    function buscarNotificacoes() {
        fetch('../Functions/ajax_buscar_notificacoes.php')
            .then(r => r.json())
            .then(notificacoes => {
                let mostradas = sessionStorage.getItem('notificacoesMostradas');
                mostradas = mostradas ? JSON.parse(mostradas) : [];

                let novas = 0;

                notificacoes.forEach(n => {
                    if (!mostradas.includes(n.id_notificacao)) {
                        showNotification("Nova Notificação", n.conteudo);
                        mostradas.push(n.id_notificacao);
                        novas++;
                    }
                });

                sessionStorage.setItem('notificacoesMostradas', JSON.stringify(mostradas));

                // Atualizar badge com o total no servidor, mesmo que novas = 0
                atualizarBadgeServidor();
            })
            .catch(console.error);
    }


    // Atualizar o badge das notificações não lidas
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
    function atualizarBadgeServidor() {
        fetch('../Functions/ajax_contar_notificacoes.php')
            .then(r => r.json())
            .then(data => {
                if (data.status === 'sucesso') {
                    atualizarBadgesValor(data.quantidade);
                }
            })
            .catch(e => console.error("Erro ao contar notificações:", e));
    }
    function atualizarBadgesValor(quantidade) {
        const badgeFooter = document.getElementById('noti-badge-footer');
        const badgePerfil = document.getElementById('noti-badge-perfil');

        [badgeFooter, badgePerfil].forEach(badge => {
            if (badge) {
                badge.textContent = quantidade;
                if (quantidade > 0) {
                    badge.classList.remove('d-none');
                } else {
                    badge.classList.add('d-none');
                }
            }
        });
    }



    // Executar buscar notificações e atualizar badge imediatamente e depois a cada 15s
    document.addEventListener('DOMContentLoaded', () => {
        buscarNotificacoes(); // mostra novas e atualiza badge com todas
        setInterval(buscarNotificacoes, 15000); // repetir de 15 em 15 seg.
    });
</script>


</body>
</html>
