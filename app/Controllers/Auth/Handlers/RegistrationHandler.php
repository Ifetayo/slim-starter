<?php 
namespace SlimStarter\Controllers\Auth\Handlers;

use SlimStarter\Models\User;
use Illuminate\Database\Capsule\Manager as DB;
use SlimStarter\Controllers\Auth\RegistrationController;
use SlimStarter\Services\CustomExceptions\User404Exception;
use SlimStarter\Services\CustomExceptions\UserRecordNotSaved;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Controllers\Auth\Contracts\RegistrationInterface;
use SlimStarter\Services\CustomExceptions\Activation404Exception;
use SlimStarter\Services\CustomExceptions\ActivationRecordNotSaved;
use SlimStarter\Services\CustomExceptions\ErrorSendingVerificationEmail;

/**
* RegistrationHandler
*/
class RegistrationHandler implements RegistrationInterface
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

	public function registerUser(array $params, RegistrationController $call_back)
	{
		$this->db->beginTransaction();
		try {
			$result = $this->createUserRecord($params)
							->createActivationRecord($this->user->id)
								->sendVerificationEmail($this->user, $this->token);
		} catch (UserRecordNotSaved $e) {
			//you could do some error reporting here, same as the rest
			$this->db->rollback();
			return $call_back->couldNotCreateUserRecord();
		}catch(ActivationRecordNotSaved $e){
			$this->db->rollback();
			return $call_back->couldNotCreateActivationRecord();
		} catch(ErrorSendingVerificationEmail $e){
			$this->db->rollback();
		}		
		$this->db->commit();
		return $call_back->registrationComplete($this->user, $this->token);
	}

	public function createUserRecord(array $params)
	{
		$user = $this->user_repo->registerUser($params);
		return is_null($user) ? 
						call_user_func( function(){ throw new UserRecordNotSaved("User record could not be saved", 1); }) 
						: call_user_func( function () use ($user) { $this->user = $user; return $this; });
	}
	
	public function createActivationRecord($user_id)
	{
		$token = $this->activation_handler->createActivationToken($user_id);
		return !$token ? 
				call_user_func( function(){ throw new ActivationRecordNotSaved("Activation record could not be saved", 1); }) 
				: call_user_func( function () use ($token) { $this->token = $token; return $this; }) ;
	}

	public function sendVerificationEmail(User $user, $token)
	{
		$result = $this->activation_handler->sendEmailActivation($user, $token);
		return !$result ? call_user_func( function(){ throw new ErrorSendingVerificationEmail("Error sending verification email", 1); })
					: call_user_func( function(){ return $this; });
	}

	/**
	 *
	 * Verify the user token and email
	 * First you find the user by email
	 * if a user exists get the activation record
	 * if the activation record exists, check that the token has not expired
	 * if it hasn't set the verified flag on the db to true
	 */	
	public function verifyUserEmail(array $params, RegistrationController $call_back)
	{
		//check for user
		try {
			$result = $this->getUser($params['email'])
								->getActivationRecord($this->user)
									->checkActivationTokenHasExpired($this->activation, $params['token']);
		} catch (User404Exception $e) {
			$call_back->noUserFound();
		}catch(Activation404Exception $e){
			$call_back->noActivationFound();
		}		
		//check for activation record
		//check activation record is within 2 days
		//set activation in user table
	}

	private function getUser($email)
	{
		$user = $this->user_repo->findUserByEmail($params['email']);
		return is_null($user) ? 
						call_user_func( function() use($email){ throw new User404Exception("No User record by email - $email found", 1); }) 
						: call_user_func( function () use ($user) { $this->user = $user; return $this; });
	}

	private function getActivationRecord(User $user)
	{
		$activation = $this->user_repo->getActivation($user);
		return is_null($activation) ? 
						call_user_func( function() use($user){ throw new Activation404("No activation record for email - $user->email found", 1); }) 
						: call_user_func( function () use ($activation) { $this->activation = $activation; return $this; });
	}

	private function checkActivationTokenHasExpired($activation, $token)
	{
		return $this->activation_handler->checkEmailTokenValidity($activation, $token);
	}
}