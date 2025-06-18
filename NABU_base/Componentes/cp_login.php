
<main class=" d-flex justify-content-center align-items-center min-vh-100">


    <div class="w-100" style="max-width: 360px;">
        <!-- Ícone com fundo -->
        <img src="../LOGO_ICO/android-chrome-512x512.png" alt="Logo"
             class="verde_bg rounded mx-auto d-block mb-4 w-25 w-sm-20 w-md-10" style="aspect-ratio: 1 / 1; object-fit: cover;">






        <!-- Título -->
        <h1 class="text-center tituloLS fw-bold mb-4">LOGIN</h1>

        <?php
        if (isset($_GET['id_erro'])) {
            $mensagem_erro = '';

            switch ($_GET['id_erro']) {
                case 1:
                    $mensagem_erro = 'Por favor, preencha todos os campos do formulário.';
                    break;
                case 2:
                    $mensagem_erro = 'Login ou palavra-passe incorretos.';
                    break;
                case 3:
                    $mensagem_erro = 'Ocorreu um erro interno. Por favor, tente novamente mais tarde.';
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
        <form action="../scripts/sc_login.php" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <input type="text" class="form-control rounded-pill cinza_bg border-0 ps-3 py-2" name="login" placeholder="Login" required>
            </div>
            <div class="mb-4">
                <input type="password" class="form-control rounded-pill cinza_bg border-0 ps-3 py-2" name="password" placeholder="Palavra-passe" required>
            </div>
            <button type="submit" class="btn verde_bg rounded-pill w-100 fw-bold text-white py-3 fs-5 mt-5">ENTRAR</button>

        </form>
    </div>
</main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>

    </body>
    </html>