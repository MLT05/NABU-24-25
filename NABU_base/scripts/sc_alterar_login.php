<?php
session_start();
require_once '../connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit;
}

if (!empty($_POST['login'])) {
    $id_user = $_SESSION['id_user'];
    $novo_login = htmlspecialchars(trim($_POST['login']));
    $password = $_POST['password'] ?? '';

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Buscar login atual
    $query_get_login = "SELECT login FROM users WHERE id_user = ?";
    if (mysqli_stmt_prepare($stmt, $query_get_login)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $login_atual);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    } else {
        header("Location: ../Paginas/perfil_details.php?msg2=erro_bd");
        exit();
    }

    // Só valida login se for diferente do atual
    if ($novo_login !== $login_atual) {
        // Verifica se o novo login já existe (exceto o próprio)
        $stmt = mysqli_stmt_init($link);
        $query_login = "SELECT id_user FROM users WHERE login = ? AND id_user != ?";
        if (mysqli_stmt_prepare($stmt, $query_login)) {
            mysqli_stmt_bind_param($stmt, 'si', $novo_login, $id_user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            if (mysqli_stmt_num_rows($stmt) > 0) {
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                header("Location: ../Paginas/perfil_details.php?msg2=login_ja_usado");
                exit();
            }
            mysqli_stmt_free_result($stmt);
            mysqli_stmt_close($stmt);
        } else {
            mysqli_close($link);
            header("Location: ../Paginas/perfil_details.php?msg2=erro_bd");
            exit();
        }
    }

    // Se password preenchida, valida e atualiza login + password
    if (!empty($password)) {
        if (strlen($password) < 8) {
            mysqli_close($link);
            header("Location: ../Paginas/perfil_details.php?msg2=senha_curta");
            exit();
        }
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Atualiza login e password
        $query_update = "UPDATE users SET login = ?, password_hash = ? WHERE id_user = ?";
        $stmt = mysqli_stmt_init($link);
        if (mysqli_stmt_prepare($stmt, $query_update)) {
            mysqli_stmt_bind_param($stmt, 'ssi', $novo_login, $password_hash, $id_user);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                header("Location: ../Paginas/perfil_details.php?msg2=sucesso");
                exit();
            } else {
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                header("Location: ../Paginas/perfil_details.php?msg2=erro_bd");
                exit();
            }
        } else {
            mysqli_close($link);
            header("Location: ../Paginas/perfil_details.php?msg2=erro_bd");
            exit();
        }
    } else {
        // Password vazia — atualiza login só se for diferente do atual
        if ($novo_login !== $login_atual) {
            $query_update = "UPDATE users SET login = ? WHERE id_user = ?";
            $stmt = mysqli_stmt_init($link);
            if (mysqli_stmt_prepare($stmt, $query_update)) {
                mysqli_stmt_bind_param($stmt, 'si', $novo_login, $id_user);
                if (mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);
                    header("Location: ../Paginas/perfil_details.php?msg2=sucesso");
                    exit();
                } else {
                    mysqli_stmt_close($stmt);
                    mysqli_close($link);
                    header("Location: ../Paginas/perfil_details.php?msg2=erro_bd");
                    exit();
                }
            } else {
                mysqli_close($link);
                header("Location: ../Paginas/perfil_details.php?msg2=erro_bd");
                exit();
            }
        } else {
            // Não houve alteração no login nem password
            mysqli_close($link);
            header("Location: ../Paginas/perfil_details.php?msg2=sem_alteracao");
            exit();
        }
    }

} else {
    header("Location: ../Paginas/perfil_details.php?msg2=campos_vazios");
    exit();
}
