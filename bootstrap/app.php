<?php 
use Dotenv\Dotenv;

session_start();

require __DIR__.'/../vendor/autoload.php';

$dot_env = new Dotenv(__DIR__.'/../');
$dot_env->load();

$app = new \SlimStarter\SlimStarter;

require_once __DIR__.'/../app/routes/web.php';
require_once __DIR__.'/../app/routes/api.php';