<?php

use PHPMailer\PHPMailer\PHPMailer;

function enviaEmail($email, $nome, $assunto, $msg)
{

    #https://support.google.com/accounts/answer/185833?hl=en
    #https://myaccount.google.com/apppasswords
    $mail = new PHPMailer(true);      
   
    try {
        $mail->AddAddress($email,$nome);
        $mail->Subject = $assunto;
        $mail->MsgHTML($msg);

        $mail->FromName = SITE;
        $mail->From = 'voipgus@gmail.com';
        #$mail->SMTPDebug = 2;                          
        $mail->isSMTP();                               
        $mail->Host = 'smtp.gmail.com';               
        $mail->SMTPAuth = true;                        
        $mail->Username = 'voipgus@gmail.com';         
        $mail->Password = 'itivpejzvcxablpd';                          
        $mail->SMTPSecure = 'tls';                    
        $mail->Port = 587;                            
    
        $mail->send();
        echo 'Email enviado com sucesso';

    } catch (\PHPMailer\PHPMailer\Exception $e) {
        echo $e->errorMessage(); //Pretty error messages from PHPMailer
    } catch (Exception $e) {
        echo $e->getMessage(); //Boring error messages from anything else!
    }

}

function insertPedido($cod,$itens,$total,$nome,$email,$endereco): int {
    global $pdo;

    $sql = "INSERT INTO pedidos 
    (cod,itens,total,nome,email,endereco) 
    VALUES 
    (:cod,:itens,:total,:nome,:email,:endereco)
    ;";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':cod',$cod);
    $stmt->bindParam(':itens',$itens);
    $stmt->bindParam(':total',$total);
    $stmt->bindParam(':nome',$nome);
    $stmt->bindParam(':email',$email);
    $stmt->bindParam(':endereco',$endereco);
    
    try{
        $stmt->execute();
        return $pdo->lastInsertId();
    } catch(Exception $e){
        echo $e->getMessage();
        return 0;
    }
}


function getPedido(string $cod): bool|array {
    global $pdo;

    $sql = "SELECT * FROM pedidos WHERE cod=:cod;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':cod',$cod);

    if($stmt->execute()){
        if($stmt->rowCount()>0){
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res[0];
        } else {
            return false;
        }
    }
    return false;

}

function itensCarrinhoToString(array $itens): string {
    $html = "";
    foreach($itens as $id=>$item){
        extract($item); #id, nome, preco, qtd
        $html .= "[$id] $nome R$ $preco x $qtd <br>";
    }
    return $html;
}


function formataArray(array $arr, int $pai = 0): array
{
    $items = [];
    foreach ($arr as $item) {
        if ($item['pai'] == $pai) {
            $item['filhos'] = isset($item['filhos']) ? $item['filhos'] : formataArray($arr, $item['id']);
            $items[] = $item;
        }
    }
    return $items;
}

function menuHtml(array $array): string
{
    $html = '';
    foreach ($array as $item) {
        $html .= '<ul><li>';
        $html .= '<a href="#' . $item['id'] . '">' . $item['nome'] . '</a>';
        $html .= menuHtml($item['filhos']);
        $html .= '</li></ul>';
    }
    return $html;
}

function menuSimples(array $categs): void
{

    echo "<ul class='list-group'>";
    foreach ($categs as $categ) {
        echo "<a class='text-decoration-none' href='listagem.php?categ_id={$categ['id']}'>
        <li class='text-uppercase list-group-item d-flex justify-content-between align-items-center'>
            {$categ['nome']}
            <span class='badge bg-primary rounded-pill'>{$categ['total']}</span>
        </li>
        </a>
        ";
    }
    echo "</ul>";
}

function formataPreco(float $preco)
{
    return sprintf("R$ %.2f", $preco);
}

function getCategs()
{
    global $pdo;

    $q = "SELECT categorias.*, COUNT(produtos.id) AS total 
    FROM categorias
    JOIN produtos ON (categorias.id = produtos.categ_id)
    GROUP BY categorias.id;";

    #$q = "SELECT * FROM categorias;";
    $sth = $pdo->prepare($q);
    $sth->execute();

    $categs = $sth->fetchAll(PDO::FETCH_ASSOC);
    #echo "<pre>";print_r($categs);die;
    return $categs;
}


function vitrine(array $produtos, int $colunas)
{

    echo '<div class="row row-cols-' . $colunas . ' g-4">';
    $img_fluid = count($produtos) == 1 ? 'img-fluid' : 'img-vitrine';
    foreach ($produtos as $k => $produto) {
        extract($produto); #$id $nome $preco $categ_id $img $desc
        $preco = formataPreco($preco);
        $div = <<<div
        <div class="col">
            <a href="detalhes.php?id=$id" class="text-decoration-none">
                <div class="card shadow-sm">
                <img class='$img_fluid bd-placeholder-img card-img-top' src='img/$img'>
                    <div class="card-body">
                        <p class="card-text">$nome</p>
                        <p class="link-dark"">$preco</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <div class="btn-group">
                                <a href="carrinho.php?a=add&id=$id"
                                    class="btn btn-outline-success">Comprar</a>
                                <a href="#"
                                    class="ms-2 btn btn-outline-primary">Share</a>
                            </div>
                            
                        </div>
                    </div>
                    <div class="card-footer text-truncate lh-lg">
                        $desc
                    </div>
                </div>
            </a>
        </div>
        div;

        echo $div;
    }
    #echo "</table>";
    echo "</div>";
}


