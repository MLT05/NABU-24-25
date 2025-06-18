<?php

require_once '../connections/connection.php';
session_start();

if (isset($_POST["login"]) && isset($_POST["password"])) {
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
                    // Guardar sessão de utilizador
                    session_start();
                    $_SESSION["login"] = $login;
                    $_SESSION["role"] = $perfil;
                    $_SESSION["id_user"] = $id_utilizador;

                    // Feedback de sucesso
                    header("Location: ../Paginas/index.php");
                } else {
                    // Password está errada
                    echo "Incorrect credentials!";
                    echo "<a href='../Paginas/login.php'>Try again</a>";
                }
            } else {
                // Username não existe
                echo "Incorrect credentials!";
                echo "<a href='../Paginas/login.php'>Try again</a>";
            }
        } else {
            // Acção de erro
            echo "Error:" . mysqli_stmt_error($stmt);
        }
    } else {
        // Acção de erro
        echo "Error:" . mysqli_error($link);
    }
    mysqli_stmt_close($stmt);
    mysqli_close($link);
} else {
    echo "Campos do formulário por preencher";
}
