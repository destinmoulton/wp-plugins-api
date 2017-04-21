<?php


// Allow localhost access to the api
$cors_whitelist = array('127.0.0.1','localhost');

if(in_array($_SERVER['REMOTE_ADDR'], $cors_whitelist)){
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token');
}

error_reporting(E_ALL);
ini_set("display_errors", "on");

require('../app/init.php');