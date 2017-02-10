<?php 
require __DIR__.'/../vendor/autoload.php';
session_start();
date_default_timezone_set('UTC');

$app = new SlimStarter\SlimStarter();

$app->load($app);

$app->run();