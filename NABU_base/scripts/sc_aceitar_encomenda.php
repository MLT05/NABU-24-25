<?php
header('Content-Type: application/json');

require_once '../Connections/connection.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_encomenda"])) {
    $id = intval($_POST['id_encomenda']); // segurança

    $conn = new_db_connection();
    $stmt = mysqli_stmt_init($conn);

    // Obter o ref_comprador da encomenda
    $query_user = "SELECT ref_comprador FROM encomendas WHERE id_encomenda = ?";
    $stmt_user = mysqli_stmt_init($conn);

    if (mysqli_stmt_prepare($stmt_user, $query_user)) {
        mysqli_stmt_bind_param($stmt_user, "i", $id);
        mysqli_stmt_execute($stmt_user);
        mysqli_stmt_bind_result($stmt_user, $ref_comprador);

        if (mysqli_stmt_fetch($stmt_user)) {
            mysqli_stmt_close($stmt_user);

            // Atualizar estado para 2 (Por recolher)
            $query = "UPDATE encomendas SET ref_estado = 2 WHERE id_encomenda = ?";
            if (mysqli_stmt_prepare($stmt, $query)) {
                mysqli_stmt_bind_param($stmt, "i", $id);

                if (mysqli_stmt_execute($stmt)) {
                    // Inserir notificação
                    $mensagem = "Um dos seus pedidos já pode ser recolhido.";
                    $notificacao = "INSERT INTO notificacoes (conteudo, users_id_user) VALUES (?, ?)";
                    $stmt_n = mysqli_stmt_init($conn);
                    if (mysqli_stmt_prepare($stmt_n, $notificacao)) {
                        mysqli_stmt_bind_param($stmt_n, "si", $mensagem, $ref_comprador);
                        mysqli_stmt_execute($stmt_n);
                        mysqli_stmt_close($stmt_n);
                    }

                    $response["success"] = true;
                    $response["novo_estado"] = "Por recolher";
                } else {
                    $response["error"] = "Erro ao executar a query de atualização.";
                }
                mysqli_stmt_close($stmt);
            } else {
                $response["error"] = "Erro ao preparar a query de atualização.";
            }
        } else {
            mysqli_stmt_close($stmt_user);
            $response["error"] = "Encomenda não encontrada.";
        }
    } else {
        $response["error"] = "Erro ao preparar a query de utilizador.";
    }

    mysqli_close($conn);
} else {
    $response["error"] = "Dados inválidos ou método incorreto.";
}

echo json_encode($response);
?>
