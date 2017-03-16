<?php
namespace SlimStarter\Repositories\Contracts;

use SlimStarter\Models\Activation;
/**
* 
*/
interface ActivationRepositoryInterface
{
	public function get(Activation $activation, $property);
	public function save(Activation $activation);
	
	public function createActivationRecord($user_id, $token);
}