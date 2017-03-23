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
	
	/**
	 *
	 * Get route for verifying user's email.
	 * Check if the user details are valid
	 * then proceed with verification process
	 * @param Request $request GET request object
	 * @param FormValidatorInterface $form_validator request params validator object
	 * @param EmailVerificationHandler $verify_handler if request params are valid, this handles verification
	 */	
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

	/**
	 *
	 * Resend the user the token from registration
	 * @param Request $request GET request object
	 * @param FormValidatorInterface $form_validator request params validator object
	 * @param EmailVerificationHandler $verify_handler if request params are valid, this handles verification
	 */
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

	/**
	 *
	 * Method called when verification email has been re-sent
	 * @param User $user affected user
	 * @param string $token token value sent
	 */	
	public function verificationEmailHasBeenSent(User $user, $token)
	{
		$message = array('reg-success' => "We just re-sent you a verfication email. Click the verify link to get verified",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	/**
	 *
	 * Method called if handler cannot find the user record
	 *
	 */	
	public function noUserFound()
	{
		$message = array('info' => "Sorry we cannot seem to find the account associated with that email");
		return $this->reg_view->withMessage($message)->redirectHome();
	}

	/**
	 *
	 * When user has been sent too many emails
	 *
	 */
	public function tooManyEmailSent()
	{
		$message = array('info' => "We have sent too many emails to your email address. Please check your inbox or spam folder.");
		return $this->reg_view->withMessage($message)->redirectHome();
	}

	/**
	 *
	 * This called when no activation record is found for the user
	 *
	 */	
	public function noActivationFound()
	{
		return $this->reg_view->redirectHome();
	}

	/**
	 *
	 * Called when the activation record could not be updated
	 *
	 */	
	public function couldNotUpdateActivationRecord()
	{
		return $this->reg_view->redirectHome();
	}

	/**
	 *
	 * Called when verification email could not be sent
	 * @param User $user affected user
	 * @param string $token, token to be sent
	 */	
	public function couldNotReSendVerificationEmail(User $user, $token)
	{
		$message = array('reg-success' => "Hi there, we tried re-sending your verification email but something went wrong :(. 
											We will try again later. If the matter still persists please contact admin",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}	

	/**
	 *
	 * Called when the user's token has expired,
	 * a new one is generated and sent to the user.
	 * @param User $user affected user
	 * @param string $token, token to be sent
	 */	
	public function newTokenSent(User $user, $token)
	{
		$message = array('reg-success' => "Hi there, your token has expired, but dont worry we sent you a fresh one. 
											Click on the link in our email to get verified",
							'token' => $token,
							'email' => $user->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	/**
	 *
	 * Called when the user is already verified
	 *
	 */	
	public function alreadyVerified()
	{
		return $this->reg_view->redirectHome();
	}

	/**
	 *
	 * When the user record could not be updated
	 *
	 */	
	public function couldNotUpdateUser()
	{
		$message = array('info' => "Could not update your user record. Please try again later");
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}

	/**
	 *
	 * When all is good, the user has been verified
	 *
	 */	
	public function userVerified()
	{
		$message = array('info' => "Your email has been verified");
		return $this->reg_view->withMessage($message)->redirectHome();
	}

	/**
	 *
	 * TEST DELETE
	 *
	 */
	public function s()
	{
		//$r = \Carbon\Carbon::now();
		//$activation->setUpdatedAt(date('Y-m-d H:i:s', time()));
		$r = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', time()));
		dd($r);

		$message = array('reg-success' => "test s",
							'token' => "sd",
							'email' => User::find(188)->email,
						);
		return $this->reg_view->withMessage($message)->redirectToSignUpPage();
	}
}