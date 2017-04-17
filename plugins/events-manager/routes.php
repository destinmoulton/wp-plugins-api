<?php
require_once(__DIR__."/classes/Events.php");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/events/{date}', function (Request $request, Response $response, $args) {
    $name = $request->getAttribute('name');
    $events = new Events($this->db, $this->logger);
    $evs = $events->getForDate($args['date']);
    $newResponse = $response->withJson(['events'=>$evs]);

    return $newResponse;
});

$app->get('/event_debug/{event_id}', function (Request $request, Response $response, $args) {
    $events = new Events($this->db, $this->logger);
    $ev = $events->getSingleByID($args['event_id']);
    $newResponse = $response->getBody()->write($ev['post_content']);

    return $newResponse;
});