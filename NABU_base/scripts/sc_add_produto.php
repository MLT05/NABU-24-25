<?php
require_once '../Connections/connection.php';
session_start();

$link = new_db_connection();

if (!isset($_SESSION['id_user'])) {
   header("../Paginas/login.php");
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
$telefone_contato = $_POST['contacto'];
$data_insercao = date("Y-m-d H:i:s");

// Validar morada via OSM
function validarMoradaOSM($morada) {
    $url = 'https://nominatim.openstreetmap.org/search?format=json&q=' . urlencode($morada);
    $opts = ["http" => ["header" => "User-Agent: MinhaApp/1.0\r\n"]];
    $context = stream_context_create($opts);
    $response = file_get_contents($url, false, $context);
    if ($response === false) return false;
    $data = json_decode($response, true);
    return (is_array($data) && count($data) > 0);
}

if (!validarMoradaOSM($localizacao)) {
    $_SESSION['mensagem_sistema'] = "Morada inválida. Por favor, introduza uma morada real.";
    $_SESSION['tipo_mensagem'] = "erro";
    header("Location: ../Paginas/meus_anuncios.php");
    exit();
}

// Upload imagem
$capa = "default.png";
$upload_dir = "../uploads/capas/";
$extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['pfp']['tmp_name'];
    $file_name = basename($_FILES['pfp']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_ext, $extensoes_permitidas)) {
        $new_name = uniqid("img_", true) . '.' . $file_ext;
        $dest_path = $upload_dir . $new_name;

        switch ($file_ext) {
            case 'jpg':
            case 'jpeg':
                $orig_img = imagecreatefromjpeg($file_tmp); break;
            case 'png':
                $orig_img = imagecreatefrompng($file_tmp); break;
            case 'gif':
                $orig_img = imagecreatefromgif($file_tmp); break;
            default:

                $_SESSION['mensagem_sistema'] = "Erro ao processar a imagem.";
                $_SESSION['tipo_mensagem'] = "erro";
                header("Location: ../Paginas/meus_anuncios.php");
                exit();

        }

        if (!$orig_img) die("Erro ao processar a imagem.");

        $orig_w = imagesx($orig_img);
        $orig_h = imagesy($orig_img);
        $dest_w = 400;
        $dest_h = 300;

        $src_ratio = $orig_w / $orig_h;
        $dest_ratio = $dest_w / $dest_h;

        if ($src_ratio > $dest_ratio) {
            $new_h = $orig_h;
            $new_w = $orig_h * $dest_ratio;
            $src_x = ($orig_w - $new_w) / 2;
            $src_y = 0;
        } else {
            $new_w = $orig_w;
            $new_h = $orig_w / $dest_ratio;
            $src_x = 0;
            $src_y = ($orig_h - $new_h) / 2;
        }

        $final_img = imagecreatetruecolor($dest_w, $dest_h);

        if ($file_ext === 'png' || $file_ext === 'gif') {
            imagecolortransparent($final_img, imagecolorallocatealpha($final_img, 0, 0, 0, 127));
            imagealphablending($final_img, false);
            imagesavealpha($final_img, true);
        }

        imagecopyresampled($final_img, $orig_img, 0, 0, $src_x, $src_y, $dest_w, $dest_h, $new_w, $new_h);

        switch ($file_ext) {
            case 'jpg':
            case 'jpeg':
                imagejpeg($final_img, $dest_path, 90); break;
            case 'png':
                imagepng($final_img, $dest_path); break;
            case 'gif':
                imagegif($final_img, $dest_path); break;
        }

        imagedestroy($orig_img);
        imagedestroy($final_img);

        $capa = $new_name;
    } else {
        $_SESSION['mensagem_sistema'] = "Formato de imagem inválido. Apenas JPG, JPEG, PNG e GIF são permitidos.";
        $_SESSION['tipo_mensagem'] = "erro";
        header("Location: ../Paginas/meus_anuncios.php");
        exit();
    }
}

// INSERT atualizado com nome, email e telefone
$query = "INSERT INTO anuncios 
(nome_produto, descricao, preco, ref_categoria, ref_user, localizacao, capa, data_insercao, ref_medida, nome, email, contacto) 
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = mysqli_prepare($link, $query);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ssdissssssss",
        $nome_produto, $descricao, $preco, $ref_categoria, $ref_user,
        $localizacao, $capa, $data_insercao, $ref_medida,
        $nome_contato, $email_contato, $telefone_contato
    );

    if (mysqli_stmt_execute($stmt)) {
        $novo_id = mysqli_insert_id($link);

        $_SESSION['mensagem_sistema'] = "Anúncio criado com sucesso!";
        $_SESSION['tipo_mensagem'] = "sucesso";

        header("Location: ../Paginas/produto.php?id=" . $novo_id);
        exit();
    } else {
        $_SESSION['mensagem_sistema'] = "Erro ao inserir anúncio: " . mysqli_stmt_error($stmt);
        $_SESSION['tipo_mensagem'] = "erro";

        header("Location: ../Paginas/produto.php");
        exit();
    }

    mysqli_stmt_close($stmt);
} else {
    $_SESSION['mensagem_sistema'] = "Erro na preparação da query: " . mysqli_error($link);
    $_SESSION['tipo_mensagem'] = "erro";
    header("Location: ../Paginas/meus_anuncios.php");
    exit();
}

mysqli_close($link);
?>
