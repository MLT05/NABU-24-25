<?php
session_start();
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];

// Redimensiona imagem
function redimensionarImagem($sourcePath, $destPath, $largura = 200, $altura = 200) {
    list($origWidth, $origHeight, $type) = getimagesize($sourcePath);

    switch ($type) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($sourcePath);
            break;
        default:
            return false;
    }

    $resized = imagecreatetruecolor($largura, $altura);

    if ($type == IMAGETYPE_PNG || $type == IMAGETYPE_GIF) {
        imagecolortransparent($resized, imagecolorallocatealpha($resized, 0, 0, 0, 127));
        imagealphablending($resized, false);
        imagesavealpha($resized, true);
    }

    imagecopyresampled($resized, $image, 0, 0, 0, 0, $largura, $altura, $origWidth, $origHeight);

    switch ($type) {
        case IMAGETYPE_JPEG:
            imagejpeg($resized, $destPath);
            break;
        case IMAGETYPE_PNG:
            imagepng($resized, $destPath);
            break;
        case IMAGETYPE_GIF:
            imagegif($resized, $destPath);
            break;
    }

    imagedestroy($image);
    imagedestroy($resized);
    return true;
}

if (isset($_FILES['pfp']) && $_FILES['pfp']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['pfp']['tmp_name'];
    $fileName = $_FILES['pfp']['name'];
    $fileSize = $_FILES['pfp']['size'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = array('jpg', 'jpeg', 'png', 'gif');

    if (!in_array($fileExt, $allowedExts)) {
        header("Location: ../Paginas/perfil_details.php?id_erro=2");
        exit();
    }

    if ($fileSize > 5 * 1024 * 1024) {
        header("Location: ../Paginas/perfil_details.php?id_erro=3");
        exit();
    }

    $newFileName = 'pfp_' . $id_user . '.' . $fileExt;
    $uploadDir = '../uploads/pfp/';
    $destPath = $uploadDir . $newFileName;

    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    if (redimensionarImagem($fileTmpPath, $destPath)) {
        $link = new_db_connection();
        $stmt = mysqli_stmt_init($link);

        $relativePath = 'uploads/pfp/' . $newFileName;

        $query = "UPDATE users SET pfp = ? WHERE id_user = ?";
        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, 'si', $relativePath, $id_user);
            if (mysqli_stmt_execute($stmt)) {
                header("Location: ../Paginas/perfil_details.php?success=1");
                exit();
            } else {
                header("Location: ../Paginas/perfil_details.php?id_erro=4");
                exit();
            }
        }
        mysqli_stmt_close($stmt);
        mysqli_close($link);
    } else {
        header("Location: ../Paginas/perfil_details.php?id_erro=5");
        exit();
    }
} else {
    header("Location: ../Paginas/perfil_details.php?id_erro=1");
    exit();
}
?>
