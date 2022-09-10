<?php require('_header.php') ?>
<h1>DETALHES</h1>


<div class="album py-5 bg-light">
    <div class="container">

        <?php
$id = (int) $_GET['id'];
$produto = getProdutoById($id);
#echo "<pre>";print_r([$produto]);die;

$produtos = [$produto];
$limit = 1;
$colunas = 1;
vitrine($produtos, $limit, $colunas);

        ?>

    </div>
</div>

<?php require('_footer.php') ?>