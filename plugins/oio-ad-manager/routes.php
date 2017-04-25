<?php

require_once(__DIR__."/classes/Ads.php");

use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

$app->get('/ads/{ad_type}', function (Request $request, Response $response, $args) {
    $ads = new Ads($this->db, $this->logger, $this->get('settings'));
    $adsArr = $ads->getAdsByType($args['ad_type']);
    $newResponse = $response->withJson(['ads'=>$adsArr]);

    return $newResponse;
});