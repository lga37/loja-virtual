<?php require('_header.php') ?>
<h1>PEDIDO</h1>


<div class="album py-5 bg-light">
    <div class="container">

        <?php
        $cod = (string) $_GET['cod'];
        if($pedido=getPedido($cod)){
            #echo "<pre>";print_r($pedido);echo "</pre>";
            foreach($pedido as $campo=>$valor){
                if($campo =='itens'){
                    $dados = json_decode($valor,true);
                    #echo "<pre>";print_r($dados);echo "</pre>";
                    $valor = '<hr>'.itensCarrinhoToString($dados);
                }
                echo "<b>$campo</b>: $valor<br>";
            }
            echo "<h2>Efetuar pagamento para o PIX: xyz</h2>";

        } else {
            echo "Pedido nao encontrado";
        }

        ?>

    </div>
</div>

<?php require('_footer.php') ?>