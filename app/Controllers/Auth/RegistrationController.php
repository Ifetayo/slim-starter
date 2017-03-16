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

	public function getSignUp()
	{
		return $this->reg_view->viewSignUpPage();
	}

	public function postSignUp(Request $request, FormValidatorInterface $form_validator, RegistrationHandler $reg_handler)
	{
		$params = $request->getParams();
		//validate input and check if validation has errors
		if ($form_validator->validateSignUpForm($params)->hasErrors()) {
			return $this->reg_view->redirectToSignUpPage();
		}
		return $reg_handler->registerUser($params, $this);
	}

	public function emailVerify(Request $request, FormValidatorInterface $form_validator, ActivationHandler $activation_handler)
	{
		$params = $request->getParams();
		//validate input and check if validation has errors
		if ($form_validator->validateEmailVerify($params)->hasErrors()) {
			$message = array('type' => 'info', 'message' => "Something went wrong :(. We could not get your details. Still having issues, contact admin");
			return $this->reg_view->withMessage($message)->redirectHome();
		}
		return $activation_handler->verifyUserEmail();
	}

	public function resendToken(Request $request, RegistrationHandler $reg_handler)
	{
		$params = $request->getParams();
		return $reg_handler->resendVerificationEmail($params, $this);
	}

	public function redirectHome()
	{
		$this->reg_view->redirectHome();
	}

	public function couldNotCreateUserRecord()
	{
		$message = array('type' => 'info', 'message' => "Something went wrong :(. We could not save your details. Why don't you try again later");
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	public function couldNotCreateActivationRecord()
	{
		$message = array('type' => 'info', 'message' => "Something went wrong :(. We could not save your details. Why don't you try again later");
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	public function couldNotSendVerificationEmail(User $user, $token)
	{
		$message = array('reg-success' => "Something went wrong :(. We could not send you a verfication email. Why don't you try again later",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	public function registrationComplete(User $user, $token)
	{
		$message = array('reg-success' => "All done. We just send you a verfication email. Click the verify link to get verified",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}
}