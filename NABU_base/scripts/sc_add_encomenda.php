<?php
require_once '../Connections/connection.php';
session_start();

$link = new_db_connection();

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['id_user'])) {
    die("Erro: Utilizador não autenticado.");
}

$ref_comprador = $_SESSION['id_user'];

// Obter dados do formulário
$ref_anuncio = (int) $_POST['id_anuncio'];
$quantidade = (int) $_POST['quantidade'];
$data_encomenda = date("Y-m-d H:i:s");
$ref_estado = 1; // Ex: 1 = "Pendente"

// Buscar o preço unitário do anúncio
$query_preco = "SELECT preco FROM anuncios WHERE id_anuncio = ?";
$stmt_preco = mysqli_prepare($link, $query_preco);
mysqli_stmt_bind_param($stmt_preco, "i", $ref_anuncio);
mysqli_stmt_execute($stmt_preco);
mysqli_stmt_bind_result($stmt_preco, $preco_unitario);

if (mysqli_stmt_fetch($stmt_preco)) {
    $preço = $preco_unitario * $quantidade;
    mysqli_stmt_close($stmt_preco);

    // Inserir encomenda
    $query = "INSERT INTO encomendas (data_encomenda, ref_comprador, ref_anuncio, quantidade, preço, ref_estado) 
              VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($link, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "siiidi",
            $data_encomenda,
            $ref_comprador,
            $ref_anuncio,
            $quantidade,
            $preço,
            $ref_estado
        );

        if (mysqli_stmt_execute($stmt)) {
            $_SESSION['mensagem_sistema'] = "Encomenda criada com sucesso!";
            $_SESSION['tipo_mensagem'] = "sucesso";
        } else {
            $_SESSION['mensagem_sistema'] = "Erro ao criar encomenda: " . mysqli_stmt_error($stmt);
            $_SESSION['tipo_mensagem'] = "erro";
        }

        mysqli_stmt_close($stmt);
    } else {
        $_SESSION['mensagem_sistema'] = "Erro na preparação da query: " . mysqli_error($link);
        $_SESSION['tipo_mensagem'] = "erro";
    }

    header("Location: ../Paginas/encomendas.php"); // redireciona para página apropriada
    exit();

} else {
    $_SESSION['mensagem_sistema'] = "Anúncio não encontrado.";
    $_SESSION['tipo_mensagem'] = "erro";
    header("Location: ../Paginas/encomendas.php");
    exit();
}

mysqli_close($link);
?>

