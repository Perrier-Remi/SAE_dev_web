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

$dispatcher = new Dispatcher($action);
$dispatcher->run();
