<?php
require_once '../Connections/connection.php';

// Aqui deves validar sessão e permissões se for necessário
// Exemplo:
// if (!isset($_SESSION['login']) || $_SESSION["role"] != 1) {
//     header("Location: ../login.php");
//     exit;
// }
$id_anuncio = 18;
// Receber dados do POST
$id_anuncio = $_POST['id_produto'] ;
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

$link = new_db_connection();

if ($id_anuncio && $titulo && $preco !== null && $medida && $categoria && $descricao && $localizacao && $nome && $email && $telefone && $id_user) {

    // Atualizar dados do anúncio
    $query = "UPDATE anuncios 
              SET nome_produto = ?, preco = ?, ref_medida = ?, ref_categoria = ?, descricao = ?, localizacao = ? 
              WHERE id_anuncio = ?";
    $stmt = mysqli_prepare($link, $query);

    mysqli_stmt_bind_param($stmt, "sdisssi", $titulo, $preco, $medida, $categoria, $descricao, $localizacao, $id_anuncio);

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
