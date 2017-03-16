<?php 
namespace SlimStarter\Views;
/**
 * undocumented class
 *
 * @package default
 * @author 
 **/
interface ViewsInterface
{
	public function renderView($response, $template, $data = []);
	public function fetch($template, $data = []);
} // END interface ViewsInterface