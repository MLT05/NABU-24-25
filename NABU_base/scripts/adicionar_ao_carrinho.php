<?php
session_start();
require_once '../Connections/connection.php';



if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (!isset($_SESSION['id_user'])) {
        http_response_code(401); // Não autorizado
        echo 'nao_autenticado';
        exit;
    }

    $ref_user = $_SESSION['id_user'];
    $id_anuncio = $_POST['id_anuncio'] ?? null;
    $quantidade = $_POST['quantidade'] ?? null;



    if (!$id_anuncio || !$quantidade || $quantidade <= 0) {
        http_response_code(400);
        echo 'Quantidade inválida.';
        exit;
    }

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    // Verificar se o produto já está no carrinho para este usuário
    $checkQuery = "SELECT quantidade FROM carrinho WHERE ref_user = ? AND anuncios_id_anuncio = ?";
    if (mysqli_stmt_prepare($stmt, $checkQuery)) {
        mysqli_stmt_bind_param($stmt, "ii", $ref_user, $id_anuncio);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            // Produto já existe no carrinho
            echo "produto_existente";
            exit;
        }

    } else {
        http_response_code(500);
        echo "Erro na preparação da verificação.";
        exit;
    }
    // Buscar o preço atual do produto


    $query = "INSERT INTO carrinho (quantidade, ref_user, anuncios_id_anuncio) VALUES (?, ?, ?)";
    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, "dii", $quantidade, $ref_user, $id_anuncio);
        if (mysqli_stmt_execute($stmt)) {
            echo "ok";
        } else {
            http_response_code(500);
            echo "Erro ao inserir no carrinho.";
        }
    } else {
        http_response_code(500);
        echo "Erro na preparação da query.";
    }

    mysqli_stmt_close($stmt);
    mysqli_close($link);
}
?>

