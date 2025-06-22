<?php
require_once '../Connections/connection.php';
include_once '../Functions/function_tempo.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio'], $_POST['id_outro_user'])
    && is_numeric($_POST['id_anuncio']) && is_numeric($_POST['id_outro_user'])) {

    $id_anuncio = (int) $_POST['id_anuncio'];
    $id_outro_user = (int) $_POST['id_outro_user'];
    header("Location: mensagens_details.php?id_anuncio=$id_anuncio&id_outro_user=$id_outro_user");
    exit();
}

if (isset($_GET['id_anuncio'], $_GET['id_outro_user']) && is_numeric($_GET['id_anuncio']) && is_numeric($_GET['id_outro_user'])) {

    $id_anuncio = (int) $_GET['id_anuncio'];
    $id_outro_user = (int) $_GET['id_outro_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Dados do user logado
    $queryUser = "SELECT nome, email, contacto, pfp FROM users WHERE id_user = ?";
    if (!mysqli_stmt_prepare($stmt, $queryUser)) {
        die("Erro na preparação da query do user.");
    }
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_nome, $user_email, $user_contacto, $user_pfp);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Dados do outro user
    $stmt = mysqli_stmt_init($link);
    $queryOutroUser = "SELECT nome, email, contacto, pfp FROM users WHERE id_user = ?";
    if (!mysqli_stmt_prepare($stmt, $queryOutroUser)) {
        die("Erro na preparação da query do outro user.");
    }
    mysqli_stmt_bind_param($stmt, "i", $id_outro_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $outro_nome, $outro_email, $outro_contacto, $outro_pfp);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Dados do anúncio
    $stmt = mysqli_stmt_init($link);
    $queryAnuncio = "SELECT nome_produto, preco, capa, ref_medida FROM anuncios WHERE id_anuncio = ?";
    if (!mysqli_stmt_prepare($stmt, $queryAnuncio)) {
        die("Erro na preparação da query do anúncio.");
    }
    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nome_produto, $preco, $capa, $ref_medida);
    if (!mysqli_stmt_fetch($stmt)) {
        die("Anúncio não encontrado.");
    }
    mysqli_stmt_close($stmt);

    // Pegar abreviatura da medida
    $stmt = mysqli_stmt_init($link);
    $queryMedida = "SELECT abreviatura FROM medidas WHERE id_medida = ?";
    if (!mysqli_stmt_prepare($stmt, $queryMedida)) {
        die("Erro na preparação da query da medida.");
    }
    mysqli_stmt_bind_param($stmt, "i", $ref_medida);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $medida_abr);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Buscar mensagens entre os dois usuários para este anúncio
    $stmt = mysqli_stmt_init($link);
    $queryMensagens = "SELECT ref_remetente, mensagem, data_envio 
                       FROM mensagens 
                       WHERE ref_produto = ? AND 
                             ((ref_remetente = ? AND ref_destinatario = ?) OR 
                              (ref_remetente = ? AND ref_destinatario = ?))
                       ORDER BY data_envio ASC";
    if (!mysqli_stmt_prepare($stmt, $queryMensagens)) {
        die("Erro na preparação da query das mensagens.");
    }
    mysqli_stmt_bind_param($stmt, "iiiii", $id_anuncio, $id_user, $id_outro_user, $id_outro_user, $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $ref_remetente_msg, $mensagem_texto, $data_envio_msg);
    ?>

    <div onclick="window.location.href='../Paginas/produto.php?id=<?= htmlspecialchars($id_anuncio) ?>';"
         class="text-decoration-none text-reset w-100 verde_claro_bg anuncio_mensagens p-3 d-flex justify-content-between align-items-center rounded-3 shadow-sm mb-1"
         style="cursor: pointer;">

        <div class="d-flex align-items-center flex-grow-1 overflow-hidden" style="min-width: 0;">
            <a href="../Paginas/perfil_outro.php?id_user=<?= htmlspecialchars($id_outro_user) ?>" onclick="event.stopPropagation();">
                <img src="../uploads/pfp/<?= htmlspecialchars($outro_pfp) ?>"
                     alt="Foto de perfil"
                     class="rounded-circle me-3 flex-shrink-0"
                     style="width: 6vh; height: 6vh; object-fit: cover;">
            </a>
            <div class="overflow-hidden">
                <h6 class="mb-0 fw-bold verde_escuro fs-2 text-truncate" style="max-width: 40vw;">
                    <?= htmlspecialchars($outro_nome) ?>
                </h6>
                <small class="fs-4 verde_escuro fw-normal pt-2 text-truncate" style="max-width: 40vw;" title="<?= htmlspecialchars($nome_produto) ?>">
                    <?= htmlspecialchars($nome_produto) ?>
                </small>
            </div>
        </div>

        <div class="d-flex flex-column align-items-end justify-content-between text-nowrap ms-3">
            <i class="bi bi-info-circle-fill text-secondary mb-2"></i>
            <div class="fw-semibold text-dark pe-4">
                <?= htmlspecialchars($preco) ?>€ /<?= htmlspecialchars($medida_abr) ?>
            </div>
        </div>
    </div>

    <main class="container flex-grow-1 d-flex flex-column" style="padding-bottom: 11vh; height: 70vh;">

        <div id="chat-mensagens" class="flex-grow-1 overflow-auto d-flex flex-column">

            <?php
            // Mostrar mensagens
            $mensagens = [];
            while (mysqli_stmt_fetch($stmt)) {
                $mensagens[] = [
                    'remetente' => $ref_remetente_msg,
                    'texto' => $mensagem_texto,
                    'data' => $data_envio_msg
                ];
            }
            mysqli_stmt_close($stmt);
            mysqli_close($link);

            // Inverter para mostrar da mais antiga para a mais recente
            $mensagens = array_reverse($mensagens);

            foreach ($mensagens as $msg) {
                if ($msg['remetente'] == $id_outro_user) {
                    // Mensagem do outro user
                    ?>
                    <div class="d-flex justify-content-start mb-2">
                        <img src="../uploads/pfp/<?= htmlspecialchars($outro_pfp) ?>"
                             alt="Foto perfil"
                             class="rounded-circle me-2 align-self-end"
                             style="width: 30px; height: 30px; object-fit: cover;">
                        <div class="verde_claro_bg p-3 rounded-3 verde_escuro" style="max-width: 75%;">
                            <?= htmlspecialchars($msg['texto']) ?>
                        </div>
                    </div>
                    <?php
                } else {
                    // Mensagem do user logado
                    ?>
                    <div class="d-flex justify-content-end mb-2">
                        <div class="verde_escuro_bg text-white p-3 rounded-3" style="max-width: 75%;">
                            <?= htmlspecialchars($msg['texto']) ?>
                        </div>
                        <div class="ms-2 align-self-end">
                            <img src="../uploads/pfp/<?= htmlspecialchars($user_pfp) ?>"
                                 alt="Foto perfil"
                                 class="rounded-circle"
                                 style="width: 30px; height: 30px; object-fit: cover;">
                        </div>
                    </div>
                    <?php
                }
            }
            ?>

        </div>

        <form class="d-flex align-items-center border rounded-3 p-2 bg-white mt-2">
            <input type="hidden" name="ref_remetente" value="<?= htmlspecialchars($id_user) ?>">
            <input type="hidden" name="ref_destinatario" value="<?= htmlspecialchars($id_outro_user) ?>">
            <input type="hidden" name="ref_produto" value="<?= htmlspecialchars($id_anuncio) ?>">

            <button type="button" class="btn p-0 me-2">
                <i class="bi bi-emoji-smile fs-4 text-success"></i>
            </button>
            <input type="text" name="mensagem" class="form-control border-0" placeholder="Escreva uma mensagem aqui..." required>
            <button type="submit" class="btn btn-success ms-2">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </main>
    <script>
        console.log("🧪 Script de mensagens_details carregado!");

        window.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('chat-mensagens');
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        });

        const id_anuncio = <?= json_encode($id_anuncio) ?>;
        const id_outro_user = <?= json_encode($id_outro_user) ?>;
        const user_id = <?= json_encode($id_user) ?>;
        const outro_pfp = <?= json_encode($outro_pfp) ?>;
        const user_pfp = <?= json_encode($user_pfp) ?>;

        function carregarMensagens() {
            console.log("⏳ A verificar mensagens novas...");

            return fetch(`../Functions/carregar_mensagens.php?id_anuncio=${id_anuncio}&id_outro_user=${id_outro_user}`)
                .then(response => {
                    if (!response.ok) {
                        console.error("❌ Erro ao carregar mensagens.");
                        return [];
                    }
                    return response.json();
                })
                .then(data => {
                    console.log("✅ Mensagens carregadas com sucesso:", data);

                    const container = document.getElementById("chat-mensagens");
                    container.innerHTML = ""; // Limpa tudo antes de reinserir

                    data.forEach((msg, index) => {
                        const isRemetenteOutro = msg.remetente == id_outro_user;

                        const nextMsg = data[index + 1];
                        const isNextSameSender = nextMsg && nextMsg.remetente == msg.remetente;

                        const div = document.createElement("div");
                        div.className = "d-flex mb-2 " + (isRemetenteOutro ? "justify-content-start" : "justify-content-end");
                        div.style.alignItems = "flex-end"; // garante alinhamento vertical

                        const msgDiv = document.createElement("div");
                        msgDiv.className = (isRemetenteOutro ? "verde_claro_bg verde_escuro" : "verde_escuro_bg text-white") + " p-3 rounded-3";
                        msgDiv.style.maxWidth = "75%";
                        msgDiv.textContent = msg.mensagem;

                        if (!isNextSameSender) {
                            const img = document.createElement("img");
                            img.src = "../uploads/pfp/" + (isRemetenteOutro ? outro_pfp : user_pfp);
                            img.className = "rounded-circle";
                            img.style.width = "30px";
                            img.style.height = "30px";
                            img.style.objectFit = "cover";

                            if (isRemetenteOutro) {
                                img.classList.add("me-2", "align-self-end");
                                div.appendChild(img);
                                div.appendChild(msgDiv);
                            } else {
                                img.classList.add("ms-2", "align-self-end");
                                div.appendChild(msgDiv);
                                div.appendChild(img);
                            }
                        } else {
                            const placeholder = document.createElement("div");
                            placeholder.style.width = "30px";
                            placeholder.style.height = "30px";

                            if (isRemetenteOutro) {
                                placeholder.classList.add("me-2");
                                div.appendChild(placeholder);
                                div.appendChild(msgDiv);
                            } else {
                                placeholder.classList.add("ms-2");
                                div.appendChild(msgDiv);
                                div.appendChild(placeholder);
                            }
                        }

                        container.appendChild(div);
                    });
                })
                .catch(error => console.error("🚨 Erro AJAX:", error));
        }

        // Atualiza já ao carregar a página
        carregarMensagens();

        // Atualiza automaticamente a cada 5 segundos
        setInterval(carregarMensagens, 5000);

        document.querySelector("form").addEventListener("submit", function (e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);
            const container = document.getElementById('chat-mensagens');

            fetch("../scripts/enviar_mensagem.php", {
                method: "POST",
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error("Erro ao enviar a mensagem");
                    }
                    console.log("📨 Mensagem enviada com sucesso");

                    form.querySelector("input[name='mensagem']").value = "";

                    // Aqui esperamos o carregarMensagens terminar antes de fazer scroll
                    return carregarMensagens();
                })
                .then(() => {
                    if (container) {
                        container.scrollTop = container.scrollHeight;
                    }
                })
                .catch(error => {
                    console.error("🚨 Erro ao enviar mensagem:", error);
                });
        });
    </script>

    <?php

} else {
    header("Location: ../Paginas/mensagens.php");
    exit();
}
?>
