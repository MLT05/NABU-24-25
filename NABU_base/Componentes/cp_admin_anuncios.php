<?php
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header('Location: ../Paginas/login.php');
    exit();
}

$link = new_db_connection();
$stmt = mysqli_stmt_init($link);

$id_user = $_SESSION['id_user'];
$is_admin = false;

$query_role = "SELECT ref_role FROM users WHERE id_user = ?";
if (mysqli_stmt_prepare($stmt, $query_role)) {
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

// Buscar todos os anúncios
$anuncios = [];
$sql = "SELECT a.id_anuncio, a.nome_produto, a.descricao, a.preco, a.data_insercao, u.nome AS nome_user
        FROM anuncios a
        JOIN users u ON a.ref_user = u.id_user
        ORDER BY a.data_insercao DESC";
$result = mysqli_query($link, $sql);
while ($row = mysqli_fetch_assoc($result)) {
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
                <th>Data</th>
                <th>Utilizador</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($anuncios as $anuncio): ?>
                <tr>
                    <td><?= $anuncio['id_anuncio'] ?></td>
                    <td><?= htmlspecialchars($anuncio['nome_produto']) ?></td>
                    <td><?= number_format($anuncio['preco'], 2, ',', '.') ?> €</td>
                    <td><?= htmlspecialchars(mb_strimwidth($anuncio['descricao'], 0, 60, '...')) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($anuncio['data_insercao']))) ?></td>
                    <td><?= htmlspecialchars($anuncio['nome_user']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

