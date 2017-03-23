<?php 
namespace SlimStarter\Controllers\Auth;

use SlimStarter\Models\User;
use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimStarter\Controllers\Auth\Handlers\ActivationHandler;
use SlimStarter\Controllers\Auth\Handlers\RegistrationHandler;
use SlimStarter\Services\FormValidation\FormValidatorInterface;
use SlimStarter\Views\Contract\RegistrationControllerViewInterface as RegViewInterface;

class RegistrationController
{
	protected $reg_view;

	function __construct(RegViewInterface $reg_view) {
		$this->reg_view = $reg_view;
	}

	/**
	 * Get the sign up page
	 */	
	public function getSignUp()
	{
		return $this->reg_view->viewSignUpPage();
	}

	/**
	 *
	 * Post sign up page form
	 * if validation fails, redirect back to home page with
	 * the list of errors in the session
	 * else go ahead and process the data
	 * @param Request $request containing the post request data
	 * @param FormValidatorInterface $form_validator validator class
	 * @param RegistrationHandler $reg_handler handles registration of the user
	 */	
	public function postSignUp(Request $request, FormValidatorInterface $form_validator, RegistrationHandler $reg_handler)
	{
		$params = $request->getParams();
		//validate input and check if validation has errors
		if ($form_validator->validateSignUpForm($params)->hasErrors()) {
			return $this->reg_view->redirectToSignUpPage();
		}
		return $reg_handler->registerUser($params, $this);
	}	

	/**
	 * Redirect user to home page
	 */	
	public function redirectHome()
	{
		return $this->reg_view->redirectHome();
	}

	/**
	 * If the user record cannot be
	 * created, redirect here
	 */	
	public function couldNotCreateUserRecord()
	{
		$message = array('info' => "Something went wrong :(. We could not save your details. Why don't you try again later");
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	/**
	 * If the activation record
	 * cannot be create, redirect here
	 */	
	public function couldNotCreateActivationRecord()
	{
		$message = array('info' => "Something went wrong :(. We could not save your details. Why don't you try again later");
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	/**
	 *
	 * If verification email could not be sent to the user
	 * @param User $user the user record
	 * @param string $token the token generated
	 */	
	public function couldNotSendVerificationEmail()
	{
		$message = array('info' => "Something went wrong :(. We could not send you a verfication email. Why don't you try registering again later");
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	/**
	 *
	 * Registration process is complete
	 *
	 */	
	public function registrationComplete(User $user, $token)
	{
		$message = array('reg-success' => "All done. We just send you a verfication email. Click the verify link to get verified",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}
}