<?php 
namespace SlimStarter\Views;

use \Slim\Views\Twig as View;
/**
* 
*/
class Twig extends View implements ViewsInterface
{
	public function renderView($response, $template, $data = [])
	{
	     parent::render($response, $template, $data = []);
	}
}