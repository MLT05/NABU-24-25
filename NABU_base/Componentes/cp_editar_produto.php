<?php

require_once '../Connections/connection.php';


if (isset($_POST['id']) && !empty($_POST['id'])) {
    // O ID existe e não é nulo ou vazio
    $id_anuncio = $_POST['id'];
    // continue o processamento...
} else {
    // ID não existe ou é nulo/vazio
    header("Location: meus_anuncios");
}

$link = new_db_connection();

// Buscar dados do produto/anúncio
$stmt = mysqli_stmt_init($link);
$query = "SELECT nome_produto, descricao, preco, ref_categoria, ref_user, localizacao, capa, data_insercao, ref_medida FROM anuncios WHERE id_anuncio = ?";
if (mysqli_stmt_prepare($stmt, $query)) {
    mysqli_stmt_bind_param($stmt, "i", $id_anuncio);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $titulo, $descricao, $preco, $ref_categoria, $ref_user, $localizacao, $capa, $data_insercao, $ref_medida);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
} else {
    die("Erro na query do produto: " . mysqli_error($link));
}

// Buscar dados do utilizador
$stmt_user = mysqli_stmt_init($link);
$query_user = "SELECT nome, email, contacto FROM users WHERE id_user = ?";
if (mysqli_stmt_prepare($stmt_user, $query_user)) {
    mysqli_stmt_bind_param($stmt_user, "i", $ref_user);
    mysqli_stmt_execute($stmt_user);
    mysqli_stmt_bind_result($stmt_user, $nome_db, $email, $contacto);
    mysqli_stmt_fetch($stmt_user);
    mysqli_stmt_close($stmt_user);
} else {
    die("Erro na query do utilizador: " . mysqli_error($link));
}
?>


<main class="body_index">
    <form method="post" enctype="multipart/form-data" action="../scripts/sc_editar_produto.php" class="needs-validation" novalidate>
        <!-- Aqui mudou o name para id_anuncio, que é o correto -->
        <input type="hidden" name="id_anuncio" value="<?= htmlspecialchars($id_anuncio) ?>">

        <h5 class="fw-bold fs-3 verde_escuro mb-0">Editar anúncio</h5>
        <p class="verde_escuro">Altere os detalhes sobre o teu produto</p>

        <!-- Upload Imagem -->

        <label for="pfp" class="form-label verde_escuro fw-semibold">Imagem*</label>
<div>
    <img s src="../uploads/capas/<?= htmlspecialchars($capa)?>" class="w-100" style="max-height: 100vh; object-fit: cover;" >
