<main class="d-flex justify-content-center align-items-center min-vh-100">

    <div class="w-100" style="max-width: 360px;">
        <!-- Ícone com fundo -->
        <img src="../LOGO_ICO/android-chrome-512x512.png" alt="Logo"
             class="verde_bg rounded mx-auto d-block mb-4 w-25 w-sm-20 w-md-10"
             style="aspect-ratio: 1 / 1; object-fit: cover;">

        <!-- Título -->
        <h1 class="text-center tituloLS fw-bold mb-4">REGISTO</h1>

        <?php
        if (isset($_GET['id_erro'])) {
            $mensagem_erro = '';

            switch ($_GET['id_erro']) {
                case 1:
                    $mensagem_erro = 'Por favor, preencha todos os campos do formulário.';
                    break;
                case 2:
                    $mensagem_erro = 'Este login já está em uso.';
                    break;
                case 3:
                    $mensagem_erro = 'Ocorreu um erro interno. Por favor, tente novamente mais tarde.';
                    break;
                case 4:
                    $mensagem_erro = 'Por favor, insira um email válido.';
                    break;
                case 5:
                    $mensagem_erro = 'A palavra-passe deve ter pelo menos 8 caracteres.';
                    break;
                case 6:
                    $mensagem_erro = 'Este email ou contacto já está em uso.';
                    break;
                default:
                    $mensagem_erro = 'Ocorreu um erro desconhecido.';
            }

            echo '<div class="alert alert-danger text-center fw-semibold" role="alert">'
                . htmlspecialchars($mensagem_erro) .
                '</div>';
        }
        ?>
        <!-- Formulário -->
        <form action="../scripts/sc_signup.php" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <input type="text" class="form-control rounded cinza_bg border-0 ps-3 py-2" name="nome" placeholder="Nome" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control rounded cinza_bg border-0 ps-3 py-2" name="email" placeholder="E-mail" required>
            </div>
            <div class="mb-3">
                <input type="number" class="form-control rounded cinza_bg border-0 ps-3 py-2" name="contacto" placeholder="Contacto" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control rounded cinza_bg border-0 ps-3 py-2" name="login" placeholder="Login" required>
            </div>
            <div class="mb-4 position-relative">
                <input type="password" id="password2" class="form-control rounded cinza_bg border-0 ps-3 py-2" name="password" placeholder="Palavra-passe" required>
                <button type="button" id="togglePassword" aria-label="Mostrar/ocultar senha"
                        class="verde d-flex align-items-center justify-content-center"
                        style="position: absolute; top: 50%; transform: translateY(-50%); right: 1rem; border: none; background: transparent; font-size: 1.4rem; cursor: pointer;">
                    <span class="material-icons" id="iconPassword">visibility</span>
                </button>
            </div>
            <p class="text-center">já tem conta? <a href="../Paginas/login.php">Entrar</a></p>
            <button type="submit" class="btn verde_bg rounded w-75 fw-bold text-white py-2 fs-5 mt-5 d-block mx-auto">CRIAR CONTA</button>
        </form>
    </div>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
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
