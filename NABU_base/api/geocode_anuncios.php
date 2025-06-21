<?php
require_once '../Connections/connection.php';

header('Content-Type: text/plain');

$link = new_db_connection();

// Buscar anúncios sem coordenadas
$query = "SELECT id_anuncio, morada FROM anuncios WHERE latitude IS NULL OR longitude IS NULL";

if ($result = mysqli_query($link, $query)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id_anuncio = $row['id_anuncio'];
        $address = $row['morada'];

        $url = "https://nominatim.openstreetmap.org/search?" . http_build_query([
                'q' => $address,
                'format' => 'json',
                'limit' => 1
            ]);

        $options = ["http" => ["header" => "User-Agent: MeuAppGeocode/1.0\r\n"]];
        $context = stream_context_create($options);

        $response = file_get_contents($url, false, $context);

        $data = json_decode($response, true);

        if (!empty($data)) {
            $lat = $data[0]['lat'];
            $lng = $data[0]['lon'];

            // Atualizar o registo diretamente na tabela anuncios
            $stmt = mysqli_stmt_init($link);
            $update = "UPDATE anuncios SET latitude = ?, longitude = ? WHERE id_anuncio = ?";
            if (mysqli_stmt_prepare($stmt, $update)) {
                mysqli_stmt_bind_param($stmt, 'ddi', $lat, $lng, $id_anuncio);
                if (mysqli_stmt_execute($stmt)) {
                    echo "Coordenadas atualizadas para anúncio $id_anuncio: $lat, $lng\n";
                } else {
                    echo "Erro ao atualizar anúncio $id_anuncio\n";
                }
                mysqli_stmt_close($stmt);
            }
        } else {
            echo "Morada não encontrada para anúncio $id_anuncio: $address\n";
        }
    }
} else {
    echo "Erro na consulta SQL: " . mysqli_error($link);
}

mysqli_close($link);
