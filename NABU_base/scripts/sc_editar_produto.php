<?php
require_once '../Connections/connection.php';


// Receber dados do POST
$id_anuncio = $_POST['id_anuncio'];
$titulo = $_POST['titulo'] ;
$preco = $_POST['preco'];
$medida = $_POST['medida'];
$categoria = $_POST['categoria'];
$descricao = $_POST['descricao'] ;
$localizacao = $_POST['localizacao'] ;
$nome = $_POST['nome'];
$email = $_POST['email'] ;
$telefone = $_POST['telefone'] ;
$id_user = $_POST['id_user'] ;
$data_insercao = date("Y-m-d H:i:s");

$link = new_db_connection();

if ($id_anuncio && $titulo && $preco !== null && $medida && $categoria && $descricao && $localizacao && $nome && $email && $telefone && $id_user) {

    // Atualizar dados do anúncio
    $query = "UPDATE anuncios 
          SET nome_produto = ?, descricao = ?, preco = ?, ref_categoria = ?, ref_user = ?, localizacao = ?, capa = ?, data_insercao = ?, ref_medida = ? 
          WHERE id_anuncio = ?";

    $stmt = mysqli_prepare($link, $query);

    mysqli_stmt_bind_param($stmt, "ssdiisssii",
        $titulo,
        $descricao,
        $preco,
        $categoria,     // <- ref_categoria
        $id_user,       // <- ref_user
        $localizacao,
        $capa,
        $data_insercao,
        $medida,        // <- ref_medida
        $id_anuncio
    );
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);

        // Atualizar dados do utilizador
        $query_user = "UPDATE users SET nome = ?, email = ?, contacto = ? WHERE id_user = ?";
        $stmt_user = mysqli_prepare($link, $query_user);

        mysqli_stmt_bind_param($stmt_user, "sssi", $nome, $email, $telefone, $id_user);

        if (mysqli_stmt_execute($stmt_user)) {
            mysqli_stmt_close($stmt_user);

            // Redirecionar para página ou mostrar sucesso
            header("Location: ../paginas/anuncio_editado_sucesso.php");
            echo "Anuncio editado com sucesso";
            exit();
        } else {
            echo "Erro ao atualizar utilizador: " . mysqli_error($link);
        }
    } else {
        echo "Erro ao atualizar anúncio: " . mysqli_error($link);
    }
} else {
    echo "Dados incompletos no formulário.";
}
?>
