<?php

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/events/{start_date}/{end_date}', function (Request $request, Response $response, $args) {
    $name = $request->getAttribute('name');
    $events = new Events($this->db, $this->logger);
    $evs = $events->getDateRange($args['start_date'], $args['end_date']);
    $newResponse = $response->withJson(['events'=>$evs]);

    return $newResponse;
});