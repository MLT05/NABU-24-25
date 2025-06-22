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

// Processar promocoes/democoes/apagar
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['target_id'])) {
    $target_id = (int) $_POST['target_id'];

    if ($_POST['action'] === 'delete') {
        $query = "DELETE FROM users WHERE id_user = ?";
    } elseif ($_POST['action'] === 'promote') {
        $query = "UPDATE users SET ref_role = 1 WHERE id_user = ?";
    } elseif ($_POST['action'] === 'demote') {
        $query = "UPDATE users SET ref_role = 2 WHERE id_user = ?";
    }

    if (isset($query) && mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $target_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header('Location: ../Paginas/admin_users.php');
        exit();
    }
}

// Buscar todos os users
$users = [];
$query_users = "SELECT id_user, nome, email, ref_role FROM users ORDER BY id_user DESC";
$result = mysqli_query($link, $query_users);
while ($row = mysqli_fetch_assoc($result)) {
    $users[] = $row;
}
?>

<div class="body_index py-4">
    <h2 class="text-center text-success mb-4">Gestão de Utilizadores</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Email</th>
                <th>Role</th>
                <th>Ações</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($users as $user): ?>
                <tr>
                    <td><?= $user['id_user'] ?></td>
                    <td><?= htmlspecialchars($user['nome']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td>
                        <?= $user['ref_role'] == 1 ? '<span class="badge bg-warning text-dark">Admin</span>' : '<span class="badge bg-secondary">User</span>' ?>
                    </td>
                    <td>
                        <div class="d-flex gap-2">
                            <?php if ($user['id_user'] != $id_user): ?>
                                <form method="POST" onsubmit="return confirm('Tem a certeza que quer apagar este utilizador?')">
                                    <input type="hidden" name="target_id" value="<?= $user['id_user'] ?>">
                                    <input type="hidden" name="action" value="delete">
                                    <button type="submit" class="btn btn-sm btn-danger">Apagar</button>
                                </form>

                                <?php if ($user['ref_role'] == 2): ?>
                                    <form method="POST">
                                        <input type="hidden" name="target_id" value="<?= $user['id_user'] ?>">
                                        <input type="hidden" name="action" value="promote">
                                        <button type="submit" class="btn btn-sm btn-warning">Promover</button>
                                    </form>
                                <?php else: ?>
                                    <form method="POST">
                                        <input type="hidden" name="target_id" value="<?= $user['id_user'] ?>">
                                        <input type="hidden" name="action" value="demote">
                                        <button type="submit" class="btn btn-sm btn-secondary">Rebaixar</button>
                                    </form>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="text-muted">(Tu)</span>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

