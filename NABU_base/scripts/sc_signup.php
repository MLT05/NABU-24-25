<?php
require_once '../Connections/connection.php';

if (isset($_POST["nome"], $_POST["email"], $_POST["contacto"], $_POST["login"], $_POST["password"])) {
    $nome = htmlspecialchars(trim($_POST['nome']));
    $email = htmlspecialchars(trim($_POST['email']));
    $contacto = htmlspecialchars(trim($_POST['contacto']));
    $login = htmlspecialchars(trim($_POST['login']));
    $password = $_POST['password'];

    // Validações básicas
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Email inválido.";
        exit();
    }

    if (strlen($password) < 8) {
        echo "A password deve ter pelo menos 8 caracteres.";
        exit();
    }


    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Verifica duplicados
    $check_query = "SELECT id_user FROM users WHERE email = ? OR login = ?";
    if (mysqli_stmt_prepare($stmt, $check_query)) {
        mysqli_stmt_bind_param($stmt, 'ss', $email, $login);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            echo "Email ou nome de utilizador já existem.";
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            exit();
        }
        mysqli_stmt_free_result($stmt);
    }

    // Inserção
    $insert_query = "INSERT INTO users (nome, email, contacto, login, password_hash) VALUES (?, ?, ?, ?, ?)";
    if (mysqli_stmt_prepare($stmt, $insert_query)) {
        mysqli_stmt_bind_param($stmt, 'ssiss', $nome, $email, $contacto, $login, $password_hash);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../login.php");
            exit();
        } else {
            echo "Erro ao inserir utilizador: " . mysqli_stmt_error($stmt);
            header("Location: ../sign.php");
            exit();
        }
    } else {
        echo "Erro na preparação do statement: " . mysqli_error($link);
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    echo "Campos do formulário por preencher.";
}
?>
