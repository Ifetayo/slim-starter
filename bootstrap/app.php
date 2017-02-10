<?php 
use Dotenv\Dotenv;
use \Slim\Views\Twig;
use Respect\Validation\Validator as v;
use Illuminate\Database\Capsule\Manager as Capsule;



//require __DIR__.'/../vendor/autoload.php';

$dot_env = new Dotenv(__DIR__.'/../');
$dot_env->load();

$app = new \SlimStarter\SlimStarter();

$container = $app->getContainer();

require_once __DIR__.'/../app/routes/web.php';
require_once __DIR__.'/../app/routes/api.php';

$app->add(new \SlimStarter\Middleware\ValidationErrorsMiddleware($container->get(Twig::class)));
$app->add(new \SlimStarter\Middleware\OldFormDataMiddleware($container->get(Twig::class)));

v::with('SlimStarter\\FormValidation\\Rules\\');
