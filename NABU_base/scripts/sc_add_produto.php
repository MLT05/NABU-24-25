<?php
require_once '../Connections/connection.php';
session_start();

$link = new_db_connection();

if (!isset($_SESSION['id_user'])) {
    die("Erro: Utilizador não autenticado.");
}

$ref_user = $_SESSION['id_user'];

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

// Função para validar morada
function validarMoradaOSM($morada) {
    $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($morada);
    $opts = ["http" => ["header" => "User-Agent: MinhaApp/1.0\r\n"]];
    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);
    if ($response === false) return false;
    $data = json_decode($response, true);
    return (is_array($data) && count($data) > 0);
}

// Verifica localização
if (!validarMoradaOSM($localizacao)) {
    die("<p style='color:red;'>Morada inválida. Por favor, introduza uma morada real.</p>");
}

// Upload da imagem
$capa = "default.png";
$upload_dir = "../uploads/capas/";
$extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$id_anuncio = (int) $_POST['id_anuncio'];

// Buscar imagem antiga
$query_capa = "SELECT capa FROM anuncios WHERE id_anuncio = ? AND ref_user = ?";
$stmt_capa = mysqli_prepare($link, $query_capa);
mysqli_stmt_bind_param($stmt_capa, "ii", $id_anuncio, $ref_user);
mysqli_stmt_execute($stmt_capa);
mysqli_stmt_bind_result($stmt_capa, $capa_atual);
mysqli_stmt_fetch($stmt_capa);
mysqli_stmt_close($stmt_capa);

if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['pfp']['tmp_name'];
    $file_name = basename($_FILES['pfp']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_ext, $extensoes_permitidas)) {
        $new_name = uniqid("img_", true) . '.' . $file_ext;
        $dest_path = $upload_dir . $new_name;

        // Obter a imagem original conforme o tipo
        switch ($file_ext) {
            case 'jpg':
            case 'jpeg':
                $orig_img = imagecreatefromjpeg($file_tmp);
                break;
            case 'png':
                $orig_img = imagecreatefrompng($file_tmp);
                break;
            case 'gif':
                $orig_img = imagecreatefromgif($file_tmp);
                break;
            default:
                die("Formato de imagem inválido.");
        }

        if (!$orig_img) {
            die("Erro ao processar a imagem.");
        }

        // Tamanho original
        $orig_w = imagesx($orig_img);
        $orig_h = imagesy($orig_img);

        // Tamanho destino fixo
        $dest_w = 400;
        $dest_h = 300;

        // Calcular proporção para crop central
        $src_ratio = $orig_w / $orig_h;
        $dest_ratio = $dest_w / $dest_h;

        if ($src_ratio > $dest_ratio) {
            // Imagem mais larga que o destino — crop horizontal
            $new_h = $orig_h;
            $new_w = $orig_h * $dest_ratio;
            $src_x = ($orig_w - $new_w) / 2;
            $src_y = 0;
        } else {
            // Imagem mais alta que o destino — crop vertical
            $new_w = $orig_w;
            $new_h = $orig_w / $dest_ratio;
            $src_x = 0;
            $src_y = ($orig_h - $new_h) / 2;
        }

        // Criar nova imagem
        $final_img = imagecreatetruecolor($dest_w, $dest_h);

        // Manter transparência para PNG e GIF
        if ($file_ext === 'png' || $file_ext === 'gif') {
            imagecolortransparent($final_img, imagecolorallocatealpha($final_img, 0, 0, 0, 127));
            imagealphablending($final_img, false);
            imagesavealpha($final_img, true);
        }

        // Copiar e redimensionar para 400x300 com crop central
        imagecopyresampled(
            $final_img,
            $orig_img,
            0, 0,                      // destino
            $src_x, $src_y,            // origem
            $dest_w, $dest_h,          // tam. destino
            $new_w, $new_h             // tam. origem crop
        );

        // Guardar imagem no caminho final
        switch ($file_ext) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($final_img, $dest_path, 90);
                break;
            case 'png':
                imagepng($final_img, $dest_path);
                break;
            case 'gif':
                imagegif($final_img, $dest_path);
                break;
        }

        imagedestroy($orig_img);
        imagedestroy($final_img);

        $capa = $new_name;
    } else {
        die("Formato de imagem inválido. Apenas JPG, JPEG, PNG e GIF são permitidos.");
    }
}

$query = "INSERT INTO anuncios (nome_produto, descricao, preco, ref_categoria, ref_user, localizacao, capa, data_insercao, ref_medida) 
          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($link, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ssdisssss",
        $nome_produto, $descricao, $preco, $ref_categoria, $ref_user,
        $localizacao, $capa, $data_insercao, $ref_medida
    );

    if (mysqli_stmt_execute($stmt)) {
        header("Location: ../Paginas/meus_anuncios.php");
        echo "Anúncio inserido com sucesso!";
    } else {
        echo "Erro ao inserir anúncio: " . mysqli_stmt_error($stmt);
    }

    mysqli_stmt_close($stmt);
} else {
    echo "Erro na preparação da query: " . mysqli_error($link);
}

mysqli_close($link);
?>
