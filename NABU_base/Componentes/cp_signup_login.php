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
