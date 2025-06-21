<?php

require_once '../Connections/connection.php';


if (!isset($_GET['id_user'])) {
    // Se não estiver logado, redireciona pro login
    header("../Paginas/login.php");

} else {


    $id_user = $_GET['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT nome, pfp , login, email, contacto FROM users WHERE id_user = ?";


    $capa = "defaultpfp.png"; // imagem padrão caso não tenha capa

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nome, $capa_db, $login, $email, $contacto);

        if (mysqli_stmt_fetch($stmt)) {

            if (!empty($capa_db)) {
                $capa = $capa_db;
            }
        }
        mysqli_stmt_close($stmt);
    }


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

    <div class="mb-3">
        <label class="form-label verde_escuro fw-semibold"><strong>Classificações:</strong></label>
        <p class="verde_escuro"> Classificação aqui</p>
    </div>


</main>

