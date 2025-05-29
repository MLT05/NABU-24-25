<main class="body_index">

    <form method="post" enctype="multipart/form-data" action="../JS/sc_venda.php">
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

            <!-- Categoria -->
            <div class="mb-3">
                <label for="categoria" class="form-label fw-semibold verde_escuro">Categorias*</label>
                <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="categoria" name="categoria" required>
                    <option value="">Escolher a categoria</option>
                    <option value="frutas">Frutas</option>
                    <option value="vegetais">Vegetais</option>
                    <option value="ovos">Ovos</option>
                    <!-- ... outras categorias -->
                </select>
            </div>

            <!-- Descrição -->
            <div class="mb-3">
                <label for="descricao" class="form-label fw-semibold verde_escuro">Descrição*</label>
                <textarea class="form-control bg-success bg-opacity-25" id="descricao" name="descricao" rows="3" required minlength="40"></textarea>
                <small class="form-text verde_escuro opacity-75">Introduz pelo menos 40 caracteres</small>
            </div>

            <!-- Contactos -->
            <h6 class="fw-bold mt-4 verde_escuro fs-4">Contactos</h6>

            <!-- Localização -->
            <div class="mb-3 mt-3 input-group input-group-lg">
                <span class="input-group-text bg-success bg-opacity-25 border-0"><i class="bi bi-geo-alt-fill verde_escuro"></i></span>
                <input type="text" class="form-control bg-success bg-opacity-25" id="localizacao" name="localizacao" placeholder="Localização" required>
            </div>

            <!-- Nome -->
            <div class="mb-3">
                <label for="nome" class="form-label fw-bold verde_escuro">Nome*</label>
                <input type="text" class="form-control bg-success bg-opacity-25" id="nome" name="nome" required>
            </div>

            <!-- Email -->
            <div class="mb-3">
                <label for="email" class="form-label fw-bold verde_escuro">Email*</label>
                <input type="email" class="form-control bg-success bg-opacity-25" id="email" name="email" required>
            </div>

            <!-- Contacto Telefónico -->
            <div class="mb-4">
                <label for="telefone" class="form-label fw-bold verde_escuro">Contacto telefónico*</label>
                <input type="tel" class="form-control bg-success bg-opacity-25" id="telefone" name="telefone" required>
            </div>

            <!-- Botões -->
            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-publicar w-100 me-2">Publicar</button>
                <button type="reset" class="btn btn-descartar w-100 ms-2">Descartar</button>
            </div>

        </div>
    </form>
</main>
