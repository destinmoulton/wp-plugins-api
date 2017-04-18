<?php
/**
 * Slim routes are stored in a 'routes.php' file inside each plugin
 * in the ../plugins/ directory.
 *
 * ie. ../plugins/events-manager/routes.php
 *
 * @author Destin Moulton
 *
 */
$PLUGIN_DIR = realpath(__DIR__ . '/../plugins/');

$plugin_directories = glob($PLUGIN_DIR . '/*' , GLOB_ONLYDIR);

foreach( $plugin_directories as $plugin_dir){
    require($plugin_dir . '/routes.php');
}