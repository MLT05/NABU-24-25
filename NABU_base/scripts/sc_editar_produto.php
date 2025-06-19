<?php
session_start();
require_once '../Connections/connection.php';

if (!isset($_SESSION['login']) || $_SESSION["role"] != 1) {
    header("Location: ../../login.php");
    exit;
}

$id_produto = $_POST['id_produto'];
$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$preco = $_POST['preco'];
$ref_categoria = $_POST['categoria'];
$ref_medida = $_POST['medida'];
$localizacao = $_POST['localizacao'];

$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];

$link = new_db_connection();

$query = "UPDATE produtos SET nome_produto = ?, descricao = ?, preco = ?, ref_categoria = ?, ref_medida = ?, localizacao = ? WHERE id_produto = ?";

$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "ssdissi", $titulo, $descricao, $preco, $ref_categoria, $ref_medida, $localizacao, $id_produto);

if (mysqli_stmt_execute($stmt)) {
    // Atualizar dados do utilizador associado
    $query_user = "UPDATE users SET nome = ?, email = ?, contacto = ? WHERE id_user = (SELECT ref_user FROM produtos WHERE id_produto = ?)";
    $stmt_user = mysqli_prepare($link, $query_user);
    mysqli_stmt_bind_param($stmt_user, "sssi", $nome, $email, $telefone, $id_produto);
    mysqli_stmt_execute($stmt_user);
    mysqli_stmt_close($stmt_user);

    header("Location: ../../produtos.php");
    exit;
} else {
    echo "Erro ao atualizar produto: " . mysqli_error($link);
}

mysqli_stmt_close($stmt);
mysqli_close($link);
