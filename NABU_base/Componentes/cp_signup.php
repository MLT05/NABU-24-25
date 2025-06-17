<main class="body_index d-flex justify-content-center align-items-center">



    <div class="w-100" style="max-width: 360px;">
        <!-- Ícone com fundo -->
        <div class="bg-success rounded d-flex justify-content-center align-items-center mx-auto mb-4" >
            <img src="../Imagens/img_cp/logo_nabu.svg" alt="Ícone fruta" width="80px" height="80px">
        </div>

        <!-- Título -->
        <h2 class="text-center fw-bold mb-4">Registo</h2>

        <!-- Formulário -->
        <form action="../scripts/sc_signup.php" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
                <input type="text" class="form-control rounded-pill bg-light border-0 ps-3" name="nome" placeholder="Nome" required>
            </div>
            <div class="mb-3">
                <input type="email" class="form-control rounded-pill bg-light border-0 ps-3" name="email" placeholder="E-mail" required>
            </div>
            <div class="mb-3">
                <input type="number" class="form-control rounded-pill bg-light border-0 ps-3" name="contacto" placeholder="Contacto" required>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control rounded-pill bg-light border-0 ps-3" name="login" placeholder="Login" required>
            </div>
            <div class="mb-4">
                <input type="password" class="form-control rounded-pill bg-light border-0 ps-3" name="password" placeholder="Palavra-passe" required>
            </div>
            <button type="submit" class="btn btn-success rounded-pill w-100 fw-bold">Criar Conta</button>
        </form>
    </div>
