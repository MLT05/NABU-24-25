<?php
session_start();
require_once '../connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    // Se não estiver logado, redireciona pro login
 header("../Paginas/login.php");

} else {


    $id_user = $_SESSION['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT nome, pfp , login, email, contacto FROM users WHERE id_user = ?";


    $capa = "defaultpfp.png"; // imagem padrão caso não tenha capa

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nome_db, $capa_db, $login, $email, $contacto);

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

    <form method="post" enctype="multipart/form-data" action="../scripts/sc_add_produto.php">

    <div class="text-center mb-4">
        <img src="../Imagens/pfp/<?php echo htmlspecialchars($capa); ?>" alt="Foto de perfil" class="rounded-circle border border-success imagempfp" style="object-fit: cover;">
        <h3 class="mt-2 verde_escuro"><a href="#">alterar foto</a></h3>
    </div>
    <h3 class="mb-3 verde_escuro" >Dados pessoais:</h3>


    <div class="mb-3">
        <label for="nome" class="form-label fw-bold verde_escuro">Nome:</label>
        <input type="text" value="<?php echo htmlspecialchars($nome_db); ?>" class="form-control bg-success bg-opacity-25" id="nome" name="nome" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label fw-bold verde_escuro">Email:</label>
        <input type="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control bg-success bg-opacity-25" id="email" name="email" required>
    </div>

    <!-- Contacto Telefónico -->
    <div class="mb-4">
        <label for="telefone" class="form-label fw-bold verde_escuro">Contacto telefónico:</label>
        <input type="tel" value="<?php echo htmlspecialchars($contacto); ?>" class="form-control bg-success bg-opacity-25" id="telefone" name="telefone" required>
    </div>
    </form>

    <form method="post" enctype="multipart/form-data" action="../scripts/sc_alterar_login.php">
    <h3 class="mb-3 verde_escuro" >Login e palavra-passe:</h3>

    <div class="mb-3">
        <label for="login" class="form-label fw-bold verde_escuro">login:</label>
        <input type="text" value="<?php echo htmlspecialchars($login); ?>" class="form-control bg-success bg-opacity-25" id="login" name="login" required>
    </div>

</main>
