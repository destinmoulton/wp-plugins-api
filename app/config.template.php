<?php
// Slim Framework configuration
// init.php contains the Slim config initialization
$config['displayErrorDetails'] = true;
$config['addContentLengthHeader'] = false;

// Wordpress Database connection credentials
$config['db']['host']   = "localhost";
$config['db']['user']   = "username";
$config['db']['pass']   = "password";
$config['db']['dbname'] = "wordpress_database";
$config['db']['charset'] = "utf8";
$config['db']['prefix'] = "";

// JWT Authentication
$config['jwt']['auth_id'] = "clientSendsThisAsVerification";
$config['jwt']['secret'] = "setThisToSomethingSuperSecret";