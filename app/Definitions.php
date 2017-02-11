<?php 
/**
 *
 * Add dependency injection declarations here
 * All you need to do is create a new array entry.
 *
 */
use DI\Container;
use DI\Bridge\Slim\CallableResolver;
use DI\Bridge\Slim\ControllerInvoker;
use Interop\Container\ContainerInterface;

$twig_path = $config->get('twig.views.path');
$twig_options = $config->get('twig.parseOptions');
$twig_env = [ $twig_path, $twig_options];

$capsule = new \Illuminate\Database\Capsule\Manager;
$capsule->addConnection($config->get('database_connections.mysql'));

$capsule->setAsGlobal();
$capsule->bootEloquent();

return[
	'settings.displayErrorDetails' => $config->get('app.settings')['displayErrorDetails'],
	/* twig */	
	\Slim\Views\Twig::class => function (ContainerInterface $container) use ($twig_env){		
		$view = new \Slim\Views\Twig($twig_env[0], $twig_env[1]);
		
		$view->addExtension(new \Slim\Views\TwigExtension(
			$container->get('router'),
			$container->get('request')->getUri()
		));
		$view->addExtension(new \Twig_Extension_Debug);
		$view->getEnvironment()->addGlobal('flash', $container->get(\SlimStarter\Flash\Contracts\FlashInterface::class));
		return $view;
	},
	/* User repository */
	\SlimStarter\Repositories\Contracts\UserRepositoryInterface::class => function(ContainerInterface $container){
		return new \SlimStarter\Repositories\UserRepository();	
	},
	/* Form Validator interface */
	\SlimStarter\FormValidation\FormValidatorInterface::class => function(ContainerInterface $container){
		return new \SlimStarter\FormValidation\FormValidator();	
	},
	/* Flash Messages interface */
	\SlimStarter\Flash\Contracts\FlashInterface::class => function(ContainerInterface $container){
		return new \SlimStarter\Flash\SlimFlash();	
	},
	/* CSRF Middleware */
	\Slim\Csrf\Guard::class => function(ContainerInterface $container){
		return new \Slim\Csrf\Guard;	
	},

];