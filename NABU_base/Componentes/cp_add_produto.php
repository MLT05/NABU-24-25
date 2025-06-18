<?php
require_once '../Connections/connection.php';
?>

<!-- Incluir Bootstrap CSS e JS (coloca isto no <head> ou no topo da página se ainda não tens) -->
<?php

if (!isset($_SESSION['id_user'])) {
    ?>
    <!-- Modal inicial oculto (fade), sem show e sem display:block -->
    <div class="modal fade show" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-modal="true" role="dialog" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-none"> <!-- Remove borda e sombra -->
                <div class="modal-header border-0"> <!-- Remove a linha de separação -->
                    <h5 class="modal-title" id="loginModalLabel">⚠ Login necessário</h5>
                </div>
                <div class="modal-body text-center">
                    <p>Para criar um anúncio é necessário ter login.</p>
                </div>
                <div class="modal-footer border-0"> <!-- Remove a linha de separação -->
                    <a href="../Paginas/login.php" class="btn btn-success verde_escuro_bg">Fazer Login</a>
                    <a href="../Paginas/index.php" class="btn btn-secondary">Fechar</a>
                </div>
            </div>
        </div>
    </div>



    <script>
        // Abrir modal automaticamente após a página carregar
        document.addEventListener('DOMContentLoaded', function () {
            var loginModal = new bootstrap.Modal(document.getElementById('loginModal'));
            loginModal.show();
        });
    </script>

    <?php
} else {

    $id_user = $_SESSION['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT nome, login, email, contacto FROM users WHERE id_user = ?";

    $capa = "defaultpfp.png"; // imagem padrão caso não tenha capa

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nome_db, $login, $email, $contacto);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
    ?>
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
                    <input type="text" class="form-control bg-success bg-opacity-25" id="titulo" name="titulo" required minlength="1">
                </div>

                <!-- Preço -->
                <div class="mb-3">
                    <label for="preco" class="form-label fw-semibold verde_escuro">Preço*</label>
                    <input type="number" step="0.01" min="0" class="form-control bg-success bg-opacity-25" id="preco" name="preco" required>
                </div>

                <!-- Medidas -->
                <div class="mb-3">
                    <label for="medida" class="form-label fw-semibold verde_escuro">Medidas*</label>
                    <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="medida" name="medida" required>
                        <?php
                        $link = new_db_connection();
                        $stmt = mysqli_stmt_init($link);
                        $query = "SELECT id_medida, abreviatura FROM medidas";
                        if (mysqli_stmt_prepare($stmt, $query)) {
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_bind_result($stmt, $id_medida, $abreviatura);
                            while (mysqli_stmt_fetch($stmt)) {
                                echo '<option value="' . $id_medida . '">' . htmlspecialchars($abreviatura) . '</option>';
                            }
                            mysqli_stmt_close($stmt);
                        }
                        mysqli_close($link);
                        ?>
                    </select>
                </div>

                <!-- Categoria -->
                <div class="mb-3">
                    <label for="categoria" class="form-label fw-semibold verde_escuro">Categoria*</label>
                    <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="categoria" name="categoria" required>
                        <?php
                        $link = new_db_connection();
                        $stmt = mysqli_stmt_init($link);
                        $query = "SELECT id_categoria, nome_categoria FROM categorias";
                        if (mysqli_stmt_prepare($stmt, $query)) {
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_bind_result($stmt, $id_categoria, $nome_categoria);
                            while (mysqli_stmt_fetch($stmt)) {
                                echo '<option value="' . $id_categoria . '">' . htmlspecialchars($nome_categoria) . '</option>';
                            }
                            mysqli_stmt_close($stmt);
                        }
                        mysqli_close($link);
                        ?>
                    </select>
                </div>

                <!-- Descrição -->
                <div class="mb-3">
                    <label for="descricao" class="form-label fw-semibold verde_escuro">Descrição*</label>
                    <textarea class="form-control bg-success bg-opacity-25" id="descricao" name="descricao" rows="3" required minlength="1"></textarea>
                </div>

                <!-- Localização -->
                <label for="descricao" class="form-label fw-semibold verde_escuro">Localização*</label>
                <div class="mb-3 d-flex align-items-center">
                    <span class="bg-success bg-opacity-25 border-0 p-2 me-2">
                    <i class="bi bi-geo-alt-fill verde_escuro"></i>
                </span>
                    <input type="text" class="form-control  bg-success bg-opacity-25" id="localizacao" name="localizacao" placeholder="Localização" required>
                </div>

                <!-- Contactos -->
                <h6 class="fw-bold mt-4 verde_escuro fs-4">Contactos</h6>

                <!-- Nome -->
                <div class="mb-3">
                    <label for="nome" class="form-label fw-bold verde_escuro">Nome*</label>
                    <input type="text" value="<?php  echo htmlspecialchars($nome_db) ?>" class="form-control bg-success bg-opacity-25" id="nome" name="nome" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold verde_escuro">Email*</label>
                    <input type="email" value="<?php  echo htmlspecialchars($email) ?>" class="form-control bg-success bg-opacity-25" id="email" name="email" required>
                </div>

                <!-- Contacto Telefónico -->
                <div class="mb-4">
                    <label for="telefone" class="form-label fw-bold verde_escuro">Contacto telefónico*</label>
                    <input type="tel" value="<?php  echo htmlspecialchars($contacto) ?>" class="form-control bg-success bg-opacity-25" id="telefone" name="telefone" required>
                </div>

                <!-- Botões -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-publicar w-100 me-2">Publicar</button>
                    <button type="reset" class="btn btn-descartar w-100 ms-2">Descartar</button>
                </div>
            </div>
        </form>
    </main>

    <?php
}
?>


