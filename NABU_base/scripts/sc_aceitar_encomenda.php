<?php
header('Content-Type: application/json');

require_once '../Connections/connection.php';

$response = ["success" => false];

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["id_encomenda"])) {
    $id = intval($_POST['id_encomenda']); // segurança

    $conn = new_db_connection();
    $stmt = mysqli_stmt_init($conn);

    // Atualizar estado para 2 (Por recolher)
    $query = "UPDATE encomendas SET ref_estado = 2 WHERE id_encomenda = ?";

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "i", $id);

        if (mysqli_stmt_execute($stmt)) {
            $response["success"] = true;
            $response["novo_estado"] = "Por recolher";
        } else {
            $response["error"] = "Erro ao executar a query.";
        }

        mysqli_stmt_close($stmt);
    } else {
        $response["error"] = "Erro ao preparar a query.";
    }

    mysqli_close($conn);
} else {
    $response["error"] = "Dados inválidos ou método incorreto.";
}

echo json_encode($response);
?>
