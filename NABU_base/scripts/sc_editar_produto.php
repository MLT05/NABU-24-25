<?php
require_once '../Connections/connection.php';

$id_anuncio = $_POST['id_anuncio'];
$titulo = $_POST['titulo'];
$preco = $_POST['preco'];
$medida = $_POST['medida'];
$categoria = $_POST['categoria'];
$descricao = $_POST['descricao'];
$localizacao = $_POST['localizacao'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$id_user = $_POST['id_user'];
$data_insercao = date("Y-m-d H:i:s");

$link = new_db_connection();

// Buscar a capa atual do anúncio
$query_capa = "SELECT capa FROM anuncios WHERE id_anuncio = ?";
$stmt_capa = mysqli_prepare($link, $query_capa);
mysqli_stmt_bind_param($stmt_capa, "i", $id_anuncio);
mysqli_stmt_execute($stmt_capa);
mysqli_stmt_bind_result($stmt_capa, $capa_atual);
mysqli_stmt_fetch($stmt_capa);
mysqli_stmt_close($stmt_capa);

// Verificar se um novo arquivo foi enviado
if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
    $upload_dir = "../uploads/";
    $file_tmp = $_FILES['capa']['tmp_name'];
    $file_name = basename($_FILES['capa']['name']);
    $target_file = $upload_dir . $file_name;

    if (move_uploaded_file($file_tmp, $target_file)) {
        $capa = $file_name; // Salva o novo nome do arquivo
    } else {
        echo "Erro ao mover o arquivo.";
        exit();
    }
} else {
    $capa = $capa_atual; // Mantém a imagem atual
}

if ($id_anuncio && $titulo && $preco !== null && $medida && $categoria && $descricao && $localizacao && $nome && $email && $telefone && $id_user) {
    $query = "UPDATE anuncios 
        SET nome_produto = ?, descricao = ?, preco = ?, ref_categoria = ?, ref_user = ?, localizacao = ?, capa = ?, data_insercao = ?, ref_medida = ?, nome = ?, email = ?, contacto = ?
        WHERE id_anuncio = ?";

    $stmt = mysqli_prepare($link, $query);

    mysqli_stmt_bind_param($stmt, "ssdiissssssii",
        $titulo,
        $descricao,
        $preco,
        $categoria,
        $id_user,
        $localizacao,
        $capa,
        $data_insercao,
        $medida,
        $nome,
        $email,
        $telefone,
        $id_anuncio
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../paginas/meus_anuncios.php");
        exit();
    } else {
        echo "Erro ao atualizar anúncio: " . mysqli_error($link);
    }
} else {
    echo "Dados incompletos no formulário.";
}
?>
