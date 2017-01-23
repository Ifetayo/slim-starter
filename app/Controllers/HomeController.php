<?php 
namespace SlimStarter\Controllers;

use Slim\Views\Twig as View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimStarter\Repositories\UserRepositoryInterface;

/**
* 
*/
class HomeController
{
	public function index(Request $request, Response $response, View $view, UserRepositoryInterface $r)
	{
		//$user_repository->getAll();
		//$r->getAll();
		return $view->render($response, 'auth/signin.twig');
	}
}