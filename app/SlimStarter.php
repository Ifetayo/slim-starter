<?php 
namespace SlimStarter;

use DI\ContainerBuilder;
use Noodlehaus\Config;
use DI\Bridge\Slim\App;
/**
* 
*/
class SlimStarter extends App
{

	protected function configureContainer(ContainerBuilder $builder)
    {
        //load all config file in the config directory
        $config = new Config(__DIR__.'/../config');    
        //require in all definitions from the definitions file               
    	$definitions = require __DIR__.'/Definitions.php';

        $builder->addDefinitions($definitions);
    }
}