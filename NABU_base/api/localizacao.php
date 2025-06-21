<?php

// We need the function!
require_once '../Connections/connection.php';

// Create a new DB connection
$link = new_db_connection();

/* create a prepared statement */
$stmt = mysqli_stmt_init($link);

// Mostrar localização do produto
$query = "SELECT latitude, longitude, localizacao FROM anuncios";

if (mysqli_stmt_prepare($stmt, $query)) {

    /* execute the prepared statement */
    mysqli_stmt_execute($stmt);

    // Bind the result variables
    mysqli_stmt_bind_result($stmt, $latitude, $longitude, $morada);

    $response = array();

    while (mysqli_stmt_fetch($stmt)) {
        $location = array(
            "morada" => $morada,
            "lng" => $longitude, // longitude vai para lng
            "lat" => $latitude,  // latitude vai para lat
        );
        $response[] = $location;
    }

    echo json_encode($response);
} else {
    echo json_encode(["error" => "Erro na preparação da query."]);
}

mysqli_stmt_close($stmt);
mysqli_close($link);
