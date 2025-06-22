<main class="body_index">

    <div class="container my-4">
        <h3 class="fw-bold verde_escuro mb-0">Encomendas Recebidas</h3>
        <p class="verde_claro">Confirme as suas encomendas recebidas</p>

        <?php
        require_once '../Connections/connection.php';
        $id_user = $_SESSION['id_user'];

        $link = new_db_connection();
        $stmt = mysqli_stmt_init($link);

        $query = "SELECT 
            encomendas.id_encomenda,
            anuncios.id_anuncio, 
            anuncios.nome_produto, 
            medidas.abreviatura, 
            anuncios.capa, 
            estados.estado, 
            estados.id_estado,
            encomendas.quantidade, 
            encomendas.preco 
        FROM 
            anuncios 
        INNER JOIN medidas ON anuncios.ref_medida = medidas.id_medida 
        INNER JOIN encomendas ON anuncios.id_anuncio = encomendas.ref_anuncio 
        INNER JOIN estados ON encomendas.ref_estado = estados.id_estado 
        WHERE anuncios.ref_user = ?";

        if (mysqli_stmt_prepare($stmt, $query)) {
            mysqli_stmt_bind_param($stmt, 'i', $id_user);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_bind_result($stmt, $id_encomenda, $id_anuncio, $nome_produto, $medida, $capa, $estado, $id_estado, $quantidade, $preco);

            while (mysqli_stmt_fetch($stmt)) { ?>
                <!-- Card da encomenda -->
                <div class="row gy-3 mb-3 encomenda-card" id="encomenda_<?php echo $id_encomenda; ?>">
                    <div class="col-12">
                        <div class="card shadow-sm position-relative overflow-hidden verde_claro_bg h-100 d-flex flex-column">

                            <!-- Linha principal -->
                            <div class="row g-0 flex-grow-1 align-items-stretch">

                                <!-- Imagem -->
                                <div class="col-3">
                                    <a href="../Paginas/detalhes_encomendas.php?id=<?php echo htmlspecialchars($id_encomenda); ?>" class="text-decoration-none d-block h-100">
                                        <img src="../uploads/capas/<?php echo htmlspecialchars($capa); ?>" class="img-fluid w-100 h-100 rounded-start object-fit-cover" alt="Imagem do produto">
                                    </a>
                                </div>

                                <!-- Texto -->
                                <div class="col-6 d-flex flex-column">
                                    <a href="../Paginas/detalhes_encomendas.php?id=<?php echo htmlspecialchars($id_encomenda); ?>" class="text-decoration-none flex-grow-1 d-flex flex-column">
                                        <div class="card-body py-1 px-2 d-flex flex-column h-100">
                                            <h3 class="card-title fw-bold verde_escuro mb-1 text-truncate"><?php echo htmlspecialchars($nome_produto); ?></h3>
                                            <p class="mb-1 text-truncate verde_escuro fw-bold"><?php echo htmlspecialchars($quantidade); ?> <?php echo htmlspecialchars($medida); ?></p>
                                            <p class="mb-0 mt-auto"><small class="text-muted">Estado: <?php echo htmlspecialchars($estado); ?></small></p>
                                        </div>
                                    </a>
                                </div>

                                <!-- Preço -->
                                <div class="col-3 d-flex flex-column justify-content-between align-items-end px-2 py-1">
                                    <div><i class="bi bi-arrow-up-right verde_escuro fs-5"></i></div>
                                    <div><h4 class="fw-bold verde_escuro m-0"><?php echo htmlspecialchars($preco); ?>€</h4></div>
                                </div>
                            </div>

                            <!-- Botões (apenas se estado == 1) -->
                            <?php if (isset($id_estado) && $id_estado == 1): ?>
                                <div class="d-flex" style="height: 2.5rem;">
                                    <button class="btn btn-success w-50 rounded-0 rounded-bottom-start"
                                            data-id="<?php echo $id_encomenda; ?>"
                                            onclick="aceitarEncomenda(this)">Aceitar</button>
                                    <button class="btn btn-danger w-50 rounded-0 rounded-bottom-end"
                                            data-id="<?php echo $id_encomenda; ?>"
                                            onclick="rejeitarEncomenda(this)">Rejeitar</button>
                                </div>
                            <?php elseif (isset($id_estado) && $id_estado == 2): ?>
                            <div class="d-flex" style="height: 2.5rem;">
                                <button class="btn btn-success w-100 rounded-0 rounded-bottom"
                                        data-id="<?php echo $id_encomenda; ?>"
                                        onclick="marcarComoEntregue(this)">Marcar como entregue</button>
                            </div>
                            <?php endif; ?>

                        </div>
                    </div>
                </div>
                <?php
            }

            mysqli_stmt_close($stmt);
            mysqli_close($link);
        }
        ?>
    </div>

</main>

<!-- JavaScript AJAX -->
<script>
    function marcarComoEntregue(btn) {
        const id = btn.getAttribute('data-id');

        fetch('../scripts/sc_marcar_entregue.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id_encomenda=${id}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`encomenda_${id}`);

                    // Atualiza o texto do estado
                    const estadoEl = card.querySelector("small.text-muted");
                    if (estadoEl && data.novo_estado) {
                        estadoEl.textContent = "Estado: " + data.novo_estado;
                    }

                    // Remove o botão
                    const botoes = card.querySelector(".d-flex[style*='2.5rem']");
                    if (botoes) botoes.remove();
                } else {
                    console.error("Erro ao marcar como entregue:", data.error || "Erro desconhecido");
                }
            })
            .catch(err => {
                console.error("Erro na resposta AJAX:", err);
            });
    }

    function aceitarEncomenda(btn) {
        const id = btn.getAttribute('data-id');

        fetch('../scripts/sc_aceitar_encomenda.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id_encomenda=${id}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const card = document.getElementById(`encomenda_${id}`);

                    // Atualiza o texto do estado
                    const estadoEl = card.querySelector("small.text-muted");
                    if (estadoEl && data.novo_estado) {
                        estadoEl.textContent = "Estado: " + data.novo_estado;
                    }

                    // Remove os botões
                    const botoes = card.querySelector(".d-flex[style*='2.5rem']");
                    if (botoes) botoes.remove();
                } else {
                    console.error("Erro ao aceitar encomenda:", data.error || "Erro desconhecido");
                }
            })
            .catch(err => {
                console.error("Erro na resposta AJAX:", err);
            });
    }

    function rejeitarEncomenda(btn) {
        const id = btn.getAttribute('data-id');

        fetch('../scripts/sc_rejeitar_encomenda.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: `id_encomenda=${id}`
        })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    document.getElementById(`encomenda_${id}`).remove();
                } else {
                    console.error("Erro ao rejeitar encomenda:", data.error);
                }
            })
            .catch(err => {
                console.error("Erro na resposta AJAX:", err);
            });
    }
</script>

