<main class="body_index">


    <div class="container my-4">
        <h3 class="fw-bold verde_escuro mb-0">Os meus pedidos</h3>
        <p class="verde_claro">Acompanhe o estado dos seus pedidos</p>
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
INNER JOIN 
    medidas ON anuncios.ref_medida = medidas.id_medida 
INNER JOIN 
    encomendas ON anuncios.id_anuncio = encomendas.ref_anuncio 
INNER JOIN 
    estados ON encomendas.ref_estado = estados.id_estado 
WHERE 
    encomendas.ref_comprador = ?";



        if (mysqli_stmt_prepare($stmt, $query)) {
        mysqli_stmt_bind_param($stmt, 'i', $id_user);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt,$id_encomenda, $id_anuncio, $nome_produto, $medida, $capa, $estado,$id_estado, $quantidade, $preco);

        while(mysqli_stmt_fetch($stmt)) { ?>
            <!-- Lista de pedidos -->
            <div class="row gy-3 mb-3">
                <div class="col-12">
                    <div class="card shadow-sm position-relative overflow-hidden verde_claro_bg h-100">
                        <a href="../Paginas/detalhes_encomendas.php?id=<?php echo htmlspecialchars($id_encomenda); ?>" class="stretched-link text-decoration-none"></a>
                        <div class="row g-0 align-items-stretch">
                            <!-- Imagem -->
                            <div class="col-3">
                                <div class="h-100">
                                    <img src="../uploads/capas/<?php echo htmlspecialchars($capa); ?>" class="img-fluid w-100 h-100 rounded-start object-fit-cover" alt="Imagem do produto">
                                </div>
                            </div>

                            <!-- Texto central -->
                            <div class="col-6 d-flex flex-column">
                                <div class="card-body py-1 px-2 d-flex flex-column h-100">
                                    <h3 class="card-title fw-bold verde_escuro mb-1 text-truncate">
                                        <?php echo htmlspecialchars($nome_produto); ?>
                                    </h3>
                                    <p class="mb-1 text-truncate verde_escuro fw-bold">
                                        <?php echo htmlspecialchars($quantidade); ?> <?php echo htmlspecialchars($medida); ?>
                                    </p>
                                    <p class="mb-0 mt-auto">
                                        <small class="text-muted ">Estado: <?php echo htmlspecialchars($estado); ?></small>
                                    </p>
                                </div>
                            </div>

                            <!-- Ícone + Preço -->
                            <div class="col-3 d-flex flex-column justify-content-between align-items-end px-2 py-1">
                                <div>
                                    <i class="bi bi-arrow-up-right verde_escuro fs-5"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold verde_escuro m-0"><?php echo htmlspecialchars($preco); ?>€</h4>
                                </div>
                            </div>
                            <!-- Botão Avaliar Vendedor se estado = 3 -->
                            <?php if ($id_estado == 3): ?>
                                <div class="d-flex" style="height: 2.5rem;">
                                    <button class="btn btn-success w-100 rounded-0 rounded-bottom"
                                            data-id="<?php echo $id_encomenda; ?>"
                                            onclick="avaliarVendedor(this)">
                                        Avaliar Vendedor
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
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