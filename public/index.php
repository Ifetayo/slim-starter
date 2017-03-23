<?php 
require __DIR__.'/../vendor/autoload.php';

use Dotenv\Dotenv;

session_start();
date_default_timezone_set('Africa/Lagos');

$dot_env = new Dotenv(__DIR__.'/../');

$dot_env->load();

$app = new SlimStarter\SlimStarter();

$app->load($app);

$app->run();