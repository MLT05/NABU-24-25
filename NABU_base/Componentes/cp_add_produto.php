<main class="body_index">

    <form method="post" enctype="multipart/form-data" action="../scripts/sc_add_produto.php">
        <div>
            <h5 class="fw-bold fs-3 verde_escuro mb-0">Criar novo anúncio</h5>
            <p class="verde_escuro">Insere todos os detalhes sobre o teu produto</p>

            <!-- Upload Imagem -->
            <div class="upload-box mb-3">
                <label for="imagens" class="w-100 text-center">
                    <i class="bi bi-upload fs-2 d-block"></i>
                    Adicionar imagens
                    <input type="file" id="imagens" name="imagens[]" multiple hidden>
                </label>
            </div>

            <!-- Título -->
            <div class="mb-3">
                <label for="titulo" class="form-label verde_escuro fw-semibold">Título do Anúncio*</label>
                <input type="text" class="form-control bg-success bg-opacity-25" id="titulo" name="titulo" required minlength="16">
                <small class="form-text verde_escuro opacity-75">Introduz pelo menos 16 caracteres</small>
            </div>

            <!-- Preço -->
            <div class="mb-3">
                <label for="preco" class="form-label fw-semibold verde_escuro">Preço*</label>
                <input type="number" step="0.01" min="0" class="form-control bg-success bg-opacity-25" id="preco" name="preco" required>
            </div>

            <!-- Categoria -->
            <div class="mb-3">
                <label for="categoria" class="form-label fw-semibold verde_escuro">Categorias*</label>
                <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="categoria" name="categoria" required>
                    <?php
                    require_once '../Connections/connection.php';
                    $link = new_db_connection();

                    $stmt = mysqli_stmt_init($link);
                    $query = "SELECT id_categoria, nome_categoria FROM categorias ORDER BY nome_categoria ASC";

                    if (mysqli_stmt_prepare($stmt, $query)) {
                        if (mysqli_stmt_execute($stmt)) {
                            mysqli_stmt_bind_result($stmt, $id_categoria, $nome_categoria);
                            while (mysqli_stmt_fetch($stmt)) {
                                echo '<option value="' . $id_categoria . '">' . htmlspecialchars($nome_categoria) . '</option>';
                            }
                        }
                    }
                    ?>
                </select>
            </div>

            <!-- Descrição -->
            <div class="mb-3">
                <label for="descricao" class="form-label fw-semibold verde_escuro">Descrição*</label>
                <textarea class="form-control bg-success bg-opacity-25" id="descricao" name="descricao" rows="3" required minlength="40"></textarea>
                <small class="form-text verde_escuro opacity-75">Introduz pelo menos 40 caracteres</small>
            </div>

            <!-- Localização -->
            <div class="mb-3 d-flex align-items-center">
                <span class="bg-success bg-opacity-25 border-0 p-2 me-2">
                    <i class="bi bi-geo-alt-fill verde_escuro"></i>
                </span>
                <input type="text" class="form-control bg-success bg-opacity-25" id="localizacao" name="localizacao" placeholder="Localização" required>
            </div>

            <!-- Contactos -->
            <h6 class="fw-bold mt-4 verde_escuro fs-4">Contactos</h6>

            <!-- Nome -->
            <div class="mb-3">
                <label for="nome" class="form-label fw-bold verde_escuro">Nome*</label>
                <input type="text" class="form-control bg-success bg-opacity-25" id="nome" name="nome" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-bold verde_escuro">Email*</label>
                <input type="email" class="form-control bg-success bg-opacity-25"
