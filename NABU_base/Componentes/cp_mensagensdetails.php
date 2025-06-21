<?php

require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

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

// Agora prepara a query para buscar o anúncio
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio']) && is_numeric($_POST['id_anuncio'])) {

    $id_anuncio = $_POST['id_anuncio'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $queryAnuncio = "SELECT a.nome_produto, a.preco, a.capa, m.descricao AS medida_desc, m.abreviatura,
                     u.id_user, u.nome, u.email, u.password_hash, u.login, u.contacto, u.ref_role, u.pfp
              FROM anuncios a
              INNER JOIN users u ON a.ref_user = u.id_user
              INNER JOIN medidas m ON a.ref_medida = m.id_medida
              WHERE a.id_anuncio = ?";

    if (!mysqli_stmt_prepare($stmt, $queryAnuncio)) {
        echo "Erro na preparação da query do anúncio.";
        exit;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt,
        $nome_produto, $preco, $capa, $medida_desc, $medida_abr,
        $user_id_anuncio, $user_nome_anuncio, $user_email_anuncio, $user_password_hash, $user_login, $user_contacto_anuncio, $user_ref_role, $user_pfp_anuncio
    );

    if (mysqli_stmt_fetch($stmt)) {
        // Aqui tens:
        // $user_nome, $user_email, $user_contacto, $user_pfp  => dados do user logado (sessão)
        // $user_nome_anuncio, $user_email_anuncio, $user_contacto_anuncio, $user_pfp_anuncio => dados do dono do anúncio

        ?>

        <!-- Exemplo onde podes usar as variáveis no HTML -->
        <a href="../Paginas/produto.php?id=<?php echo htmlspecialchars($id_anuncio); ?>" class="text-decoration-none text-reset">
            <div class="w-100 verde_claro_bg anuncio_mensagens p-3 d-flex justify-content-between rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <img src="../uploads/pfp/<?php echo htmlspecialchars($user_pfp_anuncio); ?>"
                         alt="profile pic do destinatário"
                         class="rounded-circle me-3"
                         style="width: 6vh; height: 6vh; object-fit: cover;">
                    <div>
                        <h6 class="mb-0 fw-bold verde_escuro fs-2"><?php echo htmlspecialchars($user_nome_anuncio); ?></h6>
                        <small class="fs-4 verde_escuro fw-normal pt-2"><?php echo htmlspecialchars($nome_produto); ?></small>
                    </div>
                </div>
                <div class="d-flex flex-column align-items-end justify-content-between">
                    <i class="bi bi-info-circle-fill text-secondary mb-2"></i>
                    <div class="fw-semibold text-dark pe-4">
                        <?php echo htmlspecialchars($preco); ?>€ /<?php echo htmlspecialchars($medida_abr); ?>
                    </div>
                </div>
            </div>
        </a>

        <main class="container flex-grow-1 d-flex flex-column" style="padding-bottom: 11vh;">
            <a href="javascript:history.back()" class="text-decoration-none d-inline-flex mb-2">
                <span class="material-icons verde_escuro" style="font-size: 2.5rem">arrow_back</span>
            </a>

            <div class="flex-grow-1 overflow-auto d-flex flex-column-reverse">

                <!-- Mensagem do outro utilizador (dono do anúncio) -->
                <div class="d-flex justify-content-start mb-2 align-self-end">
                    <img src="../uploads/pfp/<?php echo htmlspecialchars($user_pfp_anuncio); ?>"
                         alt="Foto perfil"
                         class="rounded-circle me-2 align-self-end"
                         style="width: 30px; height: 30px; object-fit: cover;">
                    <div class="verde_claro_bg p-2 rounded-3 verde_escuro" style="max-width: 75%;">
                        Fica combinado amanhã na sua morada às 10h00.
                    </div>
                </div>

                <!-- Mensagem do user logado (sessão) -->
                <div class="d-flex justify-content-end mb-2">
                    <div class="verde_escuro_bg verde_claro p-2 rounded-3" style="max-width: 75%;">
                        Bom dia! Estou interessado nos tomates. Podemos combinar a entrega amanhã?
                    </div>
                    <div class="ms-2 align-self-end">
                        <img src="../uploads/pfp/<?php echo htmlspecialchars($user_pfp); ?>"
                             alt="Foto perfil"
                             class="rounded-circle me-2"
                             style="width: 30px; height: 30px; object-fit: cover;">
                    </div>
                </div>

            </div>

            <form action="enviar_mensagem.php" method="post" class="d-flex align-items-center border rounded-3 p-2 bg-white mt-2">
                <button type="button" class="btn p-0 me-2">
                    <i class="bi bi-emoji-smile fs-4 text-success"></i>
                </button>
                <input type="text" name="mensagem" class="form-control border-0" placeholder="Escreva uma mensagem aqui..." required>
                <button type="submit" class="btn btn-success ms-2">
                    <i class="bi bi-send-fill"></i>
                </button>
            </form>
        </main>

        <?php
    } else {
        echo "Anúncio não encontrado.";
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);

} else {
    header("Location: ../Paginas/mensagens.php");
    exit();
}
?>
