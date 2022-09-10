<?php
require('_header.php');
getCarrinho();
?>
<h1>CARRINHO</h1>


<div class="album py-5 bg-light">
    <div class="container">

        <?php

        if (isset($_SESSION['cart']) && count($_SESSION['cart']) > 0) {
            showCarrinho();
        } else {
            echo "<h1>Carrinho Vazio</h1>";
        }


        echo '<div class="mt-4 border border-4 rounded border-danger p-3">';
        echo '<h1 class="card-header my-4">Aproveite também ...</h1>';
        $limit = 3;
        $produtos = getProdutosByRand($limit);
        $colunas = 3;
        if (empty($produtos)) {
            echo "<h1>Nenhum Registro</h1>";
        } else {
            vitrine($produtos, $colunas);
        }
        echo '</div>';
        ?>





    </div>
</div>




<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="?a=checkout" method="POST">
                <div class="modal-body">

                    <div class="mb-3 row">
                        <label for="nome" class="col-sm-2 col-form-label">Nome</label>
                        <div class="col-sm-10">
                            <input type="text" name="nome" class="form-control" id="nome">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="email" class="col-sm-2 col-form-label">Email</label>
                        <div class="col-sm-10">
                            <input type="text" name="email" class="form-control" id="email">
                        </div>
                    </div>
                    <div class="mb-3 row">
                        <label for="endereco" class="col-sm-2 col-form-label">Endereco</label>
                        <div class="col-sm-10">
                            <input type="text" name="endereco" class="form-control" id="endereco">
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <a class="btn btn-secondary" data-bs-dismiss="modal">Close</a>
                    <button class="btn btn-primary">Finalizar</button>
                </div>
            </form>
        </div>
    </div>
</div>


<?php require('_footer.php') ?>