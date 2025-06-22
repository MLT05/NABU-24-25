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

$mensagens = [];
$sql = "SELECT m.id_mensagem, m.mensagem, m.data_envio, u.nome AS nome_user
        FROM mensagens m
        JOIN users u ON m.ref_remetente = u.id_user
        ORDER BY m.data_envio DESC";
$result = mysqli_query($link, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    $mensagens[] = $row;
}
?>

<div class="body_index py-4">
    <h2 class="text-center text-success mb-4">Gestão de Mensagens</h2>

    <div class="table-responsive">
        <table class="table table-bordered table-striped">
            <thead class="table-success">
            <tr>
                <th>ID</th>
                <th>Utilizador</th>
                <th>Mensagem</th>
                <th>Data</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($mensagens as $m): ?>
                <tr>
                    <td><?= $m['id_mensagem'] ?></td>
                    <td><?= htmlspecialchars($m['nome_user']) ?></td>
                    <td><?= nl2br(htmlspecialchars($m['mensagem'])) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($m['data_envio'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

