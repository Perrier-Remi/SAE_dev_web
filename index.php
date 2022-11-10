<?php

require_once 'vendor/autoload.php';

use \iutnc\netvod\dispatch\Dispatcher as Dispatcher;
use iutnc\netvod\bd\ConnectionFactory;

session_start();
ConnectionFactory::setConfig("src/classes/bd/db.config.ini");

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = "";
}

if (!isset($_SESSION['user']) && $action != 'add-user' && $action != 'mdp-oublie' && $action != 'mdp-oublie-confirmation' && $action != 'confirmer-inscription'){
    if ($_SESSION['co']=='succes') $action='load_profil';
    else $action = 'sign-in';
}else{
    $_SESSION['list_url'][]= $_SERVER['QUERY_STRING'];
    if (count($_SESSION['list_url'])>8) array_shift($_SESSION['list_url']);
}

$dispatcher = new Dispatcher($action);
$dispatcher->run();
