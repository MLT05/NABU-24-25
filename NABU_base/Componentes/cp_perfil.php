<main class="body_index">
<?php
include_once '../Conexao/conexao.php';
// Verifica se a sessão está iniciada
session_start();
if (!isset($_SESSION['user_id'])) {
    // Redireciona para a página de login se o usuário não estiver autenticado
    header("Location: ../Paginas/login.php");
    exit();
} 
// Obtém o ID do usuário da sessão
$user_id = $_SESSION['user_id'];
// Consulta o nome do usuário no banco de dados
$query = "SELECT nome FROM utilizadores WHERE id = ?";
$stmt = $conexao->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
// Verifica se o usuário foi encontrado
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_name = $row['nome'];
} else {
    // Se o usuário não for encontrado, redireciona para a página de login
    header("Location: ../Paginas/login.php");
    exit();
}
// Fecha a conexão com o banco de dados
$stmt->close();
$conexao->close();  
?>
    <div class="container py-4">
        <div class="text-center mb-4">
            <img src="../Imagens/produtos/ovos.jpg" alt="Foto de perfil" class="rounded-circle border border-success" width="100" height="100" style="object-fit: cover;">
            <h2 class="mt-2 fs-5 text-success">Teresa Oliveira/<= {$user_name} ?></h2>
        </div>

        <div class="card border-0 shadow-sm ">
            <div class="list-group  list-group-flush">

                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center small verde_claro_bg">
                    <img src="../Imagens/icons/add_circle_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" width="20" height="20">
                    Os meus anúncios
                </a>

                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center small verde_claro_bg">
                    <img src="../Imagens/icons/add_circle_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" width="20" height="20">
                    Log-in/ Sign up
                </a>

                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center small verde_claro_bg">
                    <img src="../Imagens/icons/add_circle_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" width="20" height="20">
                    Dados pessoais
                </a>

                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center small verde_claro_bg">
                    <img src="../Imagens/icons/add_circle_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" width="20" height="20">
                    Formas de Pagamento
                </a>

                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center small verde_claro_bg">
                    <img src="../Imagens/icons/add_circle_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" width="20" height="20">
                    Definições
                </a>

                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center small verde_claro_bg">
                    <img src="../Imagens/icons/add_circle_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" width="20" height="20">
                    Favoritos
                </a>

                <a href="#" class="list-group-item list-group-item-action d-flex align-items-center text-danger small verde_claro_bg">
                    <img src="../Imagens/icons/add_circle_24dp_FFFFFF_FILL0_wght400_GRAD0_opsz24.svg" alt="Ícone" class="me-3" width="20" height="20">
                    Logout
                </a>
            </div>
        </div>
    </div>

</main>