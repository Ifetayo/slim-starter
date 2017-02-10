<?php 

namespace SlimStarter\Repositories\Contracts;

/**
* 
*/
interface UserRepositoryInterface
{
	public function getAll();
	public function isUserAvailable($email);	
	public function registerUser(array $params);
}