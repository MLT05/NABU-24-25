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
                        ?>  <span id="noti-badge-footer" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    0
                </span> <?php
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

            </div>
            <p class="menu-label">Perfil</p>
        </a>
    </div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Mostrar notificação desktop + toast na página
    function showNotification(title, body, mostrarToast = true) {
        console.log('showNotification chamada:', title);
        if ("Notification" in window && Notification.permission === "granted" && document.visibilityState !== 'visible') {
            console.log('Mostrando notificação desktop.');
            new Notification(title, {
                body: body,
                badge: "../Imagens/app/NABU-LOGO.png",
                vibrate: [200, 100, 200],
                tag: "notificacao-1",
                renotify: true,
                timestamp: Date.now()
            });
        } else {
            console.log('Permissão para notificações desktop não concedida ou página visível.');
        }
        if (mostrarToast) {
            showInPageToast(title, body);
        }
    }

    function showInPageToast(title, body) {
        console.log('showInPageToast chamada:', title);
        const toast = document.createElement("div");
        toast.className = "custom-toast show mt-5";
        toast.style.position = "fixed";
        toast.style.top = "1rem";
        toast.style.right = "1rem";
        toast.style.zIndex = "9999";

        toast.innerHTML = `
          <div class="d-flex align-items-center p-3 rounded shadow verde_claro_bg" style="max-width: 90vw;">
            <img src="../Imagens/app/NABU-LOGO.png" style="width: 24px; margin-right: 10px;">
            <div class="toast-body" style="word-break: break-word; overflow-wrap: break-word; white-space: normal; max-width: 100%;">
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
        setTimeout(() => toast.remove(), 6000);
    }

    if ("Notification" in window && Notification.permission !== "granted") {
        Notification.requestPermission().then(() => {
            console.log('Permissão para notificações solicitada');
        });
    }

    let mensagensNotificadas = new Set(
        JSON.parse(sessionStorage.getItem('mensagensNotificadas') || '[]')
    );

    // Função para criar a notificação na BD via ajax_adicionar_notificacao.php
    function criarNotificacaoBD(mensagem) {
        console.log('Criando notificação na BD com mensagem:', mensagem);
        return fetch('../Functions/ajax_adicionar_notificacao.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({ mensagem })
        })
            .then(r => {
                console.log('Resposta recebida do servidor para criação da notificação:', r.status);
                return r.json();
            })
            .then(res => {
                console.log('Resposta JSON da criação da notificação:', res);
                if (res.status === 'sucesso') {
                    console.log('Notificação criada na BD com sucesso.');
                    return true;
                } else {
                    console.error('Erro ao criar notificação na BD:', res.mensagem);
                    return false;
                }
            })
            .catch(e => {
                console.error('Erro fetch notificação:', e);
                return false;
            });
    }

    async function buscarMensagensNaoLidas() {
        console.log('Buscando mensagens não lidas...');
        try {
            const response = await fetch('../scripts/verificar_mensagens_nao_lidas.php');
            console.log('Resposta do fetch mensagens:', response.status);
            const mensagens = await response.json();

            if (!Array.isArray(mensagens)) {
                console.warn('Resposta inesperada para mensagens:', mensagens);
                return 0;
            }

            let novasMensagens = 0;

            for (const msg of mensagens) {
                console.log('Mensagem atual:', msg);
                if (!mensagensNotificadas.has(msg.id_mensagem)) {
                    console.log('Nova mensagem não lida detectada:', msg);

                    // Texto da notificação conforme solicitado
                    const textoNotificacao = "Recebeste uma mensagem!";
                    const corpoNotificacao = "De " + msg.remetente_nome + ", produto: " + msg.nome_produto;

                    // Cria a notificação na BD para o user
                    console.log('Tentando criar notificação na BD com texto:', textoNotificacao);
                    const criada = await criarNotificacaoBD(textoNotificacao);

                    if (criada) {
                        console.log('Mostrando notificação para o utilizador.');
                        // Mostrar toast e notificação desktop com texto customizado
                        showNotification(textoNotificacao, corpoNotificacao);
                        mensagensNotificadas.add(msg.id_mensagem);
                        novasMensagens++;
                    } else {
                        console.warn('Notificação na BD não foi criada, não mostraremos o alerta.');
                    }
                } else {
                    console.log('Mensagem já notificada:', msg.id_mensagem);
                }
            }

            sessionStorage.setItem('mensagensNotificadas', JSON.stringify(Array.from(mensagensNotificadas)));

            console.log('Total de novas mensagens notificadas:', novasMensagens);
            return novasMensagens;
        } catch (e) {
            console.error('Erro ao buscar mensagens não lidas:', e);
            return 0;
        }
    }

    function buscarNotificacoes() {
        console.log('Buscando notificações...');
        return fetch('../Functions/ajax_buscar_notificacoes.php')
            .then(r => {
                console.log('Resposta do fetch notificações:', r.status);
                return r.json();
            })
            .then(notificacoes => {
                let mostradas = sessionStorage.getItem('notificacoesMostradas');
                mostradas = mostradas ? JSON.parse(mostradas) : [];

                notificacoes.forEach(n => {
                    if (!mostradas.includes(n.id_notificacao)) {
                        console.log('Nova notificação detectada:', n);
                        // Mostra só notificação desktop (sem toast) para notificações gerais
                        if ("Notification" in window && Notification.permission === "granted" && document.visibilityState !== 'visible') {
                            new Notification("Nova Notificação", {
                                body: n.conteudo,
                                badge: "../Imagens/app/NABU-LOGO.png",
                                vibrate: [200, 100, 200],
                                tag: `notificacao-${n.id_notificacao}`,
                                renotify: true,
                                timestamp: Date.now()
                            });
                        } else {
                            console.log('Permissão para notificações desktop não concedida ou página visível.');
                        }
                        mostradas.push(n.id_notificacao);
                    } else {
                        console.log('Notificação já mostrada:', n.id_notificacao);
                    }
                });

                sessionStorage.setItem('notificacoesMostradas', JSON.stringify(mostradas));

                return buscarMensagensNaoLidas();
            })
            .then(() => {
                atualizarBadgeServidor();
            })
            .catch(e => {
                console.error('Erro ao buscar notificações:', e);
            });
    }

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
                    console.log('Badge atualizado com quantidade:', data.quantidade);
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

    document.addEventListener('DOMContentLoaded', () => {
        console.log('Documento carregado. Iniciando busca de notificações e mensagens.');
        buscarNotificacoes();
        setInterval(() => {
            console.log('Intervalo de 5 segundos: buscando notificações e mensagens...');
            buscarNotificacoes();
        }, 5000); // 5 segundos
    });
</script>


</body>
</html>
