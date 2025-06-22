<?php
session_start();
require_once '../connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

if (!isset($_FILES['pfp']) || $_FILES['pfp']['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../Paginas/perfil.php?id_erro=1");
    exit;
}

$arquivo_tmp = $_FILES['pfp']['tmp_name'];
$nome_original = $_FILES['pfp']['name'];
$tamanho_max = 10 * 1024 * 1024; // 10MB
$tamanho_arquivo = $_FILES['pfp']['size'];

if ($tamanho_arquivo > $tamanho_max) {
    header("Location: ../Paginas/perfil.php?id_erro=3");
    exit;
}

// Validar tipo mime e extensão permitidos
$tipos_permitidos = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
$tipo_imagem = exif_imagetype($arquivo_tmp);

if (!in_array($tipo_imagem, $tipos_permitidos)) {
    header("Location: ../Paginas/perfil.php?id_erro=2");
    exit;
}

// Função para corrigir orientação EXIF (apenas JPEG)
function corrigirOrientacao($filename) {
    if (function_exists('exif_read_data')) {
        $exif = @exif_read_data($filename);
        if (!empty($exif['Orientation'])) {
            $imagem = imagecreatefromjpeg($filename);
            switch ($exif['Orientation']) {
                case 3:
                    $imagem = imagerotate($imagem, 180, 0);
                    break;
                case 6:
                    $imagem = imagerotate($imagem, -90, 0);
                    break;
                case 8:
                    $imagem = imagerotate($imagem, 90, 0);
                    break;
                default:
                    return;
            }
            imagejpeg($imagem, $filename, 90);
            imagedestroy($imagem);
        }
    }
}

// Corrige a orientação antes do redimensionamento
if ($tipo_imagem == IMAGETYPE_JPEG) {
    corrigirOrientacao($arquivo_tmp);
}

// Gerar nome único para o arquivo
$extensoes = array(
    IMAGETYPE_JPEG => '.jpg',
    IMAGETYPE_PNG => '.png',
    IMAGETYPE_GIF => '.gif'
);

$ext = isset($extensoes[$tipo_imagem]) ? $extensoes[$tipo_imagem] : '.jpg';
$nome_novo = 'pfp_' . $id_user . '_' . time() . $ext;
$caminho_destino = __DIR__ . '/../uploads/pfp/' . $nome_novo;

// Função para redimensionar e croppar a imagem mantendo proporção (200x200)
function redimensionarImagem($src, $destino, $largura_max, $altura_max) {
    list($largura_original, $altura_original, $tipo) = getimagesize($src);

    $ratio_original = $largura_original / $altura_original;
    $ratio_destino = $largura_max / $altura_max;

    if ($ratio_original > $ratio_destino) {
        $nova_altura = $altura_original;
        $nova_largura = intval($altura_original * $ratio_destino);
        $src_x = intval(($largura_original - $nova_largura) / 2);
        $src_y = 0;
    } else {
        $nova_largura = $largura_original;
        $nova_altura = intval($largura_original / $ratio_destino);
        $src_x = 0;
        $src_y = intval(($altura_original - $nova_altura) / 2);
    }

    $imagem_destino = imagecreatetruecolor($largura_max, $altura_max);

    if ($tipo == IMAGETYPE_PNG || $tipo == IMAGETYPE_GIF) {
        imagealphablending($imagem_destino, false);
        imagesavealpha($imagem_destino, true);
        $transparency = imagecolorallocatealpha($imagem_destino, 0, 0, 0, 127);
        imagefill($imagem_destino, 0, 0, $transparency);
    } else {
        $white = imagecolorallocate($imagem_destino, 255, 255, 255);
        imagefill($imagem_destino, 0, 0, $white);
    }

    switch ($tipo) {
        case IMAGETYPE_JPEG:
            $imagem_origem = imagecreatefromjpeg($src);
            break;
        case IMAGETYPE_PNG:
            $imagem_origem = imagecreatefrompng($src);
            break;
        case IMAGETYPE_GIF:
            $imagem_origem = imagecreatefromgif($src);
            break;
        default:
            return false;
    }

    imagecopyresampled(
        $imagem_destino,
        $imagem_origem,
        0, 0,
        $src_x, $src_y,
        $largura_max, $altura_max,
        $nova_largura, $nova_altura
    );

    switch ($tipo) {
        case IMAGETYPE_JPEG:
            imagejpeg($imagem_destino, $destino, 90);
            break;
        case IMAGETYPE_PNG:
            imagepng($imagem_destino, $destino);
            break;
        case IMAGETYPE_GIF:
            imagegif($imagem_destino, $destino);
            break;
    }

    imagedestroy($imagem_origem);
    imagedestroy($imagem_destino);

    return true;
}

// Tenta redimensionar e salvar
if (!redimensionarImagem($arquivo_tmp, $caminho_destino, 200, 200)) {
    header("Location: ../Paginas/perfil.php?id_erro=5");
    exit;
}

// Opcional: Deletar imagem antiga para não acumular arquivos
$link = new_db_connection();
$stmt = mysqli_stmt_init($link);

// Primeiro buscar o nome da imagem atual para deletar
$query_select = "SELECT pfp FROM users WHERE id_user = ?";
if (mysqli_stmt_prepare($stmt, $query_select)) {
    mysqli_stmt_bind_param($stmt, "i", $id_user);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $pfp_atual);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!empty($pfp_atual) && $pfp_atual !== 'default4.png') {
        $arquivo_antigo = __DIR__ . '/../uploads/pfp/' . $pfp_atual;
        if (file_exists($arquivo_antigo)) {
            unlink($arquivo_antigo);
        }
    }
} else {
    // Falha no select, seguir mesmo assim
}

// Atualiza nome no banco
$stmt = mysqli_stmt_init($link);
$query = "UPDATE users SET pfp = ? WHERE id_user = ?";
if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, "si", $nome_novo, $id_user);
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
