<?php
session_start();
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    // Se não estiver logado, redireciona pro login
 header("../Paginas/login.php");

} else {


    $id_user = $_SESSION['id_user'];

    $link = new_db_connection();
    $stmt = mysqli_stmt_init($link);

    $query = "SELECT nome, pfp , login, email, contacto FROM users WHERE id_user = ?";


    $capa = "defaultpfp.png"; // imagem padrão caso não tenha capa

    if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $nome_db, $capa_db, $login, $email, $contacto);

        if (mysqli_stmt_fetch($stmt)) {

            if (!empty($capa_db)) {
                $capa = $capa_db;
            }
        }
        mysqli_stmt_close($stmt);
    }


    mysqli_close($link);
}
?>

<main class="body_index">



    <div class="text-center mb-4">
        <img src="../uploads/pfp/<?php echo htmlspecialchars($capa); ?>" alt="Foto de perfil" class="rounded-circle border border-success imagempfp" style="object-fit: cover;">
        <form action="../scripts/sc_update_pfp.php" method="post" enctype="multipart/form-data" class="mt-4 text-center">
            <div class="mb-3">
                <label for="pfp" class="form-label fw-semibold">Alterar imagem de perfil</label>
                <input type="file" name="pfp" id="pfp" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" class="btn verde fw-bold">Guardar</button>
        </form>
    </div>

        <?php
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo '<div class="alert alert-success text-center fw-semibold">Imagem de perfil atualizada com sucesso.</div>';
        } elseif (isset($_GET['id_erro'])) {
            $mensagens_erro = array(
                1 => 'Erro ao processar o upload.',
                2 => 'Tipo de ficheiro não permitido (apenas JPG, PNG, GIF).',
                3 => 'Imagem demasiado grande (máx. 5MB).',
                4 => 'Erro ao atualizar a base de dados.',
                5 => 'Erro ao redimensionar a imagem.'
            );

            $id_erro = (int) $_GET['id_erro'];
            if (array_key_exists($id_erro, $mensagens_erro)) {
                echo '<div class="alert alert-danger text-center fw-semibold">' . htmlspecialchars($mensagens_erro[$id_erro]) . '</div>';
            }
        }
        ?>

        <h3 class="mb-3 verde_escuro" >Dados pessoais:</h3>
    <form method="post"  action="../scripts/sc_editar_perfil.php">
        <?php
        if (isset($_GET['msg'])) {
            $tipo = 'danger';
            $mensagem = '';

            switch ($_GET['msg']) {
                case 'campos_vazios':
                    $mensagem = 'Por favor, preencha todos os campos.';
                    break;
                case 'erro_bd':
                    $mensagem = 'Erro ao atualizar perfil. Tente novamente.';
                    break;
                case 'sucesso':
                    $tipo = 'success';
                    $mensagem = 'Perfil atualizado com sucesso!';
                    break;
                default:
                    $mensagem = 'Ocorreu um erro inesperado.';
            }

            echo "<div class='alert alert-$tipo text-center fw-semibold' role='alert'>" . htmlspecialchars($mensagem) . "</div>";
        }
        ?>


        <div class="mb-3">
        <label for="nome" class="form-label fw-bold verde_escuro">Nome:</label>
        <input type="text" value="<?php echo htmlspecialchars($nome_db); ?>" class="form-control bg-success bg-opacity-25" id="nome" name="nome" required>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label fw-bold verde_escuro">Email:</label>
        <input type="email" value="<?php echo htmlspecialchars($email); ?>" class="form-control bg-success bg-opacity-25" id="email" name="email" required>
    </div>

    <!-- Contacto Telefónico -->
    <div class="mb-4">
        <label for="telefone" class="form-label fw-bold verde_escuro">Contacto telefónico:</label>
        <input type="tel" value="<?php echo htmlspecialchars($contacto); ?>" class="form-control bg-success bg-opacity-25" id="telefone" name="telefone" required>
    </div>
        <button type="submit" class="btn verde_bg rounded w-75 fw-bold text-white py-2 fs-5 mt-3 mb-3 d-block mx-auto">Atualizar Dados</button>
    </form>

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
