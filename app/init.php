<?php
require '../vendor/autoload.php';


require 'config.php';
$app = new \Slim\App(["settings" => $config]);

// Use NotORM for the database 
require 'lib/NotORM.php';

$container = $app->getContainer();

$container['logger'] = function($ctxt) {
    $logger = new \Monolog\Logger('api_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($ctxt) {
    $db = $ctxt['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . $db['charset'] . "'"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db = new NotORM($pdo);
    return $db;
};

require('routes.php');

$app->run();