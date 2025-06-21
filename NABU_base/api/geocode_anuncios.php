<?php




$link = new_db_connection();

// Buscar anúncios sem coordenadas
$query = "SELECT id_anuncio, localizacao FROM anuncios 
WHERE latitude IS NULL OR longitude IS NULL 
   OR latitude = 0 OR longitude = 0;";

if ($result = mysqli_query($link, $query)) {
    while ($row = mysqli_fetch_assoc($result)) {
        $id_anuncio = $row['id_anuncio'];
        $address = $row['localizacao'];

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

                } else {

                }
                mysqli_stmt_close($stmt);
            }
        } else {

        }
    }
} else {

}

mysqli_close($link);
