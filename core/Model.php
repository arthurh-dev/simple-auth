<?php
namespace Core;

use PDO;

abstract class Model {
    protected static function getDB() {
        $config = require '../config/config.php';
        $dsn = "mysql:host={$config['db_host']};dbname={$config['db_name']};charset=utf8";
        return new PDO($dsn, $config['db_user'], $config['db_pass']);
    }
}
