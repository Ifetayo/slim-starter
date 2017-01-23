<?php 
/**
 *
 * Add dependency injection declarations here
 * All you need to do is create a new array entry.
 *
 */

use Interop\Container\ContainerInterface;
/**
    TODO:
    - load app config from config directory instead of having to
      write it out as settings.displ...
*/ 
return[
	'settings.displayErrorDetails' => $config->get('app.settings')['displayErrorDetails'],
	/* twig */	
	\Slim\Views\Twig::class => function (ContainerInterface $container) use ($config){
		$view = new \Slim\Views\Twig($config->get('twig.views.path'), [
					$config->get('twig.parseOptions'),
				]);

			$view->addExtension(new \Slim\Views\TwigExtension(
				$container->get('router'),
				$container->get('request')->getUri()
			));
			return $view;
	},
	/* User repository */	
	\SlimStarter\Repositories\UserRepositoryInterface::class => function(ContainerInterface $container)  use ($config){
		return new \SlimStarter\Repositories\Eloquent\UserRepository($config->get('database_connections.mysql'));	
	},

];