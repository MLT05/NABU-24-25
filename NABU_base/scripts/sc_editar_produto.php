<?php
require_once '../Connections/connection.php';

$id_anuncio = $_POST['id_anuncio'];
$titulo = $_POST['titulo'];
$preco = $_POST['preco'];
$medida = $_POST['medida'];
$categoria = $_POST['categoria'];
$descricao = $_POST['descricao'];
$localizacao = $_POST['localizacao'];
$nome = $_POST['nome'];
$email = $_POST['email'];
$telefone = $_POST['telefone'];
$id_user = $_POST['id_user'];
$data_insercao = date("Y-m-d H:i:s");

$link = new_db_connection();

// Buscar a capa atual do anúncio
$query_capa = "SELECT capa FROM anuncios WHERE id_anuncio = ?";
$stmt_capa = mysqli_prepare($link, $query_capa);
mysqli_stmt_bind_param($stmt_capa, "i", $id_anuncio);
mysqli_stmt_execute($stmt_capa);
mysqli_stmt_bind_result($stmt_capa, $capa_atual);
mysqli_stmt_fetch($stmt_capa);
mysqli_stmt_close($stmt_capa);

// Diretório e extensões
$upload_dir = "../uploads/capas/";
$extensoes_permitidas = ['jpg', 'jpeg', 'png', 'gif'];
$default_image = "default-image.jpg";

if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Verificar se um novo arquivo foi enviado
if (isset($_FILES['capa']) && $_FILES['capa']['error'] === UPLOAD_ERR_OK) {
    $file_tmp = $_FILES['capa']['tmp_name'];
    $file_name = basename($_FILES['capa']['name']);
    $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

    if (in_array($file_ext, $extensoes_permitidas)) {
        $new_name = uniqid("img_", true) . '.' . $file_ext;
        $dest_path = $upload_dir . $new_name;

        // Criar imagem original
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

        imagecopyresampled(
            $final_img,
            $orig_img,
            0, 0,
            $src_x, $src_y,
            $dest_w, $dest_h,
            $new_w, $new_h
        );

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

        // Apagar imagem antiga se não for a default
        if ($capa_atual !== $default_image && file_exists($upload_dir . $capa_atual)) {
            unlink($upload_dir . $capa_atual);
        }

        $capa = $new_name; // Usar nova imagem
    } else {
        echo "Formato de imagem inválido.";
        exit();
    }
} else {
    $capa = $capa_atual; // Mantém imagem atual
}

// Verificação de campos e update
if ($id_anuncio && $titulo && $preco !== null && $medida && $categoria && $descricao && $localizacao && $nome && $email && $telefone && $id_user) {
    $query = "UPDATE anuncios 
        SET nome_produto = ?, descricao = ?, preco = ?, ref_categoria = ?, ref_user = ?, localizacao = ?, capa = ?, data_insercao = ?, ref_medida = ?, nome = ?, email = ?, contacto = ?
        WHERE id_anuncio = ?";

    $stmt = mysqli_prepare($link, $query);

    mysqli_stmt_bind_param($stmt, "ssdiissssssii",
        $titulo,
        $descricao,
        $preco,
        $categoria,
        $id_user,
        $localizacao,
        $capa,
        $data_insercao,
        $medida,
        $nome,
        $email,
        $telefone,
        $id_anuncio
    );

    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: ../paginas/meus_anuncios.php");
        exit();
    } else {
        echo "Erro ao atualizar anúncio: " . mysqli_error($link);
    }
} else {
    echo "Dados incompletos no formulário.";
}
?>
