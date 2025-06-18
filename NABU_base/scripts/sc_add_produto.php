<?php
session_start();
require_once '../Connections/connection.php';
$link = new_db_connection();

// Verifica se o utilizador está autenticado e é role 1
if (!isset($_SESSION['user']) || $_SESSION["role"] != 1) {
    header("Location: ../Paginas/produto.php");
    exit;
}

// Recolha de dados do formulário
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
$capa = "default.png"; // Se quiseres tratar o upload real, posso ajudar

// Inserir na tabela `anuncios`
$query = "INSERT INTO anuncios (nome_produto, descricao, preco, ref_categoria, ref_user, localizacao, capa, data_insercao) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($link, $query);

if ($stmt) {
    mysqli_stmt_bind_param(
        $stmt,
        "ssdissss",
        $nome_produto,
        $descricao,
        $preco,
        $ref_categoria,
        $ref_user,
        $localizacao,
        $capa,
        $data_insercao
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../Paginas/perfil.php");
        exit;
    } else {
        echo "Erro ao inserir anúncio: " . mysqli_stmt_error($stmt);
    }
} else {
    echo "Erro na preparação da query: " . mysqli_error($link);
}
?>
