


    <form method="post"  action="../scripts/sc_alterar_login.php">
    <h3 class="mb-3 verde_escuro" >Login e palavra-passe:</h3>
        <?php
        if (isset($_GET['msg2'])) {
            $tipo = 'danger';
            $mensagem = '';

            switch ($_GET['msg2']) {
                case 'campos_vazios':
                    $mensagem = 'Por favor, preencha o campo de login.';
                    break;
                case 'login_ja_usado':
                    $mensagem = 'Este login já está em uso.';
                    break;
                case 'senha_curta':
                    $mensagem = 'A palavra-passe deve ter pelo menos 8 caracteres.';
                    break;
                case 'erro_bd':
                    $mensagem = 'Erro ao atualizar os dados. Tente novamente.';
                    break;
                case 'sucesso':
                    $tipo = 'success';
                    $mensagem = 'Dados atualizados com sucesso!';
                    break;
                case 'sem_alteracao':
                    $tipo = 'info';
                    $mensagem = 'Nenhuma alteração feita.';
                    break;
                default:
                    $mensagem = 'Ocorreu um erro inesperado.';
            }

            echo "<div class='alert alert-$tipo text-center fw-semibold' role='alert'>" . htmlspecialchars($mensagem) . "</div>";
        }
        ?>


        <div class="mb-3">
        <label for="login" class="form-label fw-bold verde_escuro">login:</label>
        <input type="text" value="<?php echo htmlspecialchars($login); ?>" class="form-control bg-success bg-opacity-25" id="login" name="login" required>
    </div>
        <label for="password" class="form-label fw-bold verde_escuro">Nova password:</label>
        <div class="mb-4 position-relative">

            <input type="password" id="password2" class="form-control bg-success bg-opacity-25" name="password" placeholder="Palavra-passe">
            <button type="button" id="togglePassword" aria-label="Mostrar/ocultar senha"
                    class="verde d-flex align-items-center justify-content-center"
                    style="position: absolute; top: 50%; transform: translateY(-50%); right: 1rem; border: none; background: transparent; font-size: 1.4rem; cursor: pointer;">
                <span class="material-icons" id="iconPassword">visibility</span>
            </button>
        </div>
        <button type="submit" class="btn verde_bg rounded w-75 fw-bold text-white py-2 fs-5 mt-3 mb-3 d-block mx-auto">Alterar login/password</button>
    </form>
</main>
<script>
    const togglePassword = document.querySelector('#togglePassword');
    const password = document.querySelector('#password2');
    const iconPassword = document.querySelector('#iconPassword');

    togglePassword.addEventListener('click', () => {
        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);
        iconPassword.textContent = type === 'password' ? 'visibility' : 'visibility_off';
    });
</script>
