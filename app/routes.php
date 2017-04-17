<?php
$PLUGIN_DIR = realpath(__DIR__ . '../plugins/');

$plugin_directories = glob($PLUGIN_DIR . '/*' , GLOB_ONLYDIR);

foreach( $plugin_directories as $dir){
    require($PLUGIN_DIR . $dir . '/routes.php');
}