function getProdutos(int $limit = 0)
{
    global $pdo;
    $categ_id = $_GET['categ_id'] ?? false;
    $keyword = $_POST['keyword'] ?? false;


    $sql = "SELECT * FROM produtos ";

    if ($categ_id || $keyword) {
        $sql .= " WHERE ";
    }

    $e = ($categ_id && $keyword) ? " AND " : "";

    if ($categ_id) {
        $categ_id = (int) $categ_id;
        $sql .= "categ_id=:categ_id";
    }
    if ($keyword) {
        $keyword = '%' . $keyword . '%';
        $sql .= $e . "nome LIKE :keyword";
    }

    if ($limit > 0) {
        $sql .= " LIMIT $limit";
    }

    $sql .= ";";

    $stmt = $pdo->prepare($sql);

    if ($categ_id) {
        $stmt->bindParam(":categ_id", $categ_id, PDO::PARAM_INT);
    }

    if ($keyword) {
        $stmt->bindParam(":keyword", $keyword, PDO::PARAM_STR);
    }

    try {
        $stmt->execute();
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $produtos;
}

function getProdutosByRand(int $limit = 3)
{
    global $pdo;
    $sql = "SELECT * FROM produtos ORDER BY RAND() LIMIT $limit;";
    $stmt = $pdo->prepare($sql);
    try {
        $stmt->execute();
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    $produtos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $produtos;
}


function getCarrinho()
{
    if (isset($_GET['a'])) {
        switch ($_GET['a']) {
            case 'add':
                $id = (int) $_GET['id'];

                $item = getProdutoById($id);

                if (is_array($item)) {
                    $item['qtd'] = 1;
                    $_SESSION['cart'][$id] = $item;
                }
                break;

            case 'del':
                $id = (int) $_GET['id'];
                if (isset($_SESSION['cart'][$id])) {
                    unset($_SESSION['cart'][$id]);
                }
                break;

            case 'incr':
                $id = (int) $_GET['id'];
                if (isset($_SESSION['cart'][$id])) {
                    $_SESSION['cart'][$id]['qtd']++;
                }
                break;

            case 'decr':
                $id = (int) $_GET['id'];
                if (isset($_SESSION['cart'][$id])) {
                    if ($_SESSION['cart'][$id]['qtd'] > 1) {
                        $_SESSION['cart'][$id]['qtd']--;
                    } else {
                        unset($_SESSION['cart'][$id]);
                    }
                }
                break;

            case 'checkout':

                #print_r($_POST);die;
                $email = $_POST['email'];
                $nome = $_POST['nome'];
                $endereco = $_POST['endereco'];
                $total = 0;
                foreach ($_SESSION['cart'] as $id => $produto) {
                    $total += $produto['preco'] * $produto['qtd'];
                }
                #echo "<pre>";print_r($_SESSION['cart']);die;
                $cod = uniqid();
                $itens = json_encode($_SESSION['cart']);

                $id = insertPedido($cod,$itens,$total,$nome,$email,$endereco);
                if($id > 0){
                    $assunto = "Compra # $cod ".date('d/m/Y H:i:s');
                    $dados = implode('<br>',$_POST);
                    $itens = itensCarrinhoToString($_SESSION['cart']);
                    $msg = "Seu pedido : $cod <br> $dados <hr> $itens <hr> R$ $total";
                    enviaEmail($email, $nome, $assunto, $msg);
                    $redir = 'pedido.php?cod='.$cod;
                    unset($_SESSION['cart']);
                    echo "<script>window.location.href='".$redir."';</script>";die;
                    #header($redir);

                } else {
                    echo "erro na gravacao do pedido";
                }
                break;


            case 'cancel':
                unset($_SESSION['cart']);
                break;
        }
    }
}


function showCarrinho()
{
    $total = 0;
    $html = '';
    echo '<table class="table table-striped table-hover">';
    foreach ($_SESSION['cart'] as $id => $produto) {
        extract($produto); #$id, $nome, $preco, $qtd
        $total_item = $preco * $qtd;
        $preco = number_format($preco, 2, ".", "");
        $total_item = number_format($preco * $qtd, 2, ".", "");

        $tr = <<<tr
            <tr>
                <td><img src="img/$img" class="img-carrinho rounded-circle" alt="$nome"></td>
                <td>$nome</td>
                <td>$preco</td>
                <td>
                    <div class="btn-group me-2">
                        <a href="?a=decr&id=$id" class="btn btn-outline-secondary">-</a>
                        <a href='#' class="btn disabled">$qtd</a>
                        <a href="?a=incr&id=$id" class="btn btn-outline-secondary">+</a>
                    </div>
                </td>
                <td>$total_item</td>
                <td><a href="?a=del&id=$id" class="btn btn-sm btn-outline-danger">del</a></td>
            </tr>
        tr;
        echo $tr;
        #echo sprintf("<a href='?a=del&id=%d'>del</a>  [%d] %s - R$ %.02f x %d = R$ %.02f<br>",$id, $id, $nome, $preco, $qtd, $qtd * $preco);
        $total += $total_item;
    }
    $total = number_format($total, 2, ".", "");
    echo '<tr><td colspan=4></td><td><h2>R$' . $total . '</h2></td><td></td></tr>';
    echo '</table>';

    echo '<a href="?a=cancel" class="btn btn-lg btn-outline-danger">Cancelar</a>';
    #echo '<a href="?a=pagar" class="ms-4 btn btn-lg btn-outline-success">Pagar</a>';

    echo '<button class="ms-4 btn btn-lg btn-outline-success" data-bs-toggle="modal" data-bs-target="#exampleModal">Checkout</button>';

}


function getProdutoById(int $id)
{
    global $pdo;
    $sql = "SELECT * FROM produtos WHERE id=:id;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);

    try {
        $stmt->execute();
    } catch (Exception $e) {
        echo $e->getMessage();
    }

    $produto = $stmt->fetch(PDO::FETCH_ASSOC);
    #echo "<pre>";print_r($produto);die;
    return $produto;
}
