<?php 
namespace SlimStarter\FormValidation;

use SlimStarter\Repositories\Contracts\UserRepositoryInterface;

/**
* 
*/
interface FormValidatorInterface
{
	public function validateSignUpForm(array $params, UserRepositoryInterface $user_repo);
	
	public function hasErrors();
}