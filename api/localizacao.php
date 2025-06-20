<?php
header('Content-Type: application/json');
// We need the function!
require_once '../Connections/connection.php';

// Create a new DB connection
$link = new_db_connection();

/* create a prepared statement */
$stmt = mysqli_stmt_init($link);

// Mostrar localização do produto
$query = "SELECT id_localizacao, latitude, longitude, descricao FROM localizacao";

mysqli_stmt_prepare($stmt, $query);

/* execute the prepared statement */
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result($stmt, $id_localizacao, $latitude, $longitude, $descricao);
$response = array();
while (mysqli_stmt_fetch($stmt)) {
    $location = array(
        "id" => $id_localizacao,
        "description" => $descricao,
        "lng" => $latitude,
        "lat" => $longitude,
    );
    $response[] = $location;
}

echo json_encode($response);