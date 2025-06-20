<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once '../Connections/connection.php';
session_start();

$link = new_db_connection();

if (!isset($_SESSION['id_user'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "mensagem" => "Utilizador não autenticado."]);
    exit;
}

$ref_comprador = $_SESSION['id_user'];
$data_encomenda = date("Y-m-d H:i:s");
$ref_estado = 1;

// 1. Buscar itens do carrinho e guardar em array
$query_carrinho = "
SELECT 
    carrinho.anuncios_id_anuncio, 
    carrinho.quantidade,
    anuncios.preco,
    anuncios.ref_medida
FROM carrinho 
INNER JOIN anuncios ON carrinho.anuncios_id_anuncio = anuncios.id_anuncio
WHERE carrinho.ref_user = ?
";

$stmt = mysqli_prepare($link, $query_carrinho);
mysqli_stmt_bind_param($stmt, "i", $ref_comprador);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_anuncio, $quantidade, $preco_unitario, $ref_medida);

$itens_carrinho = [];

while (mysqli_stmt_fetch($stmt)) {
    $itens_carrinho[] = [
        "id_anuncio" => $id_anuncio,
        "quantidade" => $quantidade,
        "preco_unitario" => $preco_unitario,
        "ref_medida" => $ref_medida
    ];
}
mysqli_stmt_close($stmt); // <- ESSENCIAL

// 2. Inserir cada encomenda
$encomendas_criadas = 0;

foreach ($itens_carrinho as $item) {
    $preco_total = $item['quantidade'] * $item['preco_unitario'];

    $insert_query = "
    INSERT INTO encomendas (data_encomenda, ref_comprador, ref_anuncio, quantidade, preco, ref_medida, ref_estado) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
    ";
    $insert_stmt = mysqli_prepare($link, $insert_query);

    if ($insert_stmt) {
        mysqli_stmt_bind_param($insert_stmt, "siiddii",
            $data_encomenda,
            $ref_comprador,
            $item['id_anuncio'],
            $item['quantidade'],
            $preco_total,
            $ref_medida,
            $ref_estado
        );

        if (mysqli_stmt_execute($insert_stmt)) {
            $encomendas_criadas++;
        } else {
            echo json_encode(["success" => false, "mensagem" => "Erro ao inserir: " . mysqli_stmt_error($insert_stmt)]);
            exit;
        }

        mysqli_stmt_close($insert_stmt);
    } else {
        echo json_encode(["success" => false, "mensagem" => "Erro na preparação da query: " . mysqli_error($link)]);
        exit;
    }
}

// 3. Apagar carrinho
if ($encomendas_criadas > 0) {
    $delete_stmt = mysqli_prepare($link, "DELETE FROM carrinho WHERE ref_user = ?");
    mysqli_stmt_bind_param($delete_stmt, "i", $ref_comprador);
    mysqli_stmt_execute($delete_stmt);
    mysqli_stmt_close($delete_stmt);
}

mysqli_close($link);

echo json_encode([
    "success" => true,
    "mensagem" => "$encomendas_criadas encomenda(s) criada(s) com sucesso."
]);
