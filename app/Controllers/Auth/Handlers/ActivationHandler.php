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

	public function createActivationToken($user_id)
	{
		$token = $this->createToken();
		$activation = $this->activation_repo->createActivationRecord($user_id, $token);

		if (!$activation) {
			return false;
		}
		return $token;
	}

	public function refreshToken(Activation $activation)
	{
		$token = $this->createToken();
		$activation = $this->activation_repo->refreshActivationRecord($activation, $token);

		if (!$activation) {
			return false;
		}
		return $token;
	}

	public function sendEmailActivation(User $user, $token)
	{
		return $this->mailer->sendEmailVerification($user, $token);
	}

	public function setResentCount($activation, $value)
	{
		$activation->resent_count = $value;
		return $this->activation_repo->save($activation);
	}
	

	public function setActivationEmailResentCount(Activation $activation, $value)
	{
		$activation->resent_count = $value;
		return $this->activation_repo->save($activation);
	}

	private function createToken()
	{
		return $this->rand_gen->generateString(128);
	}

	/**
	 *
	 * Should expire in one day from
	 * when the activation record was last updated
	 *
	 */	
	public function checkEmailTokenValidity(Activation $activation)
	{
		$send_date = $activation->updated_at;
		try {
			if ($send_date->addMinutes(1440)->diffInMinutes(Carbon::now(), false) < 0) {
				return true;
			}
		} catch (\Exception $e) {
			//do some error reporting here
		}
		return false;
	}

	public function setVerification()
	{

		/*$activation->activated = $value;
		return $this->activation_repo->updateActivationRecord($activation);*/
	}

	public function validateToken(Activation $activation, $token)
	{
		if (password_verify($token, $activation->token_hash)) {
			return true;
		}
		return false;
	}

	public function emailThrottle(Activation $activation)
	{
		$send_date = $activation->updated_at;
		try {
			if ($activation->resent_count >= 4) {
				if ($send_date->addMinutes(1440)->diffInMinutes(Carbon::now(), false) < 0) {
					/*throw new TooManyEmailsException("We have sent too many activation emails to this account. Check your spam or contact admin", 1);*/
					return false;					
				}
			}
		}catch (\Exception $e) {
			return false;
			//do some error reporting here
		}
		return true;
	}
}