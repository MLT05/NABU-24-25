<?php
require_once '../Connections/connection.php';

if (!isset($_SESSION['login']) || $_SESSION["role"] != 1) {
    header("Location: ../Paginas/login.php");
    exit;
}

// Recolher dados do formulário
$id_produto = $_POST['id_produto'];
$titulo = $_POST['titulo'];
$descricao = $_POST['descricao'];
$preco = $_POST['preco'];
$localizacao = $_POST['localizacao'];
$categoria = $_POST['categoria'];
$medida = $_POST['medida'];

// Dados de contacto
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];

$link = new_db_connection();

// Atualizar os dados do produto (assumindo que a tabela se chama 'filmes' — altera para 'produtos' se necessário)
$query = "UPDATE anucios 
          SET nome_produto = ?, descricao = ?, preco = ?, localizacao = ?, ref_categoria = ?, ref_medida = ?, nome_contacto = ?, email_contacto = ?, telefone_contacto = ?
          WHERE id_filmes = ?";

$stmt = mysqli_prepare($link, $query);

mysqli_stmt_bind_param($stmt, "ssdsiisssi", $titulo, $descricao, $preco, $localizacao, $categoria, $medida, $nome, $email, $telefone, $id_produto);

if (mysqli_stmt_execute($stmt)) {
    header("Location: ../../produto.php");
    exit;
} else {
    echo "Erro ao atualizar produto: " . mysqli_error($link);
}
?>