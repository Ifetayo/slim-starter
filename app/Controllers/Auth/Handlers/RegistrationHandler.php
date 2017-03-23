<?php 
namespace SlimStarter\Controllers\Auth\Handlers;

use SlimStarter\Models\User;
use SlimStarter\Controllers\Auth\RegistrationController;
use SlimStarter\Services\CustomExceptions\User404Exception;
use SlimStarter\Services\CustomExceptions\UserRecordNotSaved;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Controllers\Auth\Contracts\RegistrationInterface;
use SlimStarter\Services\CustomExceptions\Activation404Exception;
use SlimStarter\Services\CustomExceptions\ActivationRecordNotSaved;
use SlimStarter\Services\Database\Contract\DatabaseInterface as DB;
use SlimStarter\Services\CustomExceptions\UpdatingActivationException;
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
		$this->db = $db;
	}

	/**
	 *
	 * Create the user record, create the activation record and email the verification email
	 * @param array $params contains the user details from the signup form
	 * @param RegistrationController $call_back 
	 */	
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
			return $call_back->couldNotSendVerificationEmail();
		}	
		$this->db->commit();
		return $call_back->registrationComplete($this->user, $this->token);
	}

	/**
	 *
	 * Create the user record, if not created throw UserRecordNotSaved exception
	 * @param array $params
	 * @throws UserRecordNotSaved if user records cannot be created
	 * @return RegistrationHandler
	 */	
	public function createUserRecord(array $params)
	{
		$user = $this->user_repo->registerUser($params);
		return !$user ? 
						call_user_func( function(){ throw new UserRecordNotSaved("User record could not be saved", 1); }) 
						: call_user_func( function () use ($user) { $this->user = $user; return $this; });
	}
	
	/**
	 *
	 * Create an activation record with the corresponding user id
	 * @param int $user_id
	 * @throws ActivationRecordNotSaved if activation records cannot be created
	 * @return RegistrationHandler
	 */	
	public function createActivationRecord($user_id)
	{
		$token = $this->activation_handler->createActivationToken($user_id);
		return !$token ? 
				call_user_func( function(){ throw new ActivationRecordNotSaved("Activation record could not be saved", 1); }) 
				: call_user_func( function () use ($token) { $this->token = $token; return $this; }) ;
	}

	/**
	 *
	 * Send user a verification email
	 * @param int $user_id
	 * @throws ErrorSendingVerificationEmail if verification email could not be sent
	 * @return RegistrationHandler
	 */	
	public function sendVerificationEmail(User $user, $token)
	{
		$result = $this->activation_handler->sendEmailActivation($user, $token);
		return !$result ? call_user_func( function(){ throw new ErrorSendingVerificationEmail("Error sending verification email", 1); })
					: call_user_func( function(){ return $this; });
	}	
}