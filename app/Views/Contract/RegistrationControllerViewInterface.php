<?php 
namespace SlimStarter\Views\Contract;

/**
 * undocumented class
 *
 * @package default
 * @author 
 **/
interface RegistrationControllerViewInterface
{
	public function withMessage(array $message);
	public function viewSignUpPage();
	public function redirectToSignUpPage();
} // END interface RegistrationControllerViewInterface
