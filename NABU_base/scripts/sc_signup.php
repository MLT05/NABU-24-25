<?php
require_once '../Connections/connection.php';

if (
    !empty($_POST["nome"]) &&
    !empty($_POST["email"]) &&
    !empty($_POST["contacto"]) &&
    !empty($_POST["login"]) &&
    !empty($_POST["password"])
) {
    $nome = htmlspecialchars(trim($_POST['nome']));
    $email = htmlspecialchars(trim($_POST['email']));
    $contacto = htmlspecialchars(trim($_POST['contacto']));
    $login = htmlspecialchars(trim($_POST['login']));
    $password = $_POST['password'];

    // Validações básicas
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../Paginas/signup.php?id_erro=4"); // email inválido
        exit();
    }

    if (strlen($password) < 8) {
        header("Location: ../Paginas/signup.php?id_erro=5"); // senha curta
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Verifica se login existe
    $query_login = "SELECT id_user FROM users WHERE login = ?";
    if (mysqli_stmt_prepare($stmt, $query_login)) {
        mysqli_stmt_bind_param($stmt, 's', $login);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../Paginas/signup.php?id_erro=2"); // login já existe
            exit();
        }
        mysqli_stmt_free_result($stmt);
    } else {
        header("Location: ../Paginas/signup.php?id_erro=3"); // erro interno
        exit();
    }

    // Verifica se email ou contacto existem
    $query_email_contacto = "SELECT id_user FROM users WHERE email = ? OR contacto = ?";
    if (mysqli_stmt_prepare($stmt, $query_email_contacto)) {
        mysqli_stmt_bind_param($stmt, 'ss', $email, $contacto);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../Paginas/signup.php?id_erro=6"); // email ou contacto já existe
            exit();
        }
        mysqli_stmt_free_result($stmt);
    } else {
        header("Location: ../Paginas/signup.php?id_erro=3"); // erro interno
        exit();
    }

    // Inserção
    $insert_query = "INSERT INTO users (nome, email, contacto, login, password_hash) VALUES (?, ?, ?, ?, ?)";
    if (mysqli_stmt_prepare($stmt, $insert_query)) {
        mysqli_stmt_bind_param($stmt, 'sssss', $nome, $email, $contacto, $login, $password_hash);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../Paginas/login.php");
            exit();
        } else {
            header("Location: ../Paginas/signup.php?id_erro=3"); // erro interno ao inserir
            exit();
        }
    } else {
        header("Location: ../Paginas/signup.php?id_erro=3"); // erro interno na preparação
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);

} else {
    header("Location: ../Paginas/signup.php?id_erro=1"); // campos vazios
    exit();
}
