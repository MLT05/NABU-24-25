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

    // Validação de email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        header("Location: ../Paginas/signup.php?id_erro=4");
        exit();
    }

    // Validação da password
    if (strlen($password) < 8) {
        header("Location: ../Paginas/signup.php?id_erro=5");
        exit();
    }

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    // Escolher imagem de perfil aleatória
    $default_pfps = ['default2.png', 'default3.png', 'default4.png'];
    $pfp_aleatoria = $default_pfps[array_rand($default_pfps)];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Verifica se login já existe
    $query_login = "SELECT id_user FROM users WHERE login = ?";
    if (mysqli_stmt_prepare($stmt, $query_login)) {
        mysqli_stmt_bind_param($stmt, 's', $login);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../Paginas/signup.php?id_erro=2");
            exit();
        }
        mysqli_stmt_free_result($stmt);
    } else {
        header("Location: ../Paginas/signup.php?id_erro=3");
        exit();
    }

    // Verifica se email ou contacto já existem
    $query_email_contacto = "SELECT id_user FROM users WHERE email = ? OR contacto = ?";
    if (mysqli_stmt_prepare($stmt, $query_email_contacto)) {
        mysqli_stmt_bind_param($stmt, 'ss', $email, $contacto);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../Paginas/signup.php?id_erro=6");
            exit();
        }
        mysqli_stmt_free_result($stmt);
    } else {
        header("Location: ../Paginas/signup.php?id_erro=3");
        exit();
    }

    // Inserção com imagem de perfil aleatória
    $insert_query = "INSERT INTO users (nome, email, contacto, login, password_hash, pfp) VALUES (?, ?, ?, ?, ?, ?)";
    if (mysqli_stmt_prepare($stmt, $insert_query)) {
        mysqli_stmt_bind_param($stmt, 'ssssss', $nome, $email, $contacto, $login, $password_hash, $pfp_aleatoria);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../Paginas/login.php?id_sucesso=1");
            exit();
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../Paginas/signup.php?id_erro=3");
            exit();
        }
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        header("Location: ../Paginas/signup.php?id_erro=3");
        exit();
    }

} else {
    header("Location: ../Paginas/signup.php?id_erro=1");
    exit();
}
