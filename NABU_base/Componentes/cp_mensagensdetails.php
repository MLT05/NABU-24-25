<?php
require_once '../Connections/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio']) && is_numeric($_POST['id_anuncio'])) {

    $id_user = $_SESSION['id_user'];
    $id_anuncio = $_POST['id_anuncio'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT a.nome_produto, a.preco, a.capa, m.descricao AS medida_desc, m.abreviatura,
                     u.id_user, u.nome, u.email, u.password_hash, u.login, u.contacto, u.ref_role, u.pfp
              FROM anuncios a
              INNER JOIN users u ON a.ref_user = u.id_user
              INNER JOIN medidas m ON a.ref_medida = m.id_medida
              WHERE a.id_anuncio = ?";

    if (!mysqli_stmt_prepare($stmt, $query)) {
        echo "Erro na preparação da query.";
        exit;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt,
        $nome_produto, $preco, $capa, $medida_desc, $medida_abr,
        $user_id, $user_nome, $user_email, $user_password_hash, $user_login, $user_contacto, $user_ref_role, $user_pfp
    );

    if (mysqli_stmt_fetch($stmt)) {

        // Aqui podes usar os dados do user, por exemplo:
        // $user_nome, $user_email, $user_contacto, $user_pfp, etc.

        ?>
        <!-- HTML com os dados -->
        <a href="../Paginas/produto.php?id=<?php echo htmlspecialchars($id_anuncio); ?>" class="text-decoration-none text-reset">
            <div class="w-100 verde_claro_bg anuncio_mensagens p-3 d-flex justify-content-between rounded-3 shadow-sm">
                <div class="d-flex align-items-center">
                    <img src="../uploads/pfp/<?php echo htmlspecialchars($user_pfp); ?>"
                         alt="profile pic do destinatário"
                         class="rounded-circle me-3"
                         style="width: 6vh; height: 6vh; object-fit: cover;">
                    <div>
                        <h6 class="mb-0 fw-bold verde_escuro fs-2"><?php echo htmlspecialchars($user_nome); ?></h6>
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
        <main class="body_index container d-flex flex-column justify-content-end">
            <a href="javascript:history.back()" class=" text-decoration-none d-inline-flex " >
                <span class="material-icons verde_escuro" style="font-size: 2.5rem">arrow_back</span>

            </a>
            <!-- Área de mensagens (ocupa o espaço restante acima da caixa de texto) -->
            <div class="flex-grow-1 overflow-auto mb-3" id="chat-area">
                <!-- Mensagens aqui -->


            <!-- Área de mensagens -->
                <div class="flex-grow-1 overflow-auto mb-3" id="chat-area">
                    <!-- Mensagem do utilizador atual -->
                    <div class="d-flex justify-content-end mb-2">
                        <div class="verde_escuro_bg verde_claro p-2 rounded-3" style="max-width: 75%;">
                            Bom dia! Estou interessado nos tomates. Podemos combinar a entrega amanhã?
                        </div>
                        <div class="ms-2 align-self-end">
                            <i class="bi bi-person-circle fs-4 text-verde-escuro"></i>
                        </div>
                    </div>

                    <!-- Mensagem do outro utilizador -->
                    <div class="d-flex justify-content-start mb-2">
                        <img src="../uploads/pfp/<?php echo htmlspecialchars($user_pfp); ?>"
                             alt="Foto perfil"
                             class="rounded-circle me-2"
                             style="width: 30px; height: 30px; object-fit: cover;">
                        <div class="verde_claro_bg p-2 rounded-3 verde_escuro" style="max-width: 75%;">
                            Fica combinado amanhã na sua morada às 10h00.
                        </div>
                    </div>
                </div>
            </div>


            <!-- Caixa de mensagem fixa ao fundo do main -->
            <form action="enviar_mensagem.php" method="post" class="d-flex align-items-center border rounded-3 p-2 bg-white">
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

} else {
    header("Location: ../Paginas/mensagens.php");
    exit();
}
?>
