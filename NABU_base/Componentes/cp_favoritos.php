<?php
require_once '../Connections/connection.php';

if (!isset($_SESSION['id_user'])) {
    header("Location: ../paginas/login.php");
    exit();
}

$id_user = $_SESSION['id_user'];
$link = new_db_connection();

$query = "
    SELECT a.id_anuncio, a.nome_produto, a.preco, a.capa, c.nome_categoria
    FROM favoritos f
    INNER JOIN anuncios a ON f.anuncios_id_anuncio = a.id_anuncio
    LEFT JOIN categorias c ON a.ref_categoria = c.id_categoria
    WHERE f.users_id_user = ?
    ORDER BY f.data_insercao DESC
";

$stmt = mysqli_stmt_init($link);
mysqli_stmt_prepare($stmt, $query);
mysqli_stmt_bind_param($stmt, "i", $id_user);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $id_anuncio, $nome_produto, $preco, $capa, $nome_categoria);
?>

<main class="body_index">

    <section class="sec-favoritos">
        <div class="px-lg-5">
            <h3 class="fw-bold verde_escuro mb-0 ">Os meus favoritos</h3>
            <p class="verde_claro">Conteúdos que mais lhe interessam</p>

            <div class="row g-3">
                <?php
                $tem_favoritos = false;
                while (mysqli_stmt_fetch($stmt)) {
                    $tem_favoritos = true;
                    // Ícone preenchido porque é favorito
                    $icon_class = "material-symbols-filled";
                    ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="card rounded-4 shadow-sm border-0 position-relative card_pesquisa">

                            <!-- Ícone de favorito -->
                            <div class="position-absolute top-0 end-0 m-2 d-flex justify-content-center align-items-center rounded-circle shadow favorite-circle">
            <span
                    class="<?= $icon_class ?> verde_escuro btn-favorito mt-0 fs-4"
                    data-id="<?= htmlspecialchars($id_anuncio) ?>"
                    role="button"
                    style="cursor:pointer;"
                    aria-label="Favoritar produto"
            >
                favorite
            </span>
                            </div>

                            <!-- Link do produto -->
                            <a href="../paginas/produto.php?id=<?= htmlspecialchars($id_anuncio) ?>" style="text-decoration: none">

                                <!-- Imagem -->
                                <div class="imagem_card_pesquisa">
                                    <img
                                            src="../uploads/capas/<?= htmlspecialchars($capa) ?>"
                                            alt="<?= htmlspecialchars($nome_produto) ?>"
                                            class="card-img-top rounded-4 img_hp_card"
                                    >
                                </div>

                                <!-- Título e categoria -->
                                <div class="card-body m-2 pt-2 px-2 pb-0">
                                    <h6 class="card-title mb-1 fw-semibold verde_escuro align-middle fs-3 text-truncate">
                                        <?= htmlspecialchars($nome_produto) ?>
                                    </h6>
                                    <small class="text-muted"><?= htmlspecialchars($nome_categoria) ?></small>
                                </div>
                            </a>

                            <hr class="linha-card verde_escuro">

                            <!-- Rodapé com avaliação e preço -->
                            <div class="card-body m-2 pt-0 pb-2 px-2">
                                <div class=" align-items-center text-end">

                                    <small class="fw-bolder verde_escuro fs-5">
                                        <?= number_format($preco, 2, ',', ' ') ?> €
                                    </small>
                                </div>
                            </div>

                        </div>
                    </div>

                    <?php
                }
                if (!$tem_favoritos) {
                    echo '<p class="text-center verde_escuro fs-4">Ainda não tem favoritos adicionados.</p>';
                }
                mysqli_stmt_close($stmt);
                mysqli_close($link);
                ?>
            </div>
        </div>
    </section>
</main>

<script>
    document.querySelectorAll(".btn-favorito").forEach(btn => {
        const toggleFavorito = function(event) {
            event.preventDefault();

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
                        alert(data);
                        return;
                    }

                    // Alterna ícone (visual imediato)
                    if (this.classList.contains("material-symbols-outlined")) {
                        this.classList.remove("material-symbols-outlined");
                        this.classList.add("material-symbols-filled");
                    } else {
                        this.classList.remove("material-symbols-filled");
                        this.classList.add("material-symbols-outlined");
                    }

                    // Recarrega a página para atualizar lista de favoritos
                    location.reload();
                })
                .catch(error => {
                    console.error("Erro no AJAX:", error);
                    alert("Erro ao tentar alterar favoritos. Tente novamente.");
                });
        };

        btn.addEventListener("click", toggleFavorito);
        btn.addEventListener("touchstart", toggleFavorito);
    });
</script>
