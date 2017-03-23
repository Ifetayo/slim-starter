<?php 
namespace SlimStarter\Repositories;

use Carbon\Carbon;
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
	            'created_at' => date('Y-m-d H:i:s', time()),
	            'updated_at' => date('Y-m-d H:i:s', time()),
	    ]);	
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