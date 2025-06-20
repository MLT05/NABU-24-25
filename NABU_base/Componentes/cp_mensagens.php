<?php
require_once '../Connections/connection.php';
$link = new_db_connection();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit;
}

$user_id = $_SESSION['id_user'];

// Buscar dados do utilizador atual para sidebar/perfil
$stmt_sidebar = mysqli_prepare($link, "SELECT nome, pfp FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt_sidebar, "i", $user_id);
mysqli_stmt_execute($stmt_sidebar);
mysqli_stmt_bind_result($stmt_sidebar, $username, $pfp);
mysqli_stmt_fetch($stmt_sidebar);
mysqli_stmt_close($stmt_sidebar);

// Buscar lista de outros utilizadores para iniciar conversas
$query_users = "SELECT id_user, nome, pfp FROM users WHERE id_user != ?";
$stmt_users = mysqli_prepare($link, $query_users);
mysqli_stmt_bind_param($stmt_users, "i", $user_id);
mysqli_stmt_execute($stmt_users);
$result_users = mysqli_stmt_get_result($stmt_users);
?>
<main class="body_index">
    <div class="h-100 d-flex">
        <!-- Sidebar de utilizadores -->
        <div class="border-end bg-white d-flex flex-column" style="width: 300px;">
            <header class="px-3 py-2 border-bottom text-primary fw-bold fs-5">
                Conversas
            </header>
            <div class="px-3 py-2 border-bottom">
                <input type="text" class="form-control rounded-pill" placeholder="Pesquisar..." />
            </div>
            <div class="flex-grow-1 overflow-auto">
                <?php while ($user = mysqli_fetch_assoc($result_users)): ?>
                    <?php
                    $pfp_filename = $user['pfp'];
                    $pfp_path = (!empty($pfp_filename) && file_exists("../uploads/pfp/" . $pfp_filename)) ? $pfp_filename : 'defaultpfp.png';
                    ?>
                    <div class="d-flex align-items-center px-3 py-2 border-bottom"
                         data-userid="<?= $user['id_user'] ?>"
                         data-username="<?= htmlspecialchars($user['nome']) ?>"
                         data-pfp="<?= htmlspecialchars($pfp_path) ?>"
                         style="cursor:pointer;">
                        <img src="../uploads/pfp/<?= htmlspecialchars($pfp_path) ?>" alt="Foto de perfil" class="rounded-circle" style="width: 45px; height: 45px; object-fit: cover;" />
                        <div class="ms-3 flex-grow-1">
                            <div class="fw-semibold text-dark"><?= htmlspecialchars($user['nome']) ?></div>
                            <div class="text-muted small text-truncate">Clique para conversar...</div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
        <div class="d-none">
        <!-- Janela do chat -->
            <section class="d-flex flex-column flex-grow-1 bg-light">
            <!-- Cabeçalho do chat -->
            <header class="d-flex align-items-center px-3 py-2 border-bottom bg-white" style="display:none;">
                <img src="../uploads/pfp/<?= htmlspecialchars($pfp) ?>" alt="pfp" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover; margin-right: 15px;" />
                <div class="fw-bold fs-5"></div>
            </header>

            <!-- Área de mensagens -->
            <div class="flex-grow-1 overflow-auto p-3">
                <div class="text-center text-muted mt-5">Selecione uma conversa para começar.</div>
            </div>

            <!-- Campo para enviar mensagens -->
            <footer class="p-3 border-top bg-white" style="display:none;">
                <form id="message-form" class="d-flex align-items-center" enctype="multipart/form-data">
                    <input type="file" id="image-input" name="image_file" accept="image/*" style="display:none;" />
                    <button type="button" class="btn btn-link text-primary me-2" id="image-button-trigger" title="Enviar Imagem">
                        <i class="fas fa-image"></i>
                    </button>
                    <button type="button" class="btn btn-link text-primary me-2" id="record-audio-button" title="Gravar Áudio">
                        <i class="fas fa-microphone"></i>
                    </button>
                    <input type="text" id="message-input" name="message_content" class="form-control rounded-pill me-2" placeholder="Escreva uma mensagem..." autocomplete="off" />
                    <button type="submit" class="btn btn-primary rounded-circle" title="Enviar" style="width: 45px; height: 45px;">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </form>
            </footer>
        </section>
        </div>
    </div>
</main>

<!-- Bootstrap Bundle com Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- Script de chat -->
<script src="scripts/sc_chat.js"></script>

