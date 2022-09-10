<?php
require_once('init.php');
echo "<pre>";

$produtos_url = "https://dummyjson.com/products?limit=100&select=id,brand,category,thumbnail,title,price,description";
$categs_url = "https://dummyjson.com/products/categories";


$produtos = json_decode(file_get_contents($produtos_url),true)['products'];
$categs = json_decode(file_get_contents($categs_url),true);

#print_r($produtos);die;
#print_r($categs);die;

foreach($categs as $categ){
    #insertCateg($categ);
}
foreach($produtos as $produto){
    #insertProduto($produto);
}



function insertProduto(array $produto): void {
    global $pdo;

    extract($produto);#id,brand,category,thumbnail,title,price,description

    $categ_id = getCategId($category);
    $nome = $title .' ('.$brand.')';
    $nome = ucfirst($nome);
    $preco = (float) $price;
    $desc = $description;
    $img = saveImg($id,$nome,$thumbnail);

    $sql = "INSERT INTO produtos 
    (id,nome,preco,img,categ_id,`desc`) 
    VALUES 
    (:id,:nome,:preco,:img,:categ_id,:desc)
    ;";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id',$id);
    $stmt->bindParam(':nome',$nome);
    $stmt->bindParam(':preco',$preco);
    $stmt->bindParam(':img',$img);
    $stmt->bindParam(':categ_id',$categ_id);
    $stmt->bindParam(':desc',$desc);
    
    try{
        $stmt->execute();
        echo "OK prod<br>";
    } catch(Exception $e){
        echo $e->getMessage();
    }

}

function saveImg(int $id,string $nome,string $url_img){
    $img = file_get_contents($url_img);
    $parts = explode(".",$url_img);
    $ext = end($parts);

    $nome_img = $id.'-'.$nome;
    $dir = "img/";
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $nome_img)));
    $slug = trim($slug,"-");
    $nome_img_ext = $slug.'.'.$ext;
    $file = $dir.$nome_img_ext;
    file_put_contents($file,$img);
    return $nome_img_ext;
}

function getCategId(string $nome): int {
    global $pdo;

    $sql = "SELECT id FROM categorias WHERE nome=:nome;";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome',$nome);

    if($stmt->execute()){
        if($stmt->rowCount()>0){
            $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $res[0]['id'];
        } else {
            return 0;
        }
    }
    return 0;

}

function insertCateg(string $nome) {
    global $pdo;
    $nome = ucfirst($nome);
    
    $sql = "INSERT INTO categorias (nome) VALUES (:nome);";

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':nome',$nome);
    
    try{
        $stmt->execute();
        echo "OK categ<br>";
    } catch(Exception $e){
        echo $e->getMessage();
    }
}

