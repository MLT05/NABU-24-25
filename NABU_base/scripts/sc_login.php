<?php

require_once '../connections/connection.php';
session_start();

if (!empty($_POST["login"]) && !empty($_POST["password"])) {

    $login = $_POST['login'];
    $password = $_POST['password'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT password_hash, ref_role, id_user FROM users WHERE login LIKE ?";

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 's', $login);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_bind_result($stmt, $password_hash, $perfil, $id_utilizador);

            if (mysqli_stmt_fetch($stmt)) {
                if (password_verify($password, $password_hash)) {
                    $_SESSION["login"] = $login;
                    $_SESSION["role"] = $perfil;
                    $_SESSION["id_user"] = $id_utilizador;
                    header("Location: ../Paginas/index.php");
                    exit();
                } else {
                    header("Location: ../Paginas/login.php?id_erro=2");
                    exit();
                }
            } else {
                header("Location: ../Paginas/login.php?id_erro=2");
                exit();
            }
        } else {
            header("Location: ../Paginas/login.php?id_erro=3"); // Erro interno
            exit();
        }
    } else {
        header("Location: ../Paginas/login.php?id_erro=3"); // Erro interno
        exit();
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    header("Location: ../Paginas/login.php?id_erro=1");
    exit();
}
