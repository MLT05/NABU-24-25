
<main class=" d-flex justify-content-center align-items-center min-vh-100">


    <div class="w-100" style="max-width: 360px;">
        <!-- Ícone com fundo -->
        <div class="bg-success rounded d-flex justify-content-center align-items-center mx-auto mb-4" >
            <img src="../Imagens/img_cp/logo_nabu.svg" alt="Ícone fruta" width="80px" height="80px">
        </div>

        <!-- Título -->
        <h2 class="text-center fw-bold mb-4">LOGIN</h2>

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