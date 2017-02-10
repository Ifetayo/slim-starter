<?php 
namespace SlimStarter\Repositories;

use SlimStarter\Models\User;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
/**
* 
*/
class UserRepository implements UserRepositoryInterface
{
	
	public function getAll()
	{
		return "h";
	}

	public function isUserAvailable($email)
	{
		return User::where('email', $email)->count() === 0;
	}

	public function registerUser(array $params)
	{
		return User::create([
								'email' => $params['email'],
								'name' => $params['name'],
								'password' => password_hash($params['password'], PASSWORD_BCRYPT),
		]);
	}

}