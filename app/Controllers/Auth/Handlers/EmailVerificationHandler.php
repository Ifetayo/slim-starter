<?php 
namespace SlimStarter\Controllers\Auth\Handlers;

use Carbon\Carbon;
use SlimStarter\Models\User;
use SlimStarter\Models\Activation;
use Illuminate\Database\Capsule\Manager as DB;
use SlimStarter\Controllers\Auth\EmailVerificationController;
use SlimStarter\Services\CustomExceptions\User404Exception;
use SlimStarter\Services\CustomExceptions\UserRecordNotSaved;
use SlimStarter\Services\CustomExceptions\UpdatingUserException;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Services\CustomExceptions\TokenNotValidException;
use SlimStarter\Services\CustomExceptions\TokenMismatchException;
use SlimStarter\Controllers\Auth\Contracts\RegistrationInterface;
use SlimStarter\Services\CustomExceptions\Activation404Exception;
use SlimStarter\Services\CustomExceptions\TooManyEmailsException;
use SlimStarter\Services\CustomExceptions\ActivationRecordNotSaved;
use SlimStarter\Services\CustomExceptions\UserHasBeenVerifiedException;
use SlimStarter\Services\CustomExceptions\ErrorSendingVerificationEmail;
use SlimStarter\Services\CustomExceptions\ErrorUpdatingActivationRecordException;


/**
* RegistrationHandler
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
		$this->db = $db->connection();
	}

	public function resendVerificationEmail(array $params, EmailVerificationController $call_back)
	{
		//check if the user exists
		//if the user has an activation record
		//if so send the token, and increment the resent count
		//check for user
		try {
			$result = $this->getUser($params['email'])
								->hasNotBeenVerified($this->user)
									->getActivationRecord($this->user)
										->checkActivationTokenHasExpired($this->activation)
											->checkEmailThrottle($this->activation)
												->sendVerificationEmail($this->user, $params['token'])
													->setActivationResentCount($this->activation, ++$this->activation->resent_count);
		} catch (User404Exception $e) {
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
		}
		return $call_back->verificationEmailHasBeenSent($this->user, $params['token']);
	}	

	/**
	 *
	 * Check if the too many emails have been sent to the
	 * user over the last one day
	 * Check that the resent count is less than 4
	 */	
	public function checkEmailThrottle(Activation $activation)
	{
		$result = $this->activation_handler->emailThrottle($activation);
		return !$result ? 
						call_user_func( function() use ($activation){ throw new TooManyEmailsException("We have sent too many activation emails to this account. Check your spam or contact admin", 1); }) 
						: call_user_func( function () { return $this; });
	}

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

	private function getUser($email)
	{
		$user = $this->user_repo->findUserByEmail($email);
		return is_null($user) ? 
						call_user_func( function() use($email){ throw new User404Exception("No User record by email - $email found", 1); }) 
						: call_user_func( function () use ($user) { 
																		$this->user = $user; 
																		return $this; 
																}
										);
	}

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

	private function getActivationRecord(User $user)
	{
		$activation = $this->user_repo->get($user, 'activation');
		return is_null($activation) ? 
						call_user_func( function() use($user){ throw new Activation404Exception("No activation record for email - $user->email found", 1); }) 
						: call_user_func( function () use ($activation) { $this->activation = $activation; return $this; });
	}

	private function checkActivationTokenHasExpired($activation)
	{
		$result = $this->activation_handler->checkEmailTokenValidity($activation);
		return !$result ? 
						call_user_func( function() { throw new TokenNotValidException("Token has expired", 1); }) 
						: call_user_func( function() use ($result) { return $this; });
	}

	public function checkForTokenMismatch(Activation $activation, $token)
	{
		$result = $this->activation_handler->validateToken($activation, $token);
		return !$result ? 
						call_user_func( function() use ($activation) { throw new TokenMismatchException("Token mismatch for user id - $activation->user_id", 1); }) 
						: call_user_func( function() use ($result) { return $this; });

	}

	public function setUserEmailVerifiedFlag(User $user, $value)
	{
		$user->email_verified = $value;
		$result = $this->user_repo->save($user);
		return !$result ? 
						call_user_func( function() { throw new UpdatingUserException("Could not set the email verified db column for user - $user->email", 1); }) 
						: call_user_func( function() use ($result) { return $this; });
	}

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
	{return $this;
		$result = $this->activation_handler->sendEmailActivation($user, $token);
		return !$result ? 
						call_user_func( function() use ($user) { throw new ErrorSendingVerificationEmail("Error sending verification email for user - $user->email", 1); })
						: call_user_func( function() use ($result) { return $this; });
	}
}