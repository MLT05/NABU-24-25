<?php
require_once '../Connections/connection.php';

// Verifica admin
if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit();
}

$link = new_db_connection();
$id_user = $_SESSION['id_user'];
$is_admin = false;

// Verifica role
$stmt = mysqli_stmt_init($link);
if (mysqli_stmt_prepare($stmt, "SELECT ref_role FROM users WHERE id_user = ?")) {
    mysqli_stmt_bind_param($stmt, 'i', $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $ref_role);
    if (mysqli_stmt_fetch($stmt)) {
        $is_admin = ($ref_role == 1);
    }
    mysqli_stmt_close($stmt);
}

if (!$is_admin) {
    echo "<div class='alert alert-danger text-center'>Acesso restrito.</div>";
    exit();
}

// Se foi pedido para apagar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio']) && is_numeric($_POST['id_anuncio'])) {
    $id_anuncio = (int) $_POST['id_anuncio'];

    // Buscar capa
    $stmt_capa = mysqli_prepare($link, "SELECT capa FROM anuncios WHERE id_anuncio = ?");
    mysqli_stmt_bind_param($stmt_capa, 'i', $id_anuncio);
    mysqli_stmt_execute($stmt_capa);
    mysqli_stmt_bind_result($stmt_capa, $capa);
    mysqli_stmt_fetch($stmt_capa);
    mysqli_stmt_close($stmt_capa);

    $upload_dir = "../uploads/capas/";
    $default_image = "default-image.jpg";

    if ($capa && $capa !== $default_image && file_exists($upload_dir . $capa)) {
        unlink($upload_dir . $capa);
    }

    // Apagar relacionado
    $stmt = mysqli_prepare($link, "DELETE FROM carrinho WHERE anuncios_id_anuncio = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_anuncio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($link, "DELETE FROM favoritos WHERE anuncios_id_anuncio = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_anuncio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($link, "DELETE FROM encomendas WHERE ref_anuncio = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_anuncio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    $stmt = mysqli_prepare($link, "DELETE FROM anuncios WHERE id_anuncio = ?");
    mysqli_stmt_bind_param($stmt, 'i', $id_anuncio);
    if (mysqli_stmt_execute($stmt)) {
        echo "<div class='alert alert-success text-center'>Anúncio apagado com sucesso!</div>";
    } else {
        echo "<div class='alert alert-danger text-center'>Erro ao apagar anúncio.</div>";
    }
    mysqli_stmt_close($stmt);
}

// Buscar anúncios
$anuncios = [];
$sql = "SELECT a.id_anuncio, a.nome_produto, a.preco, LEFT(a.descricao, 50) AS resumo, u.nome AS nome_user 
        FROM anuncios a 
        JOIN users u ON a.ref_user = u.id_user 
        ORDER BY a.id_anuncio DESC";
$res = mysqli_query($link, $sql);
while ($row = mysqli_fetch_assoc($res)) {
    $anuncios[] = $row;
}
?>

<div class="body_index py-4">
    <h2 class="text-center text-success mb-4">Gestão de Anúncios</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Preço</th>
                <th>Descrição</th>
                <th>Utilizador</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($anuncios as $a): ?>
                <tr>
                    <td><?= $a['id_anuncio'] ?></td>
                    <td><?= htmlspecialchars($a['nome_produto']) ?></td>
                    <td><?= number_format($a['preco'], 2) ?>€</td>
                    <td><?= htmlspecialchars($a['resumo']) ?>...</td>
                    <td><?= htmlspecialchars($a['nome_user']) ?></td>
                    <td>
                        <form method="post" onsubmit="return confirm('Tem a certeza que deseja apagar este anúncio?');">
                            <input type="hidden" name="id_anuncio" value="<?= $a['id_anuncio'] ?>">
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-trash"></i> Apagar
                            </button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
