<?php

require_once '../Connections/connection.php';


if (!isset($_GET['id_user'])) {
    // Se não estiver logado, redireciona pro login
    header("Location: ../Paginas/login.php");

} else {


    $id_user = $_GET['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT nome, pfp , login, email, contacto FROM users WHERE id_user = ?";


    $capa = "default3.png"; // imagem padrão caso não tenha capa

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nome, $capa_db, $login, $email, $contacto);

        if (mysqli_stmt_fetch($stmt)) {

            if (!empty($capa_db)) {
                $capa = $capa_db;
            }
        }

    }
    $feedbacks = [];
    $total = 0;
    $contador = 0;

    $media = $contador > 0 ? round($total / $contador, 1) : null;

    $query_feedback = "SELECT f.comentario, f.classificacao, f.data_feedback, u.nome 
                   FROM feedback f 
                   JOIN users u ON f.ref_avaliador = u.id_user 
                   WHERE f.ref_user = ?
                   ORDER BY f.data_feedback DESC";

    if (mysqli_stmt_prepare($stmt, $query_feedback)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $comentario, $classificacao, $data_feedback, $nome_avaliador);

        while (mysqli_stmt_fetch($stmt)) {
            $feedbacks[] = [
                'comentario' => $comentario,
                'classificacao' => $classificacao,
                'data' => $data_feedback,
                'avaliador' => $nome_avaliador
            ];
        }

        mysqli_stmt_close($stmt);
    }
    $contador = count($feedbacks);

    foreach ($feedbacks as $fb) {
        $total += $fb['classificacao'];
    }

    $media = $contador > 0 ? round($total / $contador, 1) : null;

    mysqli_close($link);
}
?>


<main class="body_index">

    <div class="text-center mb-4">
        <img src="../uploads/pfp/<?php echo htmlspecialchars($capa); ?>" alt="Foto de perfil" class="rounded-circle border border-success imagempfp" style="object-fit: cover;">
        <h2 class="mt-2 verde_escuro"><?php echo htmlspecialchars($nome); ?></h2>
    </div>
    <h6 class="fw-bold mt-4 verde_escuro fs-4">Informações do Utilizador</h6>

            <!-- Contactos do vendedor -->


            <div class="mb-3">
                <label class="form-label fw-bold verde_escuro"><strong>Email:</strong></label>
                <p class="verde_escuro"><?= htmlspecialchars($email) ?></p>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold verde_escuro"><strong>Contacto telefónico:</strong></label>
                <p class="verde_escuro"><?= htmlspecialchars($contacto) ?></p>
            </div>

    <?php if (!empty($feedbacks)): ?>
        <!-- Avaliação média -->
        <div class="mb-4">
            <h6 class="fw-bold verde_escuro fs-4">Avaliação</h6>
            <p class="fs-5 verde_escuro">
                Média de <strong><?= $media ?></strong> em <?= $contador ?> avaliações
            </p>
        </div>

        <!-- Classificações individuais -->
        <div class="mb-3">
            <h6 class="fw-bold mt-4 verde_escuro fs-4">Classificações:</h6>
        </div>

        <div class="row">
            <?php foreach (array_slice($feedbacks, 0, 5) as $feedback): ?>
                <div class="col-md-6 mb-3">
                    <div class="card verde_claro_bg shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Avaliação: <?= htmlspecialchars($feedback['classificacao']) ?>/10</h5>
                            <h6 class="card-subtitle mb-2 text-muted">Por: <?= htmlspecialchars($feedback['avaliador']) ?> em <?= date("d/m/Y", strtotime($feedback['data'])) ?></h6>
                            <p class="card-text"><?= nl2br(htmlspecialchars($feedback['comentario'])) ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <?php if ($contador > 5): ?>
            <div class="text-center">
                <a href="ver_mais_feedbacks.php?id_user=<?= $id_user ?>" class="btn btn-outline-success">Ver mais avaliações</a>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <p class="text-muted">Este utilizador ainda não recebeu avaliações.</p>
    <?php endif; ?>


</main>

