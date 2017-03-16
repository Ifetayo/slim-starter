<?php 
namespace SlimStarter\Views\Concrete;

use Slim\Router;
use SlimStarter\Views\ViewsInterface;
use Psr\Http\Message\ResponseInterface as Response;
use SlimStarter\Services\Flash\Contracts\FlashInterface;
use SlimStarter\Views\Contract\RegistrationControllerViewInterface;
/**
* 
*/
class RegistrationControllerView implements RegistrationControllerViewInterface
{
	protected $view;
	protected $router;
	protected $response;
	protected $flash;

	function __construct(ViewsInterface $view, Router $router, Response $response, FlashInterface $flash)
	{
		$this->view = $view;
		$this->router = $router;
		$this->response = $response;
		$this->flash = $flash;
	}

	public function withMessage(array $message)
	{
		foreach ($message as $key => $value) {
			$this->flash->addMessage($key, $value);
		}
		return $this;	
	}

	public function viewSignUpPage()
	{
		$this->view->render($this->response, 'auth\signup.twig');
	}

	public function redirectToSignUpPage()
	{
		return $this->response->withRedirect($this->router->pathFor('auth.signup'));
	}

	public function redirectHome()
	{
		return $this->response->withRedirect($this->router->pathFor('home'));
	}
}