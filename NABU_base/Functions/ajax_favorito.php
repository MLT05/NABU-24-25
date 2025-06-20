<?php
// Incluir a tua função para favoritos
require_once 'function_favorito.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Chama a função que adiciona/remove favorito e retorna a mensagem
    echo toggle_favorito_post();
} else {
    echo "❌ Método não permitido.";
}
?>
