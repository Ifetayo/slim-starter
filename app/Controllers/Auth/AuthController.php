<?php 
namespace SlimStarter\Controllers\Auth;

use Slim\Router;
use Slim\Views\Twig as View;
use SlimStarter\Controllers\Controller;
use SlimStarter\Flash\Contracts\FlashInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimStarter\FormValidation\FormValidatorInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;


class AuthController extends Controller
{
	public function getSignUp(Response $response)
	{	
		return $this->view->render($response, 'auth\signup.twig');
	}

	public function postSignUp(Request $request, Response $response, UserRepositoryInterface $user_repo, FormValidatorInterface $form_validator, Router $router)
	{
		$params = $request->getParams();
		$validation = $form_validator->validateSignUpForm($params, $user_repo);
		//check if validation has errors
		if ($validation->hasErrors()) {
			return $response->withRedirect($router->pathFor('auth.signup'));
		}

		$user = $this->storeUserDetails($params, $user_repo);
		
		if (!$user) {
			//could not save user details
			$this->flash->addMessage('info', "Something went wrong :(. Do not panic, we are working to get it fixed. Why don't you try again later");
			return $response->withRedirect($router->pathFor('auth.signup'));
		}

		$this->flash->addMessage('success', "Your registration has been successfully. An email has been sent to the provided email. You would need to verify your email before you can login");
		return $response->withRedirect($router->pathFor('auth.signin'));
	}

	private function storeUserDetails(array $params, UserRepositoryInterface $user_repo)
	{
		return $user_repo->registerUser($params);		
	}

	public function getSignIn(Response $response)
	{
		return $this->view->render($response, 'auth\signin.twig');
	}
}