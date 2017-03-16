<?php 
namespace SlimStarter\Controllers\Auth;


use Slim\Views\Twig as View;
use SlimStarter\Controllers\Controller;
use SlimStarter\Flash\Contracts\FlashInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimStarter\FormValidation\FormValidatorInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;



class AuthController extends Controller
{
	public function getSignUp()
	{	
		return $this->view->render($this->response, 'auth\signup.twig');
	}

	public function postSignUp(FormValidatorInterface $form_validator, RegistrationHandler $reg_handler)
	{
		$params = $this->request->getParams();
		$validation = $form_validator->validateSignUpForm($params);
		//check if validation has errors
		if ($validation->hasErrors()) {
			return $this->response->withRedirect($this->router->pathFor('auth.signup'));
		}
		return $reg_handler->registerUser($params, $this);
	}

	public function getSignIn()
	{
		return $this->view->render($this->response, 'auth\signin.twig');
	}

	public function emailVerify()
	{
		var_dump('emailver');
		die();
	}

	/**
	 *
	 * could not save user details
	 *
	 */	
	public function couldNotSaveDetails()
	{
		$this->flash->addMessage('info', "Something went wrong :(. We could not save your details. Why don't you try again later");
		return $this->response->withRedirect($this->router->pathFor('auth.signup'));
	}

	public function couldNotSendVerificationEmail($user)
	{
		$this->flash->addMessage('info', "Something went wrong :(. We could not send you an email to verify your account, we are working to get it fixed.");
		return $this->response->withRedirect($this->router->pathFor('auth.signup'));
	}

	public function registrationComplete($user)
	{
		$this->flash->addMessage('success', "Your registration has been successfully. An email has been sent to the provided email. You would need to verify your email before you can login");
		return $this->response->withRedirect($this->router->pathFor('auth.signin'));
	}	
}