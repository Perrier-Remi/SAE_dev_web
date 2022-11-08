<?php

require_once 'vendor/autoload.php';

use \iutnc\netvod\dispatch\Dispatcher as Dispatcher;

session_start();

if (isset($_GET['action'])) {
    $action = $_GET['action'];
} else {
    $action = "";
}

$dispatcher = new Dispatcher($action);
$dispatcher->run();
