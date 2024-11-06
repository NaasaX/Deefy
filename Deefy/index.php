<?php


// Auto loader
require_once 'vendor/autoload.php';

// Set configuration
src\classes\repository\DeefyRepository::setConfig("config.db.ini");

// Exécutez le dispatcher
$dispatcher = new src\classes\dispatch\Dispatcher();
$dispatcher->run();
