<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/events/{date}', function (Request $request, Response $response, $args) {
    $name = $request->getAttribute('name');
    $events = new Events($this->db, $this->logger);
    $evs = $events->getForDate($args['date']);
    $newResponse = $response->withJson(['events'=>$evs]);

    return $newResponse;
});