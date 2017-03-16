<?php 
namespace SlimStarter\Controllers\Auth;

use SlimStarter\Models\User;

use Interop\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use SlimStarter\Controllers\Auth\Handlers\EmailVerificationHandler;
use SlimStarter\Controllers\Auth\Handlers\RegistrationHandler;
use SlimStarter\Services\FormValidation\FormValidatorInterface;
use SlimStarter\Views\Contract\RegistrationControllerViewInterface as RegViewInterface;

class EmailVerificationController
{
	protected $reg_view;

	function __construct(RegViewInterface $reg_view) {
		$this->reg_view = $reg_view;
	}

	public function emailVerify(Request $request, FormValidatorInterface $form_validator, EmailVerificationHandler $verify_handler)
	{
		$params = $request->getParams();
		//validate input and check if validation has errors
		if ($form_validator->validateEmailVerify($params)->hasErrors()) {
			$message = array('type' => 'info', 'message' => "Something went wrong :(. We could not get your details. Still having issues, contact admin");
			return $this->reg_view->withMessage($message)->redirectHome();
		}

		return $verify_handler->verifyUserEmail($params, $this);
	}

	public function resendToken(Request $request, FormValidatorInterface $form_validator, EmailVerificationHandler $verify_handler)
	{
		$params = $request->getParams();
		//validate input and check if validation has errors
		if ($form_validator->validateResendTokenForm($params)->hasErrors()) {
			$message = array('info' => "Something went wrong :(. We could not get your details. Still having issues, contact admin");
			return $this->reg_view->withMessage($message)->redirectToSignUpPage();
		}
		return $verify_handler->resendVerificationEmail($params, $this);
	}

	public function s()
	{
		$message = array('reg-success' => "test s",
							'token' => "sd",
							'email' => User::find(188)->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	public function verificationEmailHasBeenSent(User $user, $token)
	{
		$message = array('reg-success' => "We just re-sent you a verfication email. Click the verify link to get verified",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	public function noUserFound()
	{
		$message = array('info' => "Sorry we cannot seem to find the account associated with that email");
		return $this->reg_view->withMessage($message)->redirectHome();
	}

	public function tooManyEmailSent()
	{
		$message = array('info' => "We have sent too many emails to your email address. Please check your inbox or spam folder.");
		return $this->reg_view->withMessage($message)->redirectHome();
	}

	public function noActivationFound()
	{
		return $this->reg_view->redirectHome();
	}

	public function couldNotUpdateActivationRecord()
	{
		return $this->reg_view->redirectHome();
	}

	public function couldNotReSendVerificationEmail(User $user, $token)
	{
		$message = array('reg-success' => "Hi there, we tried re-sending your verification email but something went wrong :(. 
											We will try again later. If the matter still persists please contact admin",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	public function couldNotSendVerificationEmail(User $user, $token)
	{
		$message = array('reg-success' => "Hi there your token has expired, we tried sending you another one but something went wrong :(. 
											We will try again later. If the matter still persists please contact admin",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	public function newTokenSent(User $user, $token)
	{
		$message = array('reg-success' => "Hi there, your token has expired, but dont worry we sent you a fresh one. 
											Click on the link in our email to get verified",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	public function alreadyVerified()
	{
		return $this->reg_view->redirectHome();
	}

	public function couldNotUpdateUser()
	{
		$message = array('info' => "Could not update your user record. Please try again later");
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	public function userVerified()
	{
		$message = array('info' => "Your email has been verified");
		return $this->reg_view->withMessage($message)->redirectHome();
	}
}