</div>

        <div class="text-center mb-4">
            <div class="mb-3">
                <input type="file" name="capa" id="capa" class="form-control mt-3" accept="image/*">
            </div>
        </div>

        <!-- Título -->
        <div class="mb-3">
            <label for="titulo" class="form-label verde_escuro fw-semibold">Título do Anúncio*</label>
            <input type="text" value="<?= htmlspecialchars($titulo) ?>" class="form-control bg-success bg-opacity-25" id="titulo" name="titulo" required minlength="1">
        </div>

        <!-- Preço -->
        <div class="mb-3">
            <label for="preco" class="form-label verde_escuro fw-semibold">Preço*</label>
            <input type="number" value="<?= htmlspecialchars($preco) ?>" step="0.01" min="0" class="form-control bg-success bg-opacity-25" id="preco" name="preco" required>
        </div>

        <!-- Medidas -->
        <div class="mb-3">
            <label for="medida" class="form-label fw-semibold verde_escuro">Medidas*</label>
            <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="medida" name="medida" required>
                <option value="">Seleciona uma medida</option>
                <?php
                $stmt_medida = mysqli_stmt_init($link);
                $query_medida = "SELECT id_medida, abreviatura FROM medidas";
                if (mysqli_stmt_prepare($stmt_medida, $query_medida)) {
                    mysqli_stmt_execute($stmt_medida);
                    mysqli_stmt_bind_result($stmt_medida, $id_medida, $abreviatura);
                    while (mysqli_stmt_fetch($stmt_medida)) {
                        $selected = ($id_medida == $ref_medida) ? "selected" : "";
                        echo "<option value='" . htmlspecialchars($id_medida) . "' $selected>" . htmlspecialchars($abreviatura) . "</option>";
                    }
                    mysqli_stmt_close($stmt_medida);
                }
                ?>
            </select>
        </div>

        <!-- Categoria -->
        <div class="mb-3">
            <label for="categoria" class="form-label fw-semibold verde_escuro">Categoria*</label>
            <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="categoria" name="categoria" required>
                <option value="">Seleciona uma categoria</option>
                <?php
                $stmt_categoria = mysqli_stmt_init($link);
                $query_categoria = "SELECT id_categoria, nome_categoria FROM categorias";
                if (mysqli_stmt_prepare($stmt_categoria, $query_categoria)) {
                    mysqli_stmt_execute($stmt_categoria);
                    mysqli_stmt_bind_result($stmt_categoria, $id_categoria, $nome_categoria);
                    while (mysqli_stmt_fetch($stmt_categoria)) {
                        $selected = ($id_categoria == $ref_categoria) ? "selected" : "";
                        echo "<option value='" . htmlspecialchars($id_categoria) . "' $selected>" . htmlspecialchars($nome_categoria) . "</option>";
                    }
                    mysqli_stmt_close($stmt_categoria);
                }
                ?>
            </select>
        </div>

        <!-- Descrição -->
        <div class="mb-3">
            <label for="descricao" class="form-label fw-semibold verde_escuro">Descrição*</label>
            <textarea class="form-control bg-success bg-opacity-25" id="descricao" name="descricao" rows="3" required><?= htmlspecialchars($descricao) ?></textarea>
        </div>

        <!-- Localização -->
        <label for="localizacao" class="form-label fw-semibold verde_escuro">Localização*</label>
        <div class="mb-3 d-flex align-items-center">
            <span class="bg-success bg-opacity-25 border-0 p-2 me-2">
                <i class="bi bi-geo-alt-fill verde_escuro"></i>
            </span>
            <input type="text"  value="<?= htmlspecialchars($localizacao) ?>" class="form-control bg-success bg-opacity-25" id="localizacao" name="localizacao"  required >
        </div>

        <!-- Contactos -->
        <h6 class="fw-bold mt-4 verde_escuro fs-4">Contactos</h6>

        <div class="mb-3">
            <label for="nome" class="form-label fw-bold verde_escuro">Nome*</label>
            <input type="text" value="<?= htmlspecialchars($nome_db) ?>" class="form-control bg-success bg-opacity-25" id="nome" name="nome" required>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-bold verde_escuro">Email*</label>
            <input type="email" value="<?= htmlspecialchars($email) ?>" class="form-control bg-success bg-opacity-25" id="email" name="email" required>
        </div>

        <div class="mb-4">
            <label for="telefone" class="form-label fw-bold verde_escuro">Contacto telefónico*</label>
            <input type="tel" value="<?= htmlspecialchars($contacto) ?>" class="form-control bg-success bg-opacity-25" id="telefone" name="telefone" required>
        </div>

        <!-- ID do utilizador (campo oculto) -->
        <input type="hidden" name="id_user" value="<?= htmlspecialchars($ref_user) ?>">

        <!-- Botões -->
        <div class="d-flex justify-content-between mb-3">
            <button type="submit" class="btn btn-publicar w-100 me-2">Editar</button>
            <button type="reset" class="btn btn-descartar w-100 ms-2">Reset</button>
        </div>


    </form>
    <form method="post" action="../scripts/sc_eliminar_anuncio.php" onsubmit="return confirm('Tens a certeza que queres eliminar este anúncio?');" class="w-100  ">
        <input type="hidden" name="id_anuncio" value="<?= htmlspecialchars($id_anuncio) ?>">
        <button type="submit" class="btn btn-danger w-100">Eliminar anúncio</button>
    </form>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'), {
            backdrop: 'static',
            keyboard: false
        });
        loginModal.show();
    });

    const previewImage = (event) => {
        const files = event.target.files;
        if(files.length > 0) {
            const imageUrl = URL.createObjectURL(files[0]);
            const imageElement = document.getElementById("preview-selected-image");
            if(imageElement) {
                imageElement.src = imageUrl;
            }
        }
    };
</script>
