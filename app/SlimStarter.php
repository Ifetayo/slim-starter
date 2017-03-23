<?php 
namespace SlimStarter;

use DI\Container;
use Slim\Csrf\Guard;
use Slim\Views\Twig;
use Noodlehaus\Config;
use DI\Bridge\Slim\App;
use DI\ContainerBuilder;
use Respect\Validation\Validator as v;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
* 
*/
class SlimStarter extends App
{
	protected $app;
	protected $capsule;
	/**
	 *
	 * Block comment
	 *
	 */	
	public function load($app)
    {
		$app_container = $app->getContainer();

		$config = new Config(__DIR__.'/../config');

		$this->loadDatabase($config->get('database_connections.mysql'), $app_container);

		require_once __DIR__.'/../app/routes/web.php';
		require_once __DIR__.'/../app/routes/api.php';

		$app->add(new \SlimStarter\Middleware\CSRFMiddleware($app_container->get(\SlimStarter\Middleware\Contracts\CSRFInterface::class), $app_container->get(\SlimStarter\Views\ViewsInterface::class)));
		$app->add(new \SlimStarter\Middleware\ValidationErrorsMiddleware($app_container->get(\SlimStarter\Views\ViewsInterface::class)));
		$app->add(new \SlimStarter\Middleware\OldFormDataMiddleware($app_container->get(\SlimStarter\Views\ViewsInterface::class)));
		
		$app->add($app_container->get(\SlimStarter\Middleware\Contracts\CSRFInterface::class));

		v::with('SlimStarter\\Services\\FormValidation\\Rules\\');
    }

	protected function configureContainer(ContainerBuilder $builder)
    {
		//load all config file in the config directory
    	$config = new Config(__DIR__.'/../config');

        //require in all definitions from the definitions file               
    	$definitions = require __DIR__.'/Definitions.php';

        $builder->addDefinitions($definitions);
    }

    private function loadDatabase(array $settings, Container $app_container)
    {
    	$db = $app_container->get(\SlimStarter\Services\Database\Contract\DatabaseInterface::class);
		$db->bootDB($settings);
    }
}