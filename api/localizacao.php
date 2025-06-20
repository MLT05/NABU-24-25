<?php

// We need the function!
require_once '../Connections/connection.php';

// Create a new DB connection
$link = new_db_connection();

/* create a prepared statement */
$stmt = mysqli_stmt_init($link);

// Mostrar localização do produto
$query = "SELECT id_locations, description, lng, lat FROM locations";

mysqli_stmt_prepare($stmt, $query);

/* execute the prepared statement */
mysqli_stmt_execute($stmt);

mysqli_stmt_bind_result($stmt, $id_locations, $description, $lng, $lat);
$response = array();
while (mysqli_stmt_fetch($stmt)) {
    $location = array(
        "id" => $id_locations,
        "description" => $description,
        "lng" => $lng,
        "lat" => $lat
    );
    $response[] = $location;
}

echo json_encode($response);