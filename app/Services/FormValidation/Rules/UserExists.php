<?php 
namespace SlimStarter\Services\FormValidation\Rules;

use Respect\Validation\Rules\AbstractRule;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;

/**
* 
*/
class UserExists extends AbstractRule
{
	protected $user_repo;

	function __construct(UserRepositoryInterface $user_repo) {
		$this->user_repo = $user_repo;
	}

	public function validate($email)
	{
		return $this->user_repo->userExists($email);
	}	
}