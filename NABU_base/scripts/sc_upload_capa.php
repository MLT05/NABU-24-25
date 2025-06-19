<?php
session_start();
require_once '../connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit;
}

$id_user = $_SESSION['id_user'];

if (!isset($_FILES['capa']) || $_FILES['capa']['error'] !== UPLOAD_ERR_OK) {
    header("Location: ../Paginas/criar_anuncio.php?id_erro=1"); // Ajusta a página conforme necessário
    exit;
}

if (!isset($_POST['id_anuncio']) || empty($_POST['id_anuncio'])) {
    header("Location: ../Paginas/criar_anuncio.php?id_erro=6"); // Erro: id_anuncio não fornecido
    exit;
}

$id_anuncio = intval($_POST['id_anuncio']);

$arquivo_tmp = $_FILES['capa']['tmp_name'];
$tamanho_arquivo = $_FILES['capa']['size'];
$tamanho_max = 10 * 1024 * 1024; // 10MB

if ($tamanho_arquivo > $tamanho_max) {
    header("Location: ../Paginas/criar_anuncio.php?id_erro=3");
    exit;
}

// Validar tipo mime e extensão permitidos
$tipos_permitidos = array(IMAGETYPE_JPEG, IMAGETYPE_PNG, IMAGETYPE_GIF);
$tipo_imagem = exif_imagetype($arquivo_tmp);

if (!in_array($tipo_imagem, $tipos_permitidos)) {
    header("Location: ../Paginas/criar_anuncio.php?id_erro=2");
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
$nome_novo = 'capa_' . $id_user . '_' . time() . $ext;
$caminho_destino = __DIR__ . '/../uploads/capas/' . $nome_novo;

// Função para redimensionar e cortar imagem para 400x300 px (4:3)
function redimensionarECortarImagem($src, $destino, $largura_destino, $altura_destino) {
    list($largura_original, $altura_original, $tipo) = getimagesize($src);

    $ratio_original = $largura_original / $altura_original;
    $ratio_destino = $largura_destino / $altura_destino;

    if ($ratio_original > $ratio_destino) {
        // imagem mais larga que destino: cortar largura
        $nova_altura = $altura_original;
        $nova_largura = intval($altura_original * $ratio_destino);
        $src_x = intval(($largura_original - $nova_largura) / 2);
        $src_y = 0;
    } else {
        // imagem mais alta que destino: cortar altura
        $nova_largura = $largura_original;
        $nova_altura = intval($largura_original / $ratio_destino);
        $src_x = 0;
        $src_y = intval(($altura_original - $nova_altura) / 2);
    }

    $imagem_destino = imagecreatetruecolor($largura_destino, $altura_destino);

    // Transparência para PNG e GIF
    if ($tipo == IMAGETYPE_PNG || $tipo == IMAGETYPE_GIF) {
        imagealphablending($imagem_destino, false);
        imagesavealpha($imagem_destino, true);
        $transparente = imagecolorallocatealpha($imagem_destino, 0, 0, 0, 127);
        imagefill($imagem_destino, 0, 0, $transparente);
    } else {
        // Fundo branco para JPG
        $branco = imagecolorallocate($imagem_destino, 255, 255, 255);
        imagefill($imagem_destino, 0, 0, $branco);
    }

    // Criar imagem origem
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
        $largura_destino, $altura_destino,
        $nova_largura, $nova_altura
    );

    // Salvar arquivo
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

// Redimensionar e cortar para 400x300
if (!redimensionarECortarImagem($arquivo_tmp, $caminho_destino, 400, 300)) {
    header("Location: ../Paginas/criar_anuncio.php?id_erro=5");
    exit;
}

$link = new_db_connection();
$stmt = mysqli_stmt_init($link);

// Apagar imagem antiga, se existir
$query_select = "SELECT capa FROM anuncios WHERE id_anuncio = ?";
if (mysqli_stmt_prepare($stmt, $query_select)) {
    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $capa_atual);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    if (!empty($capa_atual) && $capa_atual !== 'default_capa.jpg') {
        $arquivo_antigo = __DIR__ . '/../uploads/capas/' . $capa_atual;
        if (file_exists($arquivo_antigo)) {
            unlink($arquivo_antigo);
        }
    }
} else {
    // Se falhar, continua
}

// Atualizar nome da capa no banco
$stmt = mysqli_stmt_init($link);
$query_update = "UPDATE anuncios SET capa = ? WHERE id_anuncio = ?";

if (mysqli_stmt_prepare($stmt, $query_update)) {
    mysqli_stmt_bind_param($stmt, "si", $nome_novo, $id_anuncio);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        header("Location: ../Paginas/editar_anuncio.php?id_anuncio=$id_anuncio&success=1");
        exit;
    } else {
        mysqli_stmt_close($stmt);
        mysqli_close($link);
        header("Location: ../Paginas/criar_anuncio.php?id_erro=4");
        exit;
    }
} else {
    mysqli_close($link);
    header("Location: ../Paginas/criar_anuncio.php?id_erro=4");
    exit;
}
