<?php 
namespace SlimStarter\Repositories;

use SlimStarter\Models\User;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
/**
* 
*/
class UserRepository implements UserRepositoryInterface
{
	public function get(User $user, $property)
	{
		return $user->$property;
	}

	public function getAll()
	{
		return "h";
	}

	public function isUserAvailable($email)
	{
		return User::where('email', $email)->count() === 0;
	}

	public function userExists($email)
	{
		return User::where('email', $email)->count() > 0;
	}

	public function registerUser(array $params)
	{
		return User::create([
								'email' => $params['email'],
								'first_name' => $params['name'],
								'last_name' => $params['name'],
								'password' => password_hash($params['password'], PASSWORD_BCRYPT),
		]);
	}

	public function save(User $user)
	{
		return $user->save();
	}

	public function findUserByEmail($email)
	{
		return User::where('email', $email)->first();
	}

}