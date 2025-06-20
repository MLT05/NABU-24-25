<main class="body_index">

    <div class="container py-2">
        <div class="text-center mb-4"></div>
        <h2 class="mt-2 verde_escuro">Definições</h2>
    </div>

    <div class="card border-0 shadow-sm ">
        <div class="list-group  list-group-flush">

            <a href="../Paginas/perfil_details.php"
                class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">

                Detalhes do Perfil e da Conta
            </a>

            <a href="../Paginas/detalhes_Pagamento.php"
                class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">

                Pagamentos
            </a>

            <a href="../Paginas/seguranca.php"
                class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">

                Segurança
            </a>

        </div>
    </div>
    </div>
    <div class="container py-2">
    </div>
    <div class="card border-0 shadow-sm ">
        <div class="list-group  list-group-flush">

            <a href="../Paginas/config_notificacoes.php"
                class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">

                Notificações
            </a>
           
        </div>
    </div>
    </div>
    <div class="container py-2">
    </div>

    <div class="card border-0 shadow-sm ">
        <div class="list-group  list-group-flush">

            <a href="#"
                class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">

                
                <br>
                <hr>

                <div class="mb-3">
                    <label for="idioma" class="form-label fw-semibold verde_escuro">Idioma da Aplicação*</label>
                    <select class="form-select bg-success bg-opacity-25 fw-light verde_escuro" id="idioma" name="idioma" required>
                        <?php
                        $link = new_db_connection();
                        $stmt = mysqli_stmt_init($link);
                        $query = "SELECT id_idioma.idiomas, idioma.idiomas FROM idiomas";
                        if (mysqli_stmt_prepare($stmt, $query)) {
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_bind_result($stmt, $id_idioma, $idioma);
                            while (mysqli_stmt_fetch($stmt)) {
                                echo '<option value="' . $id_idioma . '">' . htmlspecialchars($idioma) . '</option>';
                            }
                            mysqli_stmt_close($stmt);
                        }
                        mysqli_close($link);
                        ?>
                    </select>
                </div>
            </a>

        </div>
    </div>
    </div>

    <div class="container py-2">
    </div>

    <div class="card border-0 shadow-sm ">
        <div class="list-group  list-group-flush">

            <a href="#"
                class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">
                Mudar tema Claro/Escuro
            </a>
            <div class="form-check form-switch verde_claro_bg">
                <br>
                <form action="post">
                    <label class="theme-toggle">
                        <input type="checkbox">
                        <span class="slider round"></span>
                    </label>

                </form>

            </div>
        </div>
    </div>

    <div class="container py-2">
    </div>

    <div class="card border-0 shadow-sm ">
        <div class="list-group  list-group-flush">

            <a href="../Paginas/privacidade_def.php"
                class="verde_escuro list-group-item list-group-item-action d-flex align-items-center verde_claro_bg">

                Definições de Privacidade
            </a>

        </div>
    </div>
    </div>


</main>