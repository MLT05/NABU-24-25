<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
    <meta name="description" content="Web-app para compra venda e troca de produtos locais alimentares." />
    <meta name="author" content="G13-bdtss" />
    <title>Nabu</title>
    <link rel="icon" type="image/x-icon" href="imgs/favicon.ico" />

    <!-- Font Awesome icons (free version)-->
    <script src="https://use.fontawesome.com/releases/v6.1.0/js/all.js" crossorigin="anonymous"></script>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <!-- Google fonts-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />

    <!-- Correção da URL da fonte Jost -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@100;300;400;500;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Core theme CSS (includes Bootstrap)-->
    <link href="../CSS/meus_estilos.css?v=1.9" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" href="../LOGO_ICO/favicon-32x32.png" type="image/x-icon">

    <!-- Theme- clear or dark? -->
    <script>
        try {
            const theme = localStorage.getItem("theme");
            if (theme === "dark") {
                document.documentElement.classList.add("dark");
            }
        } catch (e) {
            console.warn("Não foi possível aplicar o tema salvo:", e);
        }
    </script>



    <!-- Mapbox CSS & JS -->
    <link href="https://api.mapbox.com/mapbox-gl-js/v3.12.0/mapbox-gl.css" rel="stylesheet">
    <script src="https://api.mapbox.com/mapbox-gl-js/v3.12.0/mapbox-gl.js"></script>

</head>
<?php
$bodyClass = isset($bodyClass) ? $bodyClass : 'container';
?>

<body class="corpo <?= $bodyClass ?> <?php echo basename($_SERVER['PHP_SELF'], ".php"); ?>">
    <!-- O conteúdo da página vai aqui -->