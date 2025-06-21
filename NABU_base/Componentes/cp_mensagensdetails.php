<?php
require_once '../Connections/connection.php';

//if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_anuncio']) && is_numeric($_POST['id_anuncio'])) {

    $id_user = $_SESSION['id_user'];
    $id_anuncio = $_POST['id_anuncio'];

    $link = new_db_connection();

    $stmt = mysqli_stmt_init($link);

    $query = "SELECT a.nome_produto, a.preco, u.nome, u.id_user, a.capa, m.descricao AS medida_desc, m.abreviatura 
          FROM anuncios a
          INNER JOIN users u ON a.ref_user = u.id_user
          INNER JOIN medidas m ON a.ref_medida = m.id_medida
          WHERE a.id_anuncio = ?";

    if (!mysqli_stmt_prepare($stmt, $query)) {
        echo "Erro na preparação da query.";
        exit;
    }

    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
    mysqli_stmt_execute($stmt);

    mysqli_stmt_bind_result($stmt, $nome_produto,$preco, $nome_user, $id_user, $capa, $medida_desc, $medida_abr);

    if ($medida_abr == "UN") {
        $min_medida = 1;
    } else {
        $min_medida = 0.05;
    }


//} else {
   // header("Location: ../Paginas/mensagens.php");
 //   exit();
//}