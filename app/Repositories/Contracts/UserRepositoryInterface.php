<?php
namespace SlimStarter\Repositories\Contracts;

use SlimStarter\Models\User;
/**
* 
*/
interface UserRepositoryInterface
{
	public function get(User $user, $property);
	public function save(User $user);
	public function getAll();
	public function isUserAvailable($email);	
	public function registerUser(array $params);
	public function findUserByEmail($email);
}