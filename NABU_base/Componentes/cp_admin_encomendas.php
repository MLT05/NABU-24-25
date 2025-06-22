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

// Atualizar estado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['encomenda_id'], $_POST['novo_estado'])) {
    $encomenda_id = (int)$_POST['encomenda_id'];
    $novo_estado = (int)$_POST['novo_estado'];

    $update_sql = "UPDATE encomendas SET ref_estado = ? WHERE id_encomenda = ?";
    if (mysqli_stmt_prepare($stmt, $update_sql)) {
        mysqli_stmt_bind_param($stmt, 'ii', $novo_estado, $encomenda_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: ../Paginas/admin_encomendas.php");
        exit();
    }
}

$encomendas = [];
$sql = "SELECT e.id_encomenda, e.data_encomenda, e.ref_estado, u.nome AS nome_user,
               (SELECT nome_produto FROM anuncios a WHERE a.id_anuncio = e.ref_anuncio) AS produto
        FROM encomendas e
        JOIN users u ON e.ref_comprador = u.id_user
        ORDER BY e.data_encomenda DESC";
$result = mysqli_query($link, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $encomendas[] = $row;
}

$estados = [1 => 'Em espera', 2 => 'Por recolher', 3 => 'Finalizada'];
$cores = [1 => 'warning', 2 => 'info', 3 => 'success'];
?>

<div class="body_index py-4">
    <h2 class="text-center text-success mb-4">Gestão de Encomendas</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Produto</th>
                <th>Data</th>
                <th>Comprador</th>
                <th>Estado</th>
                <th>Mudar Estado</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($encomendas as $enc): ?>
                <tr>
                    <td><?= $enc['id_encomenda'] ?></td>
                    <td><?= htmlspecialchars($enc['produto']) ?></td>
                    <td><?= htmlspecialchars(date('d/m/Y', strtotime($enc['data_encomenda']))) ?></td>
                    <td><?= htmlspecialchars($enc['nome_user']) ?></td>
                    <td>
                            <span class="badge bg-<?= $cores[$enc['ref_estado']] ?>">
                                <?= $estados[$enc['ref_estado']] ?>
                            </span>
                    </td>
                    <td>
                        <form method="POST" class="d-flex gap-2 align-items-center">
                            <input type="hidden" name="encomenda_id" value="<?= $enc['id_encomenda'] ?>">
                            <select name="novo_estado" class="form-select form-select-sm">
                                <?php foreach ($estados as $k => $v): ?>
                                    <option value="<?= $k ?>" <?= $enc['ref_estado'] == $k ? 'selected' : '' ?>>
                                        <?= $v ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" class="btn btn-sm btn-outline-success">Atualizar</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
