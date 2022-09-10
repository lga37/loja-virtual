<?php require('_header.php') ?>
<h1>LISTAGEM</h1>

<div class="album py-5 bg-light">
    <div class="container">

        <?php
        $limit = 20;
        $produtos = getProdutos($limit);
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