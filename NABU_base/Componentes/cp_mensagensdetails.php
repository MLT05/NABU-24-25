<?php
require_once '../Connections/connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio']) && is_numeric($_POST['id_anuncio'])) {

    $id_user = $_SESSION['id_user'];
    $id_anuncio = $_POST['id_anuncio'];

    $link = new_db_connection();

    $stmt = mysqli_stmt_init($link);

    $query = "SELECT a.nome_produto, a.preco, u.nome, u.id_user, a.capa, m.descricao AS medida_desc, m.abreviatura 
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

    mysqli_stmt_bind_result($stmt, $nome_produto,$preco, $nome_user, $id_user, $capa, $medida_desc, $medida_abr);

    if ($medida_abr == "UN") {
        $min_medida = 1;
    } else {
        $min_medida = 0.05;
    }
    mysqli_stmt_fetch($stmt)
    ?>
    <a href="javascript:history.back()" class=" text-decoration-none d-inline-flex " >
        <span class="material-icons verde_escuro" style="font-size: 2.5rem">arrow_back</span>

    </a>
    <div class="w-100 verde_claro_bg anuncio_mensagens p-3 d-flex justify-content-between rounded-3 shadow-sm">

        <!-- Imagem + Nome + Produto -->
        <div class="d-flex align-items-center">
            <img src="../uploads/capas/<?php echo htmlspecialchars($capa); ?>"
                 alt="<?php echo htmlspecialchars($capa); ?>"
                 class="rounded-circle me-3"
                 style="width: 50px; height: 50px; object-fit: cover;">

            <div>
                <h6 class="mb-0 fw-bold text-dark fs-2"><?php echo htmlspecialchars($nome_user); ?></h6>
                <small class="fs-4 pt-2"><?php echo htmlspecialchars($nome_produto); ?></small>
            </div>
        </div>

        <!-- Preço + Ícone (ícone no topo) -->
        <div class="d-flex flex-column align-items-end justify-content-between">
            <i class="bi bi-info-circle-fill text-secondary mb-2"></i>
            <div class="fw-semibold text-dark pe-4">
                <?php echo htmlspecialchars($preco); ?>€ /<?php echo htmlspecialchars($medida_abr); ?>
            </div>
        </div>

    </div>

    <main class="body_index container">

    </main>

<?php
} else {
    header("Location: ../Paginas/mensagens.php");
    exit();
}