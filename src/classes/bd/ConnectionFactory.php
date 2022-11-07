<?php

namespace iutnc\netvod\bd;

use PDO;

class ConnectionFactory
{
    public static $config = [];
    public static $db = null;

    public static function setConfig($file) {
        self::$config = parse_ini_file($file);
    }

    public static function makeConnection() : PDO
    {

        $dsn = self::$config['driver'].":host=".self::$config['host'].";dbname=".self::$config['database'];

        self::$db = new PDO($dsn, self::$config['username'], self::$config['password'], [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_STRINGIFY_FETCHES => false,
        ]);

        self::$db->prepare("SET NAMES 'UTF8'")->execute();

        return self::$db;
    }
}