<?php
ini_set("display_errors", 0);
ini_set('default_charset', 'UTF-8');
setlocale(LC_ALL, 'pt_BR');
//date_default_timezone_set('America/Sao_Paulo');

//$connection = array(
//    'host' => 'localhost',
//    'user' => 'root',
//    'pass' => 'root10741961',
//    'db' => 'foletto'
//);
if (phpversion() < 7.1) {
    die('Versão PHP: ' . phpversion() . '. Versão necessária >= 7.1');
}
require_once "helpers.php";
$connection = array(
    'host' => 'localhost',
    'user' => 'root',
    'pass' => 'root10741961',
    'db' => 'at'
);

$user = "admin";
$senha = "admin";

session_start();

//function __autoload($class_name)
//{
//    require_once 'libs/' . $class_name . '_class.php';
//}
spl_autoload_register(function ($class_name) {
    require_once 'libs/' . $class_name . '_class.php';
});

$mysql = new mysql($connection);
$util = new util;
$start = new start($mysql);
