<?php

// We need the function!
require_once '../Connections/connection.php';

// Create a new DB connection
$link = new_db_connection();

/* create a prepared statement */
$stmt = mysqli_stmt_init($link);

// Mostrar localização do produto
$query = "SELECT a.id_anuncio, a.localizacao, a.latitude, a.longitude, a.nome_produto, a.preco, m.abreviatura, a.capa
          FROM anuncios a 
          JOIN medidas m ON a.ref_medida = m.id_medida 
          WHERE a.latitude IS NOT NULL AND a.longitude IS NOT NULL";

if (mysqli_stmt_prepare($stmt, $query)) {

    /* execute the prepared statement */
    mysqli_stmt_execute($stmt);

    // Bind the result variables
    mysqli_stmt_bind_result(
        $stmt,
        $id_anuncio,
        $localizacao,
        $latitude,
        $longitude,
        $nome_produto,
        $preco,
        $ref_medida,
        $capa
    );

    $response = array();

    while (mysqli_stmt_fetch($stmt)) {
        $location = array(
            "localizacao" => $localizacao,
            "lng" => $longitude, // longitude vai para lng
            "lat" => $latitude,  // latitude vai para lat
            "nome_produto" => $nome_produto,
            "preco" => $preco,
            "ref_medida" => $ref_medida,
            "id" => $id_anuncio,
            "capa" => $capa

        );
        $response[] = $location;
    }

    echo json_encode($response);
} else {
    echo json_encode(["error" => "Erro na preparação da query."]);
}

mysqli_stmt_close($stmt);
mysqli_close($link);
