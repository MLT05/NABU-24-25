<?php
require_once '../Connections/connection.php';
$link = new_db_connection();

// Dados do formulário
$ref_user =1;
$nome_produto = $_POST['titulo'];
$descricao = $_POST['descricao'];
$preco = floatval(str_replace(',', '.', $_POST['preco']));
$ref_categoria = (int) $_POST['categoria'];
$ref_medida = (int) $_POST['medida'];

$localizacao = $_POST['localizacao'];
$nome_contato = $_POST['nome'];
$email_contato = $_POST['email'];
$telefone_contato = $_POST['telefone'];
$data_insercao = date("Y-m-d H:i:s");
$capa = "default.png";

// Função para validar morada usando Nominatim OpenStreetMap
function validarMoradaOSM($morada) {
    $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($morada);
    $opts = [
        "http" => [
            "header" => "User-Agent: MinhaAppDeTeste/1.0\r\n"
        ]
    ];
    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        return false;
    }
    $data = json_decode($response, true);
    return (is_array($data) && count($data) > 0);
}

$erro = false;

if (!validarMoradaOSM($localizacao)) {
    $erro = "Morada inválida. Por favor, introduza uma morada real.";
}

if (!$erro) {
    $query = "INSERT INTO anuncios (nome_produto, descricao, preco, ref_categoria, ref_user, localizacao, capa, data_insercao, ref_medida) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($link, $query);

    if ($stmt) {
        mysqli_stmt_bind_param(
            $stmt,
            "ssdisssss",
            $nome_produto, $descricao, $preco, $ref_categoria, $ref_user, $localizacao, $capa, $data_insercao, $ref_medida);

        if (mysqli_stmt_execute($stmt)) {
            echo "Anúncio inserido com sucesso!";
        } else {
            echo "Erro ao inserir anúncio: " . mysqli_stmt_error($stmt);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo "Erro na preparação da query: " . mysqli_error($link);
    }
} else {
    echo "<p style='color:red;'>$erro</p>";
}

mysqli_close($link);
?>
