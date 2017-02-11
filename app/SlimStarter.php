<?php 
namespace SlimStarter;

use Dotenv\Dotenv;
use Slim\Csrf\Guard;
use \Slim\Views\Twig;
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
	protected $dot_env;

	/**
	 *
	 * Block comment
	 *
	 */	
	public function load($app)
    {
    	$this->dot_env = new Dotenv(__DIR__.'/../');

		$this->dot_env->load();

		$app_container = $app->getContainer();

		$config = new Config(__DIR__.'/../config');
		
        $this->capsule = new \Illuminate\Database\Capsule\Manager;
		$this->capsule->addConnection($config->get('database_connections.mysql'));
		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();

		require_once __DIR__.'/../app/routes/web.php';
		require_once __DIR__.'/../app/routes/api.php';

		$app->add(new \SlimStarter\Middleware\CSRFMiddleware($app_container->get(Guard::class), $app_container->get(Twig::class)));
		$app->add(new \SlimStarter\Middleware\ValidationErrorsMiddleware($app_container->get(Twig::class)));
		$app->add(new \SlimStarter\Middleware\OldFormDataMiddleware($app_container->get(Twig::class)));
		
		$app->add($app_container->get(Guard::class));

		v::with('SlimStarter\\FormValidation\\Rules\\');
    }

	protected function configureContainer(ContainerBuilder $builder)
    {
        //load all config file in the config directory
        $config = new Config(__DIR__.'/../config');

        $this->capsule = new \Illuminate\Database\Capsule\Manager;
		$this->capsule->addConnection($config->get('database_connections.mysql'));
		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();
        
        //require in all definitions from the definitions file               
    	$definitions = require __DIR__.'/Definitions.php';

        $builder->addDefinitions($definitions);
    }
}