<?php
require_once '../Connections/connection.php';
$link = new_db_connection();

if (!isset($_SESSION['id_user'])) {
    header("Location: ../Paginas/login.php");
    exit;
}

$user_id = $_SESSION['id_user'];

$stmt_sidebar = mysqli_prepare($link, "SELECT nome, pfp FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt_sidebar, "i", $user_id);
mysqli_stmt_execute($stmt_sidebar);
mysqli_stmt_bind_result($stmt_sidebar, $username, $pfp);
mysqli_stmt_fetch($stmt_sidebar);
mysqli_stmt_close($stmt_sidebar);

$query_users = "SELECT id_user, nome, pfp FROM users WHERE id_user != ?";
$stmt_users = mysqli_prepare($link, $query_users);
mysqli_stmt_bind_param($stmt_users, "i", $user_id);
mysqli_stmt_execute($stmt_users);
$result_users = mysqli_stmt_get_result($stmt_users);
?>
<!DOCTYPE html>
<html lang="pt">

<head>
    <meta charset="UTF-8">
    <title>Mensagens - NABU</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="styles/style.css">
    <style>
        /* O seu CSS completo aqui... */
        body {
            font-family: 'Poppins', sans-serif;
        }

        .perfil-wrapper {
            display: flex;
        }

        .sidebar {
            width: 250px;
            background: #f4f0f7;
            padding: 30px;
            min-height: 100vh;
            border-right: 1px solid #e0e0e0;
        }

        .sidebar img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .sidebar h5 {
            margin-top: 15px;
            font-weight: 600;
            color: #333;
        }

        .sidebar a {
            display: block;
            margin: 10px 0;
            padding: 10px;
            color: #555;
            text-decoration: none;
            font-weight: 500;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .sidebar a:hover {
            background-color: #e8e2ee;
            color: #5a00a1;
        }

        .sidebar a.active {
            background-color: #5a00a1;
            color: white;
            font-weight: bold;
        }

        .chat-container {
            display: flex;
            flex-grow: 1;
            height: calc(100vh - 60px);
        }

        .conversations-list {
            width: 350px;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            background-color: #fff;
        }

        .conversations-list .header {
            padding: 20px 15px;
            font-size: 1.3rem;
            font-weight: 700;
            color: #5a00a1;
            border-bottom: 1px solid #e0e0e0;
        }

        .conversations-list .search-bar {
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        .conversations-list .search-bar input {
            border-radius: 20px;
            background-color: #f5f5f5;
            border: none;
        }

        .users-list {
            overflow-y: auto;
            flex-grow: 1;
        }

        .user-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            cursor: pointer;
            transition: background-color 0.2s ease;
            border-bottom: 1px solid #f0f0f0;
        }

        .user-item:hover {
            background-color: #f7f3ff;
        }

        .user-item.active {
            background-color: #ede7f6;
            border-left: 4px solid #5a00a1;
            padding-left: 11px;
        }

        .user-item img {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 15px;
        }

        .user-info .username {
            font-weight: 600;
            color: #333;
        }

        .user-info .last-message {
            font-size: 0.85rem;
            color: #777;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 200px;
        }

        .chat-window {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }

        .chat-header {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            background-color: #f7f3ff;
            border-bottom: 1px solid #e0e0e0;
        }

        .chat-header img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: 50%;
            margin-right: 15px;
        }

        .chat-header .username {
            font-weight: bold;
            font-size: 1.1rem;
        }

        .chat-messages {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
            background-color: #e8e2ee;
        }

        .message-bubble {
            max-width: 65%;
            padding: 10px 15px;
            border-radius: 18px;
            line-height: 1.4;
            word-wrap: break-word;
        }

        .message-bubble img {
            max-width: 100%;
            border-radius: 15px;
            margin-top: 5px;
            cursor: pointer;
        }

        .message-bubble audio {
            width: 250px;
            margin-top: 5px;
        }

        .message-bubble.sent {
            background-color: #5a00a1;
            color: white;
            align-self: flex-end;
            border-bottom-right-radius: 4px;
        }

        .message-bubble.received {
            background-color: #fff;
            color: #333;
            align-self: flex-start;
            border: 1px solid #e0e0e0;
            border-bottom-left-radius: 4px;
        }

        .chat-input {
            padding: 10px 15px;
            background-color: #f7f3ff;
            border-top: 1px solid #e0e0e0;
        }

        .chat-input .form-control {
            border-radius: 20px;
            border: 1px solid #ccc;
        }

        .chat-input .btn {
            background: transparent;
            border: none;
            color: #5a00a1;
            font-size: 1.2rem;
        }

        .chat-input .btn-primary {
            background-color: #5a00a1;
            color: white;
            border-radius: 50%;
            width: 45px;
            height: 45px;
        }
    </style>
</head>

<main class="body_index">
    <div class="perfil-wrapper">

        <div class="chat-container" data-my-id="<?= htmlspecialchars($_SESSION['id_user']) ?>">
            <div class="conversations-list">
                <div class="header">Conversas</div>
                <div class="search-bar">
                    <input type="text" class="form-control" placeholder="Pesquisar...">
                </div>
                <div class="users-list">

                    <?php while ($user = mysqli_fetch_assoc($result_users)): ?>
                        <?php
                        $pfp_filename = $user['pfp'];
                        $pfp_path = (!empty($pfp_filename) && file_exists("../uploads/pfp/" . $pfp_filename)) ? $pfp_filename : 'defaultpfp.png';
                        ?>
                        <div class="user-item" data-userid="<?= $user['id_user'] ?>" data-username="<?= htmlspecialchars($user['nome']) ?>" data-pfp="<?= htmlspecialchars($pfp_path) ?>">
                            <img src="../uploads/pfp/<?= htmlspecialchars($pfp_path) ?>" alt="pfp">
                            <div class="user-info">
                                <div class="username"><?= htmlspecialchars($user['nome']) ?></div>
                                <div class="last-message">Clique para conversar...</div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>

            <div class="chat-window">
                <div class="chat-header" style="display: none;"></div>
                <div class="chat-messages">
                    <div class="text-center text-muted mt-5">Selecione uma conversa para começar.</div>
                </div>
                <div class="chat-input" style="display: none;">
                    <form id="message-form" class="input-group align-items-center" enctype="multipart/form-data">
                        <input type="file" id="image-input" name="image_file" accept="image/*" style="display: none;">
                        <button class="btn" type="button" id="image-button-trigger" title="Enviar Imagem"><i class="fas fa-image"></i></button>
                        <button class="btn" type="button" id="record-audio-button" title="Gravar Áudio"><i class="fas fa-microphone"></i></button>
                        <input type="text" id="message-input" name="message_content" class="form-control" placeholder="Escreva uma mensagem..." autocomplete="off">
                        <button class="btn btn-primary" type="submit" title="Enviar"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="scripts/sc_chat.js"></script>


</html>