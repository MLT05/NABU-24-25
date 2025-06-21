<?php
session_start();
require_once '../Connections/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['ref_remetente'], $_POST['ref_destinatario'], $_POST['ref_produto'], $_POST['mensagem']) &&
        is_numeric($_POST['ref_remetente']) &&
        is_numeric($_POST['ref_destinatario']) &&
        is_numeric($_POST['ref_produto']) &&
        !empty(trim($_POST['mensagem']))
    ) {
        $ref_remetente = (int) $_POST['ref_remetente'];
        $ref_destinatario = (int) $_POST['ref_destinatario'];
        $ref_produto = (int) $_POST['ref_produto'];
        $mensagem = trim($_POST['mensagem']);

        $link = new_db_connection();
        $stmt = mysqli_stmt_init($link);

        $query = "INSERT INTO mensagens (ref_remetente, ref_destinatario, ref_produto, mensagem, data_envio)
                  VALUES (?, ?, ?, ?, NOW())";

        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, "iiis", $ref_remetente, $ref_destinatario, $ref_produto, $mensagem);
            if (mysqli_stmt_execute($stmt)) {
                // Redirecionar de volta para a página de detalhes da conversa
                header("Location: ../Paginas/mensagens_details.php?id_anuncio=$ref_produto&id_outro_user=$ref_destinatario");
                exit();
            } else {
                echo "Erro ao enviar mensagem.";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Erro ao preparar a inserção.";
        }

        mysqli_close($link);
    } else {
        echo "Dados inválidos.";
    }
} else {
    header("Location: ../Paginas/mensagens.php");
    exit();
}

session_start();
require_once '../Connections/connection.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (
        isset($_POST['ref_remetente'], $_POST['ref_destinatario'], $_POST['ref_produto'], $_POST['mensagem']) &&
        is_numeric($_POST['ref_remetente']) &&
        is_numeric($_POST['ref_destinatario']) &&
        is_numeric($_POST['ref_produto']) &&
        !empty(trim($_POST['mensagem']))
    ) {
        $ref_remetente = (int)$_POST['ref_remetente'];
        $ref_destinatario = (int)$_POST['ref_destinatario'];
        $ref_produto = (int)$_POST['ref_produto'];
        $mensagem = trim($_POST['mensagem']);

        $link = new_db_connection();
        $stmt = mysqli_stmt_init($link);

        $query = "INSERT INTO mensagens (ref_remetente, ref_destinatario, ref_produto, mensagem, data_envio)
                  VALUES (?, ?, ?, ?, NOW())";

        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, "iiis", $ref_remetente, $ref_destinatario, $ref_produto, $mensagem);
            if (mysqli_stmt_execute($stmt)) {
                // Redirecionar de volta para a página de detalhes da conversa
                header("Location: ../Paginas/mensagens_details.php?id_anuncio=$ref_produto&id_outro_user=$ref_destinatario");
                exit();
            } else {
                echo "Erro ao enviar mensagem.";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "Erro ao preparar a inserção.";
        }

        mysqli_close($link);
    } else {
        echo "Dados inválidos.";
    }
} else {
    header("Location: ../Paginas/mensagens.php");
    exit();
}
