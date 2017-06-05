<?php
use Slim\Middleware\JwtAuthentication;

require '../vendor/autoload.php';

require 'config.php';

require 'Token.php';

$app = new \Slim\App(["settings" => $config]);

$container = $app->getContainer();

// Custom error handler
$container['errorHandler'] = function ($container) {
    return function ($request, $response, $exception) use ($container) {
        return $container['response']->withStatus(500)
                                     ->withHeader('Content-Type', 'text/html')
                                      ->write($exception->getMessage());
    };
};

$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('api_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};

// Use Slim-PDO for the database interaction
// https://github.com/FaaPz/Slim-PDO
$container['db'] = function ($container) {
    $db_settings = $container['settings']['db'];

    $dsn = "mysql:host=" .  $db_settings['host'] . ";dbname=" .  $db_settings['dbname'] . ";charset=" .  $db_settings['charset'];
    
    $pdo = new \Slim\PDO\Database($dsn, $db_settings['user'], $db_settings['pass']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    return $pdo;
};

$container["token"] = function ($container) {
    return new Token;
};

// Setup the JWT Middleware
// https://www.appelsiini.net/projects/slim-jwt-auth
$container["JwtAuthentication"] = function ($container) {
    return new JwtAuthentication([
        "path" => "/",
        "passthrough" => ["/token", "/ads"],
        "secret" => $container['settings']['jwt']['secret'],
        "logger" => $container["logger"],
        "relaxed" => ["192.168.1.6"],
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

require(__DIR__ . '/routes/routes.php');

$app->run();