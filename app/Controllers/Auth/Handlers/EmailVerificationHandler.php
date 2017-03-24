<?php 
namespace SlimStarter\Controllers\Auth\Handlers;

use Carbon\Carbon;
use SlimStarter\Models\User;
use SlimStarter\Models\Activation;
use SlimStarter\Services\CustomExceptions\User404Exception;
use SlimStarter\Controllers\Auth\EmailVerificationController;
use SlimStarter\Services\CustomExceptions\UserRecordNotSaved;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Services\CustomExceptions\UpdatingUserException;
use SlimStarter\Services\CustomExceptions\TokenNotValidException;
use SlimStarter\Services\CustomExceptions\TokenMismatchException;
use SlimStarter\Controllers\Auth\Contracts\RegistrationInterface;
use SlimStarter\Services\CustomExceptions\Activation404Exception;
use SlimStarter\Services\CustomExceptions\TooManyEmailsException;
use SlimStarter\Services\CustomExceptions\ActivationRecordNotSaved;
use SlimStarter\Services\Database\Contract\DatabaseInterface as DB;
use SlimStarter\Services\CustomExceptions\UpdatingActivationException;
use SlimStarter\Services\CustomExceptions\UserHasBeenVerifiedException;
use SlimStarter\Services\CustomExceptions\ErrorSendingVerificationEmail;
use SlimStarter\Services\CustomExceptions\ErrorUpdatingActivationRecordException;

/**
* EmailVerificationHandler
*/
class EmailVerificationHandler
{
	protected $user_repo;
	protected $activation_handler;
	protected $db;
	protected $user;
	protected $activation;
	protected $token;

	function __construct(UserRepositoryInterface $user_repo, ActivationHandler $activation_handler, DB $db) {
		$this->user_repo = $user_repo;
		$this->activation_handler = $activation_handler;
		$this->db = $db;
	}

	/**
	 *
	 * Resend the user their verification email
	 * @param Array $params, this contains the url encoded user's email and token for the verification
	 * @param EmailVerificationController $call_back call back object for the EmailVerificationController class
	 */	
	public function resendVerificationEmail(array $params, EmailVerificationController $call_back)
	{
		//get the user
		//check if the user is verified
		//get the user's activation record
		//check if the token,
		//check the email throttle
		//send verification email
		//increment the resent count
		try {
			$result = $this->getUser($params['email'])
								->hasNotBeenVerified($this->user)
									->getActivationRecord($this->user)
										->checkActivationTokenHasExpired($this->activation)
											->checkEmailThrottle($this->activation)
												->sendVerificationEmail($this->user, $params['token'])
													->setActivationResentCount($this->activation, ++$this->activation->resent_count);
		}catch (User404Exception $e) {
			return $call_back->noUserFound();
		}catch(UserHasBeenVerifiedException $e){
			return $call_back->alreadyVerified();
		}catch(Activation404Exception $e){
			return $call_back->noActivationFound();
		}catch(TokenNotValidException $e){
			return $this->refreshToken($this->user, $this->activation, $call_back);
		}catch(TooManyEmailsException $e){
			return $call_back->tooManyEmailSent();
		}catch (ErrorSendingVerificationEmail $e) {
			return $call_back->couldNotReSendVerificationEmail($this->user, $this->token);
		}catch(UpdatingActivationException $e){
			//log error here
			//do nothing here
			//well everything up to this point is ok for the user
			//might as well do an error log here, with high level
		}
		return $call_back->verificationEmailHasBeenSent($this->user, $params['token']);
	}

	/**
	 *
	 * Check if the too many emails have been sent to the
	 * user over the last one day
	 * @param Activation $activation, this is the user's activation record
	 * @throws TooManyEmailsException
	 * @return EmailVerificationHandler 
	 */	
	public function checkEmailThrottle(Activation $activation)
	{
		$result = $this->activation_handler->emailThrottle($activation);
		return !$result ? 
						call_user_func( function() use ($activation){ throw new TooManyEmailsException("We have sent too many activation emails to this account. Check your spam or contact admin", 1); }) 
						: call_user_func( function () { return $this; });
	}

	/**
	 *
	 * Set the resent count of the user's activation record
	 * @param Activation $activation, this is the user's activation record
	 * @param int $value, this is the value the resent count is going to be updated to
	 * @throws UpdatingActivationException
	 * @return EmailVerificationHandler 
	 */	
	public function setActivationResentCount(Activation $activation, $value)
	{
		$result = $this->activation_handler->setResentCount($activation, $value);
		return !$result ? 
						call_user_func( function() use ($activation){ throw new UpdatingActivationException("Resent count for activation record for user id - $activation->user_id could not be updated", 1); }) 
						: call_user_func( function () { return $this; });
	}

	/**
	 *
	 * Verify the user token and email
	 * First you find the user by email
	 * if a user exists get the activation record
	 * if the activation record exists, check that the token has not expired
	 * if it hasn't set the verified flag on the db to true
	 * @param Array $params, user's etails, contains email and token all url encoded
	 * @param EmailVerificationController $call_back, call back
	 */	
	public function verifyUserEmail(array $params, EmailVerificationController $call_back)
	{
		//check for user
		try {
			$result = $this->getUser(urldecode($params['email']))
								->hasNotBeenVerified($this->user)
									->getActivationRecord($this->user)
										->checkActivationTokenHasExpired($this->activation)
											->checkForTokenMismatch($this->activation, urldecode($params['token']))
												->setUserEmailVerifiedFlag($this->user, true);
		} catch (User404Exception $e) {
			return $call_back->noUserFound();
		}catch(UserHasBeenVerifiedException $e){
			return $call_back->alreadyVerified();
		}catch(Activation404Exception $e){
			return $call_back->noActivationFound();
		}catch(TokenNotValidException $e){
			return $this->refreshToken($this->user, $this->activation, $call_back);
		}catch(UpdatingUserException $e){
			return $call_back->couldNotUpdateUser();
		}catch(TokenMismatchException $e){
			return $this->refreshToken($this->user, $this->activation, $call_back);
		}
		return $call_back->userVerified();
	}

	/**
	 *
	 * Get the user record that has this email
	 * @param string @email, query email
	 * @throws User404Exception
	 * @return EmailVerificationHandler
	 */	
	private function getUser($email)
	{
		$user = $this->user_repo->findUserByEmail($email);
		return !$user ? 
						call_user_func( function() use($email){ throw new User404Exception("No User record by email - $email found", 1); }) 
						: call_user_func( function () use ($user) { 
																		$this->user = $user; 
																		return $this; 
																}
										);
	}

	/**
	 *
	 * Check if the user has been verified
	 * @param User $user, the user object
	 * @throws UserHasBeenVerifiedException
	 * @return EmailVerificationHandler
	 */	
	private function hasNotBeenVerified(User $user)
	{
		return $user->email_verified ? 
						call_user_func( function() use($user){ throw new UserHasBeenVerifiedException("User - $user->email is already verified", 1); }) 
						: call_user_func( function () use ($user) { 
																		$this->user = $user; 
																		return $this; 
																}
										);
	}

	/**
	 *
	 * Get the activation record that belongs to the given user
	 * @param User $user, user object
	 * @throws Activation404Exception
	 * @return EmailVerificationHandler
	 */	
	private function getActivationRecord(User $user)
	{
		$activation = $this->user_repo->getActivation($user);
		return is_null($activation) ? 
						call_user_func( function() use($user){ throw new Activation404Exception("No activation record for email - $user->email found", 1); }) 
						: call_user_func( function () use ($activation) { $this->activation = $activation; return $this; });
	}

	/**
	 *
	 * Check if the token has expired
	 * @param Activation $activation
	 * @throws TokenNotValidException
	 * @return EmailVerificationHandler
	 */	
	private function checkActivationTokenHasExpired($activation)
	{
		$result = $this->activation_handler->checkEmailTokenValidity($activation);
		return !$result ? 
						call_user_func( function() { throw new TokenNotValidException("Token has expired", 1); }) 
						: call_user_func( function() use ($result) { return $this; });
	}

	/**
	 *
	 * Check if the token is a mismatch with what is on record
	 * @param Activation $activation
	 * @throws TokenMismatchException
	 * @return EmailVerificationHandler
	 */	
	public function checkForTokenMismatch(Activation $activation, $token)
	{
		$result = $this->activation_handler->validateToken($activation, $token);
		return !$result ? 
						call_user_func( function() use ($activation) { throw new TokenMismatchException("Token mismatch for user id - $activation->user_id", 1); }) 
						: call_user_func( function() use ($result) { return $this; });

	}

	/**
	 *
	 * Set the user email verified field
	 * @param User $user, affected user object
	 * @param int $value, value to set the user'email verified field
	 * @throws UpdatingUserException
	 * @return EmailVerificationHandler
	 */	
	public function setUserEmailVerifiedFlag(User $user, $value)
	{
		$user->email_verified = $value;
		$result = $this->user_repo->save($user);
		return !$result ? 
						call_user_func( function() use ($user) { throw new UpdatingUserException("Could not set the email verified db column for user - $user->email", 1); }) 
						: call_user_func( function() use ($result) { return $this; });
	}

	/**
	 *
	 * When the user's token is a mismatch or the user token has expired
	 * this method sends the user a fresh token to their email
	 * @param User $user, affected user object
	 * @param Activation $activation, user's activation object
	 * @param EmailVerificationController $call_back, call back object
	 */	
	public function refreshToken(User $user, Activation $activation, EmailVerificationController $call_back)
	{
		try {
			$result = $this->setNewToken($activation)
							->sendVerificationEmail($user, $this->token);
		} catch (ErrorUpdatingActivationRecordException $e) {
			return $call_back->couldNotUpdateActivationRecord();
		}catch (ErrorSendingVerificationEmail $e) {
			return $call_back->couldNotReSendVerificationEmail($user, $this->token);
		}

		return $call_back->newTokenSent($user, $this->token);
	}

	private function setNewToken($activation)
	{
		$token = $this->activation_handler->refreshToken($activation);
		return !$token ? 
						call_user_func( function() use ($activation) { throw new ErrorUpdatingActivationRecordException("Error updating activation user id - $activation->user_id record", 1); }) 
						: call_user_func( function() use ($token) { $this->token = $token; return $this; });
	}

	private function sendVerificationEmail(User $user, $token)
	{
		$result = $this->activation_handler->sendEmailActivation($user, $token);
		return !$result ? 
						call_user_func( function() use ($user) { throw new ErrorSendingVerificationEmail("Error sending verification email for user - $user->email", 1); })
						: call_user_func( function() use ($result) { return $this; });
	}
}