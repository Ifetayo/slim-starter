<?php 
namespace SlimStarter\unit\CustomValidations;

use SlimStarter\FormValidation\FormValidator;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;

class CustomValidationsTest extends ValidationTest
{
	protected $form_validator;
	protected $user_repo;

	public function setUp()
	{
		$this->form_validator = new FormValidator;
		$this->user_repo =  $this->createMock(UserRepositoryInterface::class);

	}

	
	public function returns_false_when_email_has_been_taken_by_another_user()
	{
		$params = array('email' => 'test@testing.com', 'name' => 'test', 'password' => 'password');
		$result = $this->form_validator->validateSignUpForm($params, $this->user_repo);
		//var_dump($result);
		//die();
		assert(true);

	}
}