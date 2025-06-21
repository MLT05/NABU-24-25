<?php

require_once '../Connections/connection.php';

$link = new_db_connection();

// Pega o ID do utilizador logado, ou 0 se não estiver logado
$id_user = isset($_SESSION['id_user']) ? $_SESSION['id_user'] : 0;

?>

<main class="body_index">

    <div class="mt-3">

        <!-- 🔍 Pesquisa -->
        <?php
        require_once '../Componentes/cp_intro_pesquisa.php';
        require_once '../Componentes/cp_intro_categorias.php';
        ?>

        <!-- 🧺 Produtos -->
        <div class="row g-3">
            <?php
            $stmt = mysqli_stmt_init($link);

            $query = "SELECT a.id_anuncio, a.nome_produto, a.preco, m.abreviatura, a.capa,
                             EXISTS (
                                 SELECT 1 FROM favoritos f 
                                 WHERE f.users_id_user = ? AND f.anuncios_id_anuncio = a.id_anuncio
                             ) AS favorito
                      FROM anuncios a
                      INNER JOIN medidas m ON a.ref_medida = m.id_medida";

            if (mysqli_stmt_prepare($stmt, $query)) {
                mysqli_stmt_bind_param($stmt, 'i', $id_user);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $medida, $capa, $favorito);

                while (mysqli_stmt_fetch($stmt)) {
                    $icon_class = ($favorito == 1) ? "material-symbols-filled" : "material-symbols-outlined";
                    ?>
                    <div class="col-6">
                        <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                            <!-- Ícone de favorito -->
                                    <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
                                        <span
                                                class="<?= $icon_class ?> verde_escuro btn-favorito mt-0 fs-4"
                                                data-id="<?= $id_anuncio ?>"
                                                role="button"
                                                style="cursor:pointer;"
                                                aria-label="Favoritar produto"
                                        >
                                            favorite
                                        </span>
                                    </div>

                            <!-- Link do produto -->
                            <a href="../Paginas/produto.php?id=<?= $id_anuncio ?>" style="text-decoration: none">
                                <!-- Imagem -->
                                <div class="imagem_card_pesquisa">
                                    <img src="../uploads/capas/<?php echo htmlspecialchars($capa); ?>" class="card-img-top rounded-4 img_hp_card" alt="<?= htmlspecialchars($nome_produto) ?>">
                                </div>

                                <!-- Título -->
                                <div class="card-body m-2 pt-2 px-2 pb-0">
                                    <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3 text-truncate">
                                        <?= htmlspecialchars($nome_produto) ?>
                                    </h6>
                                </div>
                            </a>

                            <hr class="linha-card verde_escuro">

                            <!-- Rodapé -->
                            <div class="card-body m-2 pt-0 pb-2 px-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="verde_escuro fw-bolder fs-5">
                                        <i class="bi bi-star-fill"></i>
                                    </small>
                                    <small class="fw-bolder verde_escuro fs-5">
                                        <?= number_format($preco, 2) ?> € / <?= htmlspecialchars($medida) ?>
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>
                    <?php
                }
                mysqli_stmt_close($stmt);
            }
            ?>
        </div>
    </div>
</main>

<script>
    document.querySelectorAll(".btn-favorito").forEach(btn => {
        const toggleFavorito = function(event) {
            event.preventDefault(); // evita ação padrão se existir

            const idAnuncio = this.getAttribute("data-id");

            fetch("../Functions/ajax_favorito.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded",
                },
                body: `id_anuncio_favorito=${idAnuncio}`
            })
                .then(response => response.text())
                .then(data => {
                    console.log(data);
                    if (data.includes("⚠️")) {
                        alert(data);  // Mensagem de erro, tipo "Necessita estar logado"
                        return;
                    }

                    // Alterna o ícone só se não houve erro
                    if (this.classList.contains("material-symbols-outlined")) {
                        this.classList.remove("material-symbols-outlined");
                        this.classList.add("material-symbols-filled");
                    } else {
                        this.classList.remove("material-symbols-filled");
                        this.classList.add("material-symbols-outlined");
                    }
                })
                .catch(error => {
                    console.error("Erro no AJAX:", error);
                    alert("Erro ao tentar alterar favoritos. Tenta novamente.");
                });
        };

        btn.addEventListener("click", toggleFavorito);
        btn.addEventListener("touchstart", toggleFavorito);
    });
</script>