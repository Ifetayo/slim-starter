<?php 
namespace SlimStarter\Controllers;

use Slim\Views\Twig as View;
use SlimStarter\Flash\Contracts\FlashInterface;
use Psr\Http\Message\ResponseInterface as Response;
/**
* 
*/
abstract class Controller
{
	protected $view;
	protected $flash;
	function __construct(View $view, FlashInterface $flash)
	{
		$this->view = $view;
		$this->flash = $flash;
	}
}