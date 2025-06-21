<?php

require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Valida dados do POST
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['id_anuncio'], $_POST['id_outro_user'])
    && is_numeric($_POST['id_anuncio'])
    && is_numeric($_POST['id_outro_user'])) {

    $id_anuncio = (int) $_POST['id_anuncio'];
    $id_outro_user = (int) $_POST['id_outro_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Buscar dados do user logado (sessão)
    $queryUser = "SELECT nome, email, contacto, pfp FROM users WHERE id_user = ?";
    if (!mysqli_stmt_prepare($stmt, $queryUser)) {
        echo "Erro na preparação da query do user.";
        exit;
    }
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $user_nome, $user_email, $user_contacto, $user_pfp);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Buscar dados do interlocutor
    $stmt = mysqli_stmt_init($link);
    $queryInterlocutor = "SELECT nome, email, contacto, pfp FROM users WHERE id_user = ?";
    if (!mysqli_stmt_prepare($stmt, $queryInterlocutor)) {
        echo "Erro na preparação da query do interlocutor.";
        exit;
    }
    mysqli_stmt_bind_param($stmt, "i", $id_outro_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $inter_nome, $inter_email, $inter_contacto, $inter_pfp);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    // Buscar dados do anúncio
    $stmt = mysqli_stmt_init($link);
    $queryAnuncio = "SELECT a.nome_produto, a.preco, a.capa, m.descricao AS medida_desc, m.abreviatura
                    FROM anuncios a
                    INNER JOIN medidas m ON a.ref_medida = m.id_medida
                    WHERE a.id_anuncio = ?";
    if (!mysqli_stmt_prepare($stmt, $queryAnuncio)) {
        echo "Erro na preparação da query do anúncio.";
        exit;
    }
    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $nome_produto, $preco, $capa, $medida_desc, $medida_abr);
    if (!mysqli_stmt_fetch($stmt)) {
        echo "Anúncio não encontrado.";
        exit;
    }
    mysqli_stmt_close($stmt);

    // Buscar mensagens entre os dois users para este produto
    $stmt = mysqli_stmt_init($link);
    $queryMensagens = "
        SELECT mensagem, ref_remetente, data_envio 
        FROM mensagens 
        WHERE ref_produto = ? 
          AND ((ref_remetente = ? AND ref_destinatario = ?) OR (ref_remetente = ? AND ref_destinatario = ?))
        ORDER BY data_envio ASC";
    if (!mysqli_stmt_prepare($stmt, $queryMensagens)) {
        echo "Erro na preparação da query de mensagens.";
        exit;
    }
    mysqli_stmt_bind_param($stmt, "iiiii", $id_anuncio, $id_user, $id_outro_user, $id_outro_user, $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $mensagem, $remetente, $data_envio);

    // Carregar mensagens numa array para facilitar o uso no HTML
    $mensagens = [];
    while (mysqli_stmt_fetch($stmt)) {
        $mensagens[] = [
            'mensagem' => $mensagem,
            'remetente' => $remetente,
            'data_envio' => $data_envio,
        ];
    }
    mysqli_stmt_close($stmt);

    mysqli_close($link);

} else {
    header("Location: ../Paginas/mensagens.php");
    exit();
}
?>

<!-- HTML abaixo -->

<a href="../Paginas/produto.php?id=<?= htmlspecialchars($id_anuncio) ?>" class="text-decoration-none text-reset">
    <div class="w-100 verde_claro_bg anuncio_mensagens p-3 d-flex justify-content-between rounded-3 shadow-sm">
        <div class="d-flex align-items-center">
            <img src="../uploads/pfp/<?= htmlspecialchars($inter_pfp) ?>"
                 alt="profile pic do interlocutor"
                 class="rounded-circle me-3"
                 style="width: 6vh; height: 6vh; object-fit: cover;">
            <div>
                <h6 class="mb-0 fw-bold verde_escuro fs-2"><?= htmlspecialchars($inter_nome) ?></h6>
                <small class="fs-4 verde_escuro fw-normal pt-2"><?= htmlspecialchars($nome_produto) ?></small>
            </div>
        </div>
        <div class="d-flex flex-column align-items-end justify-content-between">
            <i class="bi bi-info-circle-fill text-secondary mb-2"></i>
            <div class="fw-semibold text-dark pe-4">
                <?= htmlspecialchars($preco) ?>€ /<?= htmlspecialchars($medida_abr) ?>
            </div>
        </div>
    </div>
</a>

<main class="container flex-grow-1 d-flex flex-column" style="padding-bottom: 11vh;">
    <div class="flex-grow-1 overflow-auto d-flex flex-column-reverse">

        <?php foreach ($mensagens as $msg): ?>
            <?php if ($msg['remetente'] == $id_outro_user): ?>
                <!-- Mensagem do interlocutor -->
                <div class="d-flex justify-content-start mb-2">
                    <img src="../uploads/pfp/<?= htmlspecialchars($inter_pfp) ?>"
                         alt="Foto perfil"
                         class="rounded-circle me-2 align-self-end"
                         style="width: 30px; height: 30px; object-fit: cover;">
                    <div class="verde_claro_bg p-3 rounded-3 verde_escuro" style="max-width: 75%;">
                        <?= htmlspecialchars($msg['mensagem']) ?>
                    </div>
                </div>
            <?php else: ?>
                <!-- Mensagem do user logado -->
                <div class="d-flex justify-content-end mb-2">
                    <div class="verde_escuro_bg text-white p-3 rounded-3" style="max-width: 75%;">
                        <?= htmlspecialchars($msg['mensagem']) ?>
                    </div>
                    <div class="ms-2 align-self-end">
                        <img src="../uploads/pfp/<?= htmlspecialchars($user_pfp) ?>"
                             alt="Foto perfil"
                             class="rounded-circle"
                             style="width: 30px; height: 30px; object-fit: cover;">
                    </div>
                </div>
            <?php endif; ?>
        <?php endforeach; ?>

    </div>

    <!-- Formulário para enviar nova mensagem -->
    <form action="enviar_mensagem.php" method="post" class="d-flex align-items-center border rounded-3 p-2 bg-white mt-2">
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
