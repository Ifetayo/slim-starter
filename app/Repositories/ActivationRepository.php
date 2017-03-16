<?php 
namespace SlimStarter\Repositories;

use SlimStarter\Models\Activation;
use SlimStarter\Repositories\Contracts\ActivationRepositoryInterface;
/**
* 
*/
class ActivationRepository implements ActivationRepositoryInterface
{
	public function createActivationRecord($user_id, $token)
	{
		return Activation::create([
	            'user_id' => $user_id,
	            'token_hash' => password_hash($token, PASSWORD_BCRYPT),
	    ]);	
	}

	public function refreshActivationRecord(Activation $activation, $token)
	{
		$activation->token_hash = password_hash($token, PASSWORD_BCRYPT);
		$activation->save();
		return $activation;
	}

	public function get(Activation $activation, $property)
	{
		return $activation->$property;
	}

	public function save(Activation $activation)
	{
		return $activation->save();
	}
}