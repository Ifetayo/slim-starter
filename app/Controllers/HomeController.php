<?php 
namespace SlimStarter\Controllers;

use SlimStarter\Views\ViewsInterface as View;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;


/**
* 
*/
class HomeController
{
	public function index(Response $response, View $view)
	{
		return $view->renderView($response, 'home.twig');
	}
}