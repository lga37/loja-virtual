<?php
require('init.php');
require('functions.php')

?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title><?= SITE ?></title>

    <style>
        .img-vitrine {
            max-width: 100%;
            height: 200px;
        }

        .img-carrinho {
            width: 50px;
            height: 50px;
        }
    </style>

</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= RAIZ ?>"><?= SITE ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarCollapse" aria-controls="navbarCollapse" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarCollapse">

                <ul class="navbar-nav me-auto mb-2 mb-md-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="<?= RAIZ ?>">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Link</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
                    </li>
                </ul>
                <form action="listagem.php" method="POST" class="d-flex">
                    <input class="form-control me-2" name="keyword" type="search" placeholder="Search" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">Search</button>
                </form>
            </div>
        </div>
    </nav>


    <main style="margin-top: 100px;">

        <div class="container">
            <div class="row">
                <div class="col-3">
                    <?php
                    $categs = getCategs();
                    #echo menuHtml(formataArray($categs));
                    menuSimples($categs);
                    echo '<div class="mt-4 border border-4 rounded border-info p-3">';
                    echo '<h1 class="card-header my-4">Promoções</h1>';
                    $limit = 5;
                    $produtos = getProdutosByRand($limit);
                    $colunas = 1;
                    if (empty($produtos)) {
                        echo "<h1>Nenhum Registro</h1>";
                    } else {
                        vitrine($produtos, $colunas);
                    }
                    echo '</div>';
                    ?>

                </div>
                <div class="col-9">
                    <!-- content -->