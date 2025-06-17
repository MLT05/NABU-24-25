<?php
session_start();
require_once '../Connections/connectionDB.php';
$link = new_db_connection();

if (!$link) {
    die("Erro na ligação à base de dados");
}

// Verifica se o utilizador está autenticado e tem role = 1
if (!isset($_SESSION['user']) || $_SESSION["role"] != 1) {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verifica se os campos obrigatórios existem
    if (
        isset($_POST['titulo'], $_POST['descricao'], $_POST['preco'], $_POST['categoria'], $_POST['localizacao'],
            $_POST['nome'], $_POST['email'], $_POST['telefone']) &&
        isset($_FILES['imagens']['tmp_name'][0])
    ) {
        $nome_produto = $_POST['titulo'];
        $descricao = $_POST['descricao'];
        // Converter preço para float, substituindo vírgula por ponto (ex: 12,50 -> 12.50)
        $preco = floatval(str_replace(',', '.', $_POST['preco']));
        $ref_categoria = (int) $_POST['categoria'];
        $ref_user = (int) $_SESSION['user'];
        $localizacao = $_POST['localizacao'];
        $nome_contato = $_POST['nome'];
        $email_contato = $_POST['email'];
        $telefone_contato = $_POST['telefone'];
        $data_insercao = date("Y-m-d H:i:s");

        // Upload da imagem (apenas a primeira)
        if ($_FILES['imagens']['error'][0] == 0) {
            $nome_imagem = basename($_FILES['imagens']['name'][0]);
            $capa = uniqid() . "_" . $nome_imagem;
            $caminho_destino = "../uploads/" . $capa;

            if (!is_dir("../uploads")) {
                mkdir("../uploads", 0777, true);
            }

            if (!move_uploaded_file($_FILES['imagens']['tmp_name'][0], $caminho_destino)) {
                echo "Erro no upload da imagem.";
                exit;
            }
        } else {
            $capa = "default.png"; // imagem padrão caso não envie
        }

        // Aqui assumes que a tabela `anuncios` tem colunas para os contactos;
        // caso contrário, tens que ajustar a tabela ou guardar os contactos noutra tabela.
        $query = "INSERT INTO anuncios 
            (nome_produto, descricao, preco, ref_categoria, ref_user, localizacao, capa, data_insercao, nome_contato, email_contato, telefone_contato)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = mysqli_prepare($link, $query);
        if (!$stmt) {
            die("Erro na preparação da query: " . mysqli_error($link));
        }

        mysqli_stmt_bind_param(
            $stmt,
            "ssdisssssss",
            $nome_produto,
            $descricao,
            $preco,
            $ref_categoria,
            $ref_user,
            $localizacao,
            $capa,
            $data_insercao,
            $nome_contato,
            $email_contato,
            $telefone_contato
        );

        if (mysqli_stmt_execute($stmt)) {
            header("Location: ../../index.php"); // redireciona para homepage
            exit;
        } else {
            echo "Erro ao inserir anúncio: " . mysqli_stmt_error($stmt);
        }
    } else {
        echo "<script>alert('Por favor, preenche todos os campos obrigatórios.'); history.back();</script>";
    }
}
?>
