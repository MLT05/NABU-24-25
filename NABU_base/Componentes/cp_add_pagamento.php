<?php
require_once '../Connections/connection.php';


if (!isset($_SESSION['id_user'])) {
    ?>
    <!-- Modal de login obrigatório -->
    <div class="modal fade show" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-modal="true"
        role="dialog" style="display: block;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-none">
                <div class="modal-header border-0">
                    <h5 class="modal-title" id="loginModalLabel">Login necessário</h5>
                </div>
                <div class="modal-body text-center">
                    <p>Para adicionares um metéodo de pagamento é necessário ter login.</p>
                </div>
                <div class="modal-footer border-0">
                    <a href="../Paginas/login.php" class="btn btn-success verde_escuro_bg">Fazer Login</a>
                    <a href="../Paginas/index.php" class="btn btn-secondary">Fechar</a>
                </div>
            </div>
        </div>
    </div>

    <?php
} else {
    $id_user = $_SESSION['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);
    $query = "SELECT nome, login, email, contacto, data_pagamento, metodo_pagamento, valor FROM users,pagamentos WHERE id_user AND id_pagamento = ?";

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nome_db, $login, $email, $contacto, $data_pagamento, $metodo_pagamento, $valor);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
    }

    mysqli_close($link);
    ?>

    <main class="body_index">
        <form method="post" enctype="multipart/form-data" action="../scripts/sc_add_produto.php">
            <div>
                <h5 class="fw-bold fs-3 verde_escuro mb-0">Criar metéodo de pagamento</h5>
                <p class="verde_escuro">Insere todos os teus dados</p>


                <!-- Nome -->
                <div class="mb-3">
                    <label for="titulo" class="form-label verde_escuro fw-semibold">Nome do Cartão*</label>
                    <input type="text" class="form-control bg-success bg-opacity-25" id="titulo" name="titulo" required>
                </div>


                <!-- Forma de Pagamento -->
                <div class="mb-3">
                    <label for="metodo_pagamento">Método de Pagamento:</label>
                    <select id="metodo_pagamento" name="metodo_pagamento" required>
                        <option value="cartao">Cartão de Crédito</option>
                        <option value="boleto">MB Way</option>
                        <option value="transferencia">Transferência Bancária</option>
                    </select><br><br>
                </div>

                <!-- Medida -->
                <div class="mb-3">
                    <label for="medida" class="form-label fw-semibold verde_escuro">Unidade de medida*</label>
                    <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="medida" name="medida"
                        required>
                        <?php
                        $link = new_db_connection();
                        $stmt = mysqli_stmt_init($link);
                        $query = "SELECT id_medida, abreviatura FROM medidas";
                        if (mysqli_stmt_prepare($stmt, $query)) {
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_bind_result($stmt, $id_medida, $abreviatura);
                            while (mysqli_stmt_fetch($stmt)) {
                                echo '<option value="' . $user . '">' . htmlspecialchars($abreviatura) . '</option>';
                            }
                            mysqli_stmt_close($stmt);
                        }
                        mysqli_close($link);
                        ?>
                    </select>
                </div>

                <!--Confirma o user -->
                <div class="mb-3">
                    <label for="categoria" class="form-label fw-semibold verde_escuro">Categoria*</label>
                    <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="categoria"
                        name="categoria" required>
                        <?php
                        $link = new_db_connection();
                        $stmt = mysqli_stmt_init($link);
                        $query = "SELECT id_user, user FROM users";
                        if (mysqli_stmt_prepare($stmt, $query)) {
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_bind_result($stmt, $id_user, $userd);
                            while (mysqli_stmt_fetch($stmt)) {
                                echo '<option value="' . $id_user . '">' . htmlspecialchars($users) . '</option>';
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
                    <textarea class="form-control bg-success bg-opacity-25" id="descricao" name="descricao" rows="3"
                        required></textarea>
                </div>

                <!-- Localização -->
                <div class="mb-3 d-flex align-items-center">
                    <span class="bg-success bg-opacity-25 border-0 p-2 me-2">
                        <i class="bi bi-geo-alt-fill verde_escuro"></i>
                    </span>
                    <input type="text" class="form-control bg-success bg-opacity-25" id="localizacao" name="localizacao"
                        placeholder="Localização" required>
                </div>

                <!-- Contactos -->
                <h6 class="fw-bold mt-4 verde_escuro fs-4">Contactos</h6>

                <!-- Nome -->
                <div class="mb-3">
                    <label for="nome" class="form-label fw-bold verde_escuro">Nome*</label>
                    <input type="text" value="<?= htmlspecialchars($nome_db) ?>"
                        class="form-control bg-success bg-opacity-25" id="nome" name="nome" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label fw-bold verde_escuro">Email*</label>
                    <input type="email" value="<?= htmlspecialchars($email) ?>"
                        class="form-control bg-success bg-opacity-25" id="email" name="email" required>
                </div>

                <!-- Contacto Telefónico -->
                <div class="mb-4">
                    <label for="telefone" class="form-label fw-bold verde_escuro">Contacto telefónico*</label>
                    <input type="tel" value="<?= htmlspecialchars($contacto) ?>"
                        class="form-control bg-success bg-opacity-25" id="telefone" name="telefone" required>
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

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var loginModal = new bootstrap.Modal(document.getElementById('loginModal'), {
            backdrop: 'static',
            keyboard: false
        });
        loginModal.show();
    });
</script>

<script>
    const previewImage = (event) => {
        const files = event.target.files;
        if (files.length > 0) {
            const imageUrl = URL.createObjectURL(files[0]);
            const imageElement = document.getElementById("preview-selected-image")
            imageElement.src = imageUrl
        }
    }
    <div class="upload-box mb-3">
        <label for="imagens" class="w-100 text-center">
            <i class="bi bi-upload fs-2 d-block"></i>
            Adicionar imagens
            <input type="file" id="imagens" name="imagens[]" multiple hidden>
        </label>
    </div>

</script>