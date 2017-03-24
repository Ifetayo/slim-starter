<?php 
namespace SlimStarter\Controllers\Auth\Handlers;

use Carbon\Carbon;
use SlimStarter\Models\User;
use SlimStarter\Models\Activation;
use SlimStarter\Services\Mail\Contracts\AuthMailServiceInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Controllers\Auth\Contracts\RegistrationInterface;
use \SlimStarter\Services\RandomGenerator\RandomGeneratorInterface;
use SlimStarter\Repositories\Contracts\ActivationRepositoryInterface;

class ActivationHandler
{
	protected $activation_repo;
	protected $mailer;
	protected $rand_gen;

	function __construct(ActivationRepositoryInterface $activation_repo, RandomGeneratorInterface $rand_gen, AuthMailServiceInterface $mailer)
	{
		$this->activation_repo = $activation_repo;
		$this->rand_gen = $rand_gen;
		$this->mailer = $mailer;
	}

	/**
	 *
	 * Create activation record for the corresponding user id
	 * @param int $user_id user id of the user the activation record belongs to
	 */	
	public function createActivationToken($user_id)
	{
		$token = $this->createToken();
		$activation = $this->activation_repo->createActivationRecord($user_id, $token);

		if (!$activation) {
			return false;
		}
		return $token;
	}

	/**
	 *
	 * Update the activation record with a new token
	 * @param Activation $activation 
	 * @return bool|string if activation could not be created return false, else return token string
	 */	
	public function refreshToken(Activation $activation)
	{
		$token = $this->createToken();

		$activation->token_hash = password_hash($token, PASSWORD_BCRYPT);
		$activation->updated_at = date('Y-m-d H:i:s', time());
		$activation->resent_count = 0;

		$activation = $this->activation_repo->save($activation);
		if (!$activation) {
			return false;
		}
		return $token;
	}

	/**
	 *
	 * Send user an email with the verification token link
	 * @param User $user affected user
	 * @param string $token token to be emailed
	 * @return bool
	 */	
	public function sendEmailActivation(User $user, $token)
	{
		return $this->mailer->sendEmailVerification($user, $token);
	}

	/**
	 *
	 * Set the verification email resent count
	 * @param Activation $activation activation object we to update
	 * @param int $value value we want to set the resent count to
	 * @return bool
	 */	
	public function setResentCount(Activation $activation, $value)
	{
		$activation->resent_count = $value;
		return $this->activation_repo->save($activation);
	}
	
	/**
	 *
	 * Return a string alphanumeric of length 128
	 * @return string
	 */	
	private function createToken()
	{
		return $this->rand_gen->generateString(128);
	}

	/**
	 *
	 * Should expire in one day from
	 * when the activation record was last updated
	 * @param Activation $activation
	 * @return bool returns true if the token is still or false it it aint
	 */	
	public function checkEmailTokenValidity(Activation $activation)
	{
		try {
			$send_date = Carbon::createFromFormat('Y-m-d H:i:s', $activation->updated_at);
			if ($send_date->addMinutes(1440)->diffInMinutes(Carbon::now(), false) < 0) {
				return true;
			}
		} catch (\Exception $e) {
			//do some error reporting here
		}
		return false;
	}

	/**
	 *
	 * Validate the token the user has given
	 * @param Activation $activation the user's stored activation record
	 * @param string $token the token from the user
	 * @return bool return true is the token the user has 
	 * provided matches the hash in her activation record, else return false
	 */	
	public function validateToken(Activation $activation, $token)
	{
		if (password_verify($token, $activation->token_hash)) {
			return true;
		}
		return false;
	}

	/**
	 *
	 * Check if the user's activation has sent too many emails
	 * @param Activation $activation
	 * @return bool return true if the user has reached the limit '4'
	 * else return false if the user has maxed the number of email that can be sent
	 */	
	public function emailThrottle(Activation $activation)
	{
		return $activation->resent_count >= 4 ? false : true;
	}
}