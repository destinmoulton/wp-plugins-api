<?php
require '../vendor/autoload.php';


require 'config.php';
$app = new \Slim\App(["settings" => $config]);

// Use NotORM for the database 
require 'lib/NotORM.php';

require 'Token.php';

use Slim\Middleware\JwtAuthentication;

$container = $app->getContainer();

$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('api_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

$container['db'] = function ($container) {
    $db = $container['settings']['db'];
    $pdo = new PDO("mysql:host=" . $db['host'] . ";dbname=" . $db['dbname'],
        $db['user'], $db['pass'],array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . $db['charset'] . "'"));
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $db = new NotORM($pdo);
    return $db;
};

$container["token"] = function ($container) {
    return new Token;
};

// Setup the JWT Middleware
// https://www.appelsiini.net/projects/slim-jwt-auth
$container["JwtAuthentication"] = function ($container) {
    return new JwtAuthentication([
        "path" => "/",
        "passthrough" => ["/token"],
        "secret" => $container['settings']['jwt']['secret'],
        "logger" => $container["logger"],
        "relaxed" => ["localhost"],
        "error" => function ($request, $response, $arguments) {
            $data["status"] = "error";
            $data["message"] = $arguments["message"];
            return $response
                ->withHeader("Content-Type", "application/json")
                ->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
        },
        "callback" => function ($request, $response, $arguments) use ($container) {

            $container["token"]->hydrate($arguments["decoded"]);
        }
    ]);
};
$app->add("JwtAuthentication");

// Allow localhost access to the api
$cors_whitelist = array('127.0.0.1','localhost');

if(in_array($_SERVER['REMOTE_ADDR'], $cors_whitelist)){
    $app->add(function ($request, $response, $next) {
        $response->withHeader('Access-Control-Allow-Origin', '*');
        $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, PATCH, PUT, DELETE, OPTIONS');
        $response->withHeader('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Auth-Token');
        return $next($request, $response);
    });
}

require(__DIR__ . '/routes/routes.php');

$app->run();