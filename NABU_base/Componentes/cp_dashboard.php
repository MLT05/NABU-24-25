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

function contar($con, $sql) {
    $stmt = mysqli_stmt_init($con);
    $count = 0;
    if (mysqli_stmt_prepare($stmt, $sql)) {
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $count);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }
    return $count;
}

$total_users = contar($link, "SELECT COUNT(*) FROM users");
$total_anuncios = contar($link, "SELECT COUNT(*) FROM anuncios");
$total_encomendas = contar($link, "SELECT COUNT(*) FROM encomendas");
$encomendas_espera = contar($link, "SELECT COUNT(*) FROM encomendas WHERE ref_estado = 1");
$encomendas_recolher = contar($link, "SELECT COUNT(*) FROM encomendas WHERE ref_estado = 2");
$encomendas_finalizadas = contar($link, "SELECT COUNT(*) FROM encomendas WHERE ref_estado = 3");
$total_mensagens = contar($link, "SELECT COUNT(*) FROM mensagens");
$total_notificacoes = contar($link, "SELECT COUNT(*) FROM notificacoes");

$ultimos_users = [];
$sql_users = "SELECT nome, email FROM users ORDER BY id_user DESC LIMIT 5";
$result_users = mysqli_query($link, $sql_users);
while ($row = mysqli_fetch_assoc($result_users)) {
    $ultimos_users[] = $row;
}

$ultimos_anuncios = [];
$sql_anuncios = "SELECT nome_produto, data_insercao FROM anuncios ORDER BY data_insercao DESC LIMIT 5";
$result_anuncios = mysqli_query($link, $sql_anuncios);
while ($row = mysqli_fetch_assoc($result_anuncios)) {
    $ultimos_anuncios[] = $row;
}

$ultimas_encomendas = [];
$sql_encomendas = "SELECT id_encomenda, data_encomenda, ref_comprador FROM encomendas ORDER BY data_encomenda DESC LIMIT 5";
$result_encomendas = mysqli_query($link, $sql_encomendas);
while ($row = mysqli_fetch_assoc($result_encomendas)) {
    $ultimas_encomendas[] = $row;
}
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    .dashboard-card {
        border-radius: 1rem;
        box-shadow: 0 0.25rem 0.75rem rgba(0,0,0,0.1);
        transition: transform 0.2s ease-in-out;
        cursor: pointer;
    }
    .dashboard-card:hover {
        transform: scale(1.02);
    }
</style>

<div class="body_index py-4">
    <h2 class="text-center text-success mb-4">Painel de Administração</h2>

    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4">
            <a href="../Paginas/admin_users.php" class="text-decoration-none text-dark">
                <div class="card dashboard-card p-3 text-center bg-light">
                    <div class="text-primary mb-2">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <h6>Utilizadores</h6>
                    <p class="fs-4 fw-bold"><?= $total_users ?></p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4">
            <a href="../Paginas/admin_anuncios.php" class="text-decoration-none text-dark">
                <div class="card dashboard-card p-3 text-center bg-light">
                    <div class="text-success mb-2">
                        <i class="bi bi-megaphone-fill fs-3"></i>
                    </div>
                    <h6>Anúncios</h6>
                    <p class="fs-4 fw-bold"><?= $total_anuncios ?></p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4">
            <a href="../Paginas/admin_encomendas.php" class="text-decoration-none text-dark">
                <div class="card dashboard-card p-3 text-center bg-light">
                    <div class="text-warning mb-2">
                        <i class="bi bi-box-seam-fill fs-3"></i>
                    </div>
                    <h6>Encomendas</h6>
                    <p class="fs-4 fw-bold"><?= $total_encomendas ?></p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4">
            <a href="../Paginas/admin_mensagens.php" class="text-decoration-none text-dark">
                <div class="card dashboard-card p-3 text-center bg-light">
                    <div class="text-dark mb-2">
                        <i class="bi bi-chat-dots-fill fs-3"></i>
                    </div>
                    <h6>Mensagens</h6>
                    <p class="fs-4 fw-bold"><?= $total_mensagens ?></p>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4">
            <a href="../Paginas/admin_notificacoes.php" class="text-decoration-none text-dark">
                <div class="card dashboard-card p-3 text-center bg-light">
                    <div class="text-danger mb-2">
                        <i class="bi bi-bell-fill fs-3"></i>
                    </div>
                    <h6>Notificações</h6>
                    <p class="fs-4 fw-bold"><?= $total_notificacoes ?></p>
                </div>
            </a>
        </div>
    </div>

    <div class="card mb-5">
        <div class="card-header bg-success text-white">Encomendas por Estado</div>
        <div class="card-body">
            <canvas id="estadoChart"></canvas>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <h5>Ultimos Utilizadores</h5>
            <ul class="list-group">
                <?php foreach ($ultimos_users as $u): ?>
                    <li class="list-group-item">
                        <strong><?= htmlspecialchars($u['nome']) ?></strong><br>
                        <small><?= htmlspecialchars($u['email']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-4">
            <h5>Ultimos Anúncios</h5>
            <ul class="list-group">
                <?php foreach ($ultimos_anuncios as $a): ?>
                    <li class="list-group-item">
                        <?= htmlspecialchars($a['nome_produto']) ?><br>
                        <small><?= htmlspecialchars($a['data_insercao']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="col-md-4">
            <h5>Ultimas Encomendas</h5>
            <ul class="list-group">
                <?php foreach ($ultimas_encomendas as $e): ?>
                    <li class="list-group-item">
                        Encomenda #<?= $e['id_encomenda'] ?> para user #<?= $e['ref_comprador'] ?><br>
                        <small><?= htmlspecialchars($e['data_encomenda']) ?></small>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<script>
    const ctx = document.getElementById('estadoChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Em Espera', 'Por Recolher', 'Finalizadas'],
            datasets: [{
                data: [<?= $encomendas_espera ?>, <?= $encomendas_recolher ?>, <?= $encomendas_finalizadas ?>],
                backgroundColor: ['#ffc107', '#17a2b8', '#28a745'],
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
</script>
