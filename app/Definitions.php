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
use Mailgun\Mailgun;

$twig_path = $config->get('twig.views.path');
$twig_options = $config->get('twig.parseOptions');
$twig_env = [ $twig_path, $twig_options];
$mail_gun_settings = $config->get('mail.mail_gun');
$app_url = $config->get('app_url');

return[
	'settings.displayErrorDetails' => $config->get('settings.displayErrorDetails'),
	/* twig */	
	\SlimStarter\Views\ViewsInterface::class => function (ContainerInterface $container) use ($twig_env, $app_url){		
		
		/*$view = new \Slim\Views\Twig($twig_env[0], $twig_env[1]);
		
		$view->addExtension(new \Slim\Views\TwigExtension(
			$container->get('router'),
			$container->get('request')->getUri()
		));
		$view->addExtension(new \Twig_Extension_Debug);
		$view->getEnvironment()->addGlobal('flash', $container->get(\SlimStarter\Flash\Contracts\FlashInterface::class));
		$view->getEnvironment()->addGlobal('app_url', $app_url);
		return $view;*/

		
		$view = new \SlimStarter\Views\Twig($twig_env[0], $twig_env[1]);
		
		$view->addExtension(new \Slim\Views\TwigExtension(
			$container->get('router'),
			$container->get('request')->getUri()
		));
		$view->addExtension(new \Twig_Extension_Debug);
		$view->getEnvironment()->addGlobal('flash', $container->get(\SlimStarter\Services\Flash\Contracts\FlashInterface::class));
		$view->getEnvironment()->addGlobal('app_url', $app_url);
		return $view;
	},
	/* User repository */
	\SlimStarter\Repositories\Contracts\UserRepositoryInterface::class => function(ContainerInterface $container){
		return new \SlimStarter\Repositories\UserRepository();	
	},
	/* User repository */
	\SlimStarter\Repositories\Contracts\ActivationRepositoryInterface::class => function(ContainerInterface $container){
		return new \SlimStarter\Repositories\ActivationRepository();	
	},
	/* Form Validator interface */
	\SlimStarter\Services\FormValidation\FormValidatorInterface::class => function(ContainerInterface $container){
		return new \SlimStarter\Services\FormValidation\FormValidator($container->get(\SlimStarter\Repositories\Contracts\UserRepositoryInterface::class));	
	},
	/* Flash Messages interface */
	\SlimStarter\Services\Flash\Contracts\FlashInterface::class => function(ContainerInterface $container){
		return new \SlimStarter\Services\Flash\SlimFlash();	
	},
	/* CSRF Middleware */
	\SlimStarter\Middleware\Contracts\CSRFInterface::class => function(ContainerInterface $container){
		$guard = new \SlimStarter\Middleware\CSRFGuard;		
		$guard->setFlashObject($container->get(\SlimStarter\Services\Flash\Contracts\FlashInterface::class));
		$guard->setRouterObject($container->get('router'));
		return $guard;	
	},
	/* Random string/number generator */
	\SlimStarter\Services\RandomGenerator\RandomGeneratorInterface::class => function(ContainerInterface $container){
		$factory = new \SlimStarter\Services\RandomGenerator\RandomGeneratorLib();		
		return $factory->getMediumStrengthGenerator();
	},
	/* Email Service */
	\SlimStarter\Services\Mail\Contracts\AuthMailServiceInterface::class => function(ContainerInterface $container) use ($mail_gun_settings){
		return new \SlimStarter\Services\Mail\Auth\AuthMailer($container->get(\SlimStarter\Views\ViewsInterface::class), $mail_gun_settings, new Mailgun($mail_gun_settings['API_KEY']));
	},
	/* Registration controller view interface binding */
	\SlimStarter\Views\Contract\RegistrationControllerViewInterface::class => function(ContainerInterface $container){
		return new SlimStarter\Views\Concrete\RegistrationControllerView($container->get(\SlimStarter\Views\ViewsInterface::class), $container->get('router'), $container->get('response'), $container->get(\SlimStarter\Services\Flash\Contracts\FlashInterface::class));
	},

];