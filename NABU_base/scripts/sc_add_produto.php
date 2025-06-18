<?php
session_start();
require_once '../Connections/connection.php';
$link = new_db_connection();

if (!$link) {
    die("Erro na ligação à base de dados");
}

// Verifica se o utilizador está autenticado e tem role = 1
if (!isset($_SESSION['user']) || $_SESSION["role"] != 1) {
    header("Location: ../Paginas/produto.php");
    exit;
}

$nome_produto = $_POST['titulo'];
$descricao = $_POST['descricao'];
$preco = floatval(str_replace(',', '.', $_POST['preco']));
$ref_categoria = (int) $_POST['categoria'];
$ref_user = (int) $_SESSION['user'];
$localizacao = $_POST['localizacao'];
$nome_contato = $_POST['nome'];
$email_contato = $_POST['email'];
$telefone_contato = $_POST['telefone'];
$data_insercao = date("Y-m-d H:i:s");
$capa = "default.png";
$ref_user = $_SESSION ['role'];
$ref_media = $_POST ['media'];

$query = "INSERT INTO filmes (nome_produto, descricao, preco, ref_categoria, ref_user, localizacao, capa, data_insercao, ref_media) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($link, $query);
mysqli_stmt_bind_param($stmt, "sssissssis", $nome_produto, $descricao, $preco, $ref_categoria, $ref_user, $localizacao, $capa, $data_insercao, $ref_media);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../Paginas/perfil.php");
    exit;

} else {
    echo "Erro ao inserir filme: " . mysqli_error($link);
}