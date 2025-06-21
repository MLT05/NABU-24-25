<?php
header('Content-Type: application/json'); // ✅ Diz explicitamente que a resposta é JSON
require_once '../Connections/connection.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['id_encomenda'])) {
    $id_encomenda = intval($_POST['id_encomenda']); // ✅ Sanitiza o input
    $conn = new_db_connection();

    // Obter o ref_comprador da encomenda
    $query_user = "SELECT ref_comprador FROM encomendas WHERE id_encomenda = ?";
    $stmt_user = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt_user, $query_user)) {
        mysqli_stmt_bind_param($stmt_user, "i", $id_encomenda);
        mysqli_stmt_execute($stmt_user);
        mysqli_stmt_bind_result($stmt_user, $ref_comprador);

        if (mysqli_stmt_fetch($stmt_user)) {
            mysqli_stmt_close($stmt_user);

            // Apagar encomenda
            $query_delete = "DELETE FROM encomendas WHERE id_encomenda = ?";
            $stmt = mysqli_stmt_init($conn);

            if (mysqli_stmt_prepare($stmt, $query_delete)) {
                mysqli_stmt_bind_param($stmt, "i", $id_encomenda);
                if (mysqli_stmt_execute($stmt)) {

                    // Inserir notificação
                    $mensagem = "Uma das suas encomendas foi rejeitada";
                    $notificacao = "INSERT INTO notificacoes (conteudo, users_id_user) VALUES (?, ?)";
                    $stmt_n = mysqli_stmt_init($conn);
                    if (mysqli_stmt_prepare($stmt_n, $notificacao)) {
                        mysqli_stmt_bind_param($stmt_n, "si", $mensagem, $ref_comprador);
                        mysqli_stmt_execute($stmt_n);
                        mysqli_stmt_close($stmt_n);
                    }

                    $response["success"] = true;
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            mysqli_stmt_close($stmt_user);
            $response["error"] = "Encomenda não encontrada.";
        }
    } else {
        $response["error"] = "Erro ao preparar o SELECT.";
    }

    mysqli_close($conn);
} else {
    $response["error"] = "Dados inválidos ou método incorreto.";
}

echo json_encode($response);
?>
