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

$app->get('/ad/click/{ad_id}/{referer}', function (Request $request, Response $response, $args) {
    $ads = new Ads($this->db, $this->logger, $this->get('settings'));

    $status = "success";
    $msg = "Click not logged.";
    if($ads->logClick($args['ad_id'], $args['referer'])){
        $msg = "Click logged.";
    }
    $newResponse = $response->withJson(['status'=>$status, 'message'=>$msg]);

    return $newResponse;
});

$app->get('/ad/impression/{ad_id}/{referer}', function (Request $request, Response $response, $args) {
    $ads = new Ads($this->db, $this->logger, $this->get('settings'));
    $adsArr = $ads->logImpression($args['ad_id'], $args['referer']);
    $newResponse = $response->withJson(['status'=>'success']);

    return $newResponse;
});