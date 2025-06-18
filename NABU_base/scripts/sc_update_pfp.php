<?php
session_start();
require_once '../connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit;
}

function redimensionarECortarImagem($arquivo_origem, $arquivo_destino, $largura_desejada, $altura_desejada) {
    list($largura_orig, $altura_orig, $tipo) = getimagesize($arquivo_origem);

    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $img_orig = imagecreatefromjpeg($arquivo_origem);
            break;
        case IMAGETYPE_PNG:
            $img_orig = imagecreatefrompng($arquivo_origem);
            break;
        case IMAGETYPE_GIF:
            $img_orig = imagecreatefromgif($arquivo_origem);
            break;
        default:
            return false; // formato não suportado
    }

    $proporcao_orig = $largura_orig / $altura_orig;
    $proporcao_desejada = $largura_desejada / $altura_desejada;

    if ($proporcao_orig > $proporcao_desejada) {
        $nova_altura = $altura_desejada;
        $nova_largura = (int)($altura_desejada * $proporcao_orig);
    } else {
        $nova_largura = $largura_desejada;
        $nova_altura = (int)($largura_desejada / $proporcao_orig);
    }

    $img_redimensionada = imagecreatetruecolor($nova_largura, $nova_altura);

    if ($tipo == IMAGETYPE_PNG || $tipo == IMAGETYPE_GIF) {
        imagecolortransparent($img_redimensionada, imagecolorallocatealpha($img_redimensionada, 0, 0, 0, 127));
        imagealphablending($img_redimensionada, false);
        imagesavealpha($img_redimensionada, true);
    }

    imagecopyresampled($img_redimensionada, $img_orig, 0, 0, 0, 0, $nova_largura, $nova_altura, $largura_orig, $altura_orig);

    $x_corte = ($nova_largura - $largura_desejada) / 2;
    $y_corte = ($nova_altura - $altura_desejada) / 2;

    $img_final = imagecreatetruecolor($largura_desejada, $altura_desejada);

    if ($tipo == IMAGETYPE_PNG || $tipo == IMAGETYPE_GIF) {
        imagecolortransparent($img_final, imagecolorallocatealpha($img_final, 0, 0, 0, 127));
        imagealphablending($img_final, false);
        imagesavealpha($img_final, true);
    }

    imagecopy($img_final, $img_redimensionada, 0, 0, $x_corte, $y_corte, $largura_desejada, $altura_desejada);

    switch ($tipo) {
        case IMAGETYPE_JPEG:
            imagejpeg($img_final, $arquivo_destino, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($img_final, $arquivo_destino);
            break;
        case IMAGETYPE_GIF:
            imagegif($img_final, $arquivo_destino);
            break;
    }

    imagedestroy($img_orig);
    imagedestroy($img_redimensionada);
    imagedestroy($img_final);

    return true;
}

// Processo do upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['pfp']) && $_FILES['pfp']['error'] === UPLOAD_ERR_OK) {
    $id_user = $_SESSION['id_user'];

    $arquivo_tmp = $_FILES['pfp']['tmp_name'];
    $nome_arquivo = $_FILES['pfp']['name'];
    $tamanho_arquivo = $_FILES['pfp']['size'];
    $tipo_arquivo = $_FILES['pfp']['type'];

    // Extensões permitidas
    $ext_permitidas = array('jpg', 'jpeg', 'png', 'gif');
    $ext_arquivo = strtolower(pathinfo($nome_arquivo, PATHINFO_EXTENSION));

    // Verificar extensão
    if (!in_array($ext_arquivo, $ext_permitidas)) {
        header("Location: ../Paginas/perfil.php?id_erro=2");
        exit;
    }

    // Verificar tamanho (máx 5MB)
    if ($tamanho_arquivo > 5 * 1024 * 1024) {
        header("Location: ../Paginas/perfil.php?id_erro=3");
        exit;
    }

    $novo_nome = 'pfp_user_'.$id_user.'.'.$ext_arquivo;
    $destino = '../uploads/pfp/' . $novo_nome;

    // Redimensionar e cortar a imagem
    if (!redimensionarECortarImagem($arquivo_tmp, $destino, 200, 200)) {
        header("Location: ../Paginas/perfil.php?id_erro=5");
        exit;
    }

    // Atualizar BD
    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);
    $query = "UPDATE users SET pfp = ? WHERE id_user = ?";

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'si', $novo_nome, $id_user);
        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../Paginas/perfil.php?success=1");
            exit;
        } else {
            mysqli_stmt_close($stmt);
            mysqli_close($link);
            header("Location: ../Paginas/perfil.php?id_erro=4");
            exit;
        }
    } else {
        mysqli_close($link);
        header("Location: ../Paginas/perfil.php?id_erro=4");
        exit;
    }

} else {
    header("Location: ../Paginas/perfil.php?id_erro=1");
    exit;
}
