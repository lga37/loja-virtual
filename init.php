<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once('vendor/autoload.php');

session_start();
if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

define('SITE','guZZZ');
define('RAIZ','http://localhost/youtube6/');


try {
    $user = "root";
    $pass = "123";
    $pdo = new PDO('mysql:host=localhost;dbname=youtube', $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
