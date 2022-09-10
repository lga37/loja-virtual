<?php require('_header.php') ?>
<h1>HOME</h1>
<div class="p-5 mb-4 bg-light rounded-3">
    <div class="container-fluid py-5">
        <h1 class="display-5 fw-bold">Custom jumbotron</h1>
        <p class="col-md-8 fs-4">Using a series of utilities, you can create this jumbotron, just
            like the one in previous versions of Bootstrap. Check out the examples below for how you
            can remix and restyle it to your liking.</p>
            <p>https://github.com/lga37/loja-virtual</p>
        <button class="btn btn-primary btn-lg" type="button">Example button</button>
    </div>
</div>
<div class="album py-5 bg-light">
    <div class="container">

        <?php
        $limit = 20;
        $produtos = getProdutosByRand($limit);
        $colunas = 3;
        if (empty($produtos)) {
            echo "<h1>Nenhum Registro</h1>";
        } else {
            vitrine($produtos, $colunas);
        }
        ?>

    </div>
</div>

<?php require('_footer.php') ?>