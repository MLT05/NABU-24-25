<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

require_once '../Connections/connection.php';
session_start();

if (!isset($_SESSION['id_user'])) {
    http_response_code(401);
    echo json_encode(["success" => false, "mensagem" => "Utilizador não autenticado."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "mensagem" => "Método não permitido."]);
    exit;
}

if (!isset($_POST['id_anuncio'], $_POST['quantidade'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "mensagem" => "Dados incompletos."]);
    exit;
}

$id_anuncio = (int)$_POST['id_anuncio'];
$quantidade = (float)$_POST['quantidade'];

if ($quantidade <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "mensagem" => "Quantidade inválida."]);
    exit;
}

$link = new_db_connection();

$query = "SELECT preco, ref_medida FROM anuncios WHERE id_anuncio = ?";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $preco_unitario, $ref_medida);

if (!mysqli_stmt_fetch($stmt)) {
    mysqli_stmt_close($stmt);
    mysqli_close($link);
    echo json_encode(["success" => false, "mensagem" => "Anúncio não encontrado."]);
    exit;
}
mysqli_stmt_close($stmt);

$ref_comprador = $_SESSION['id_user'];
$data_encomenda = date("Y-m-d H:i:s");
$ref_estado = 1;
$preco_total = $quantidade * $preco_unitario;

$insert_query = "
    INSERT INTO encomendas (data_encomenda, ref_comprador, ref_anuncio, quantidade, preco, ref_medida, ref_estado) 
    VALUES (?, ?, ?, ?, ?, ?, ?)
";

$insert_stmt = mysqli_prepare($link, $insert_query);
mysqli_stmt_bind_param($insert_stmt, "siiddii", $data_encomenda, $ref_comprador, $id_anuncio, $quantidade, $preco_total, $ref_medida, $ref_estado);

if (mysqli_stmt_execute($insert_stmt)) {
    echo json_encode(["success" => true, "mensagem" => "Pedido realizado com sucesso!"]);
} else {
    echo json_encode(["success" => false, "mensagem" => "Erro ao realizar pedido: " . mysqli_stmt_error($insert_stmt)]);
}
mysqli_stmt_close($insert_stmt);
mysqli_close($link);

