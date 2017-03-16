<?php
namespace SlimStarter\Services\FormValidation;

use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Exceptions\ValidationException;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
/**
* Form Validation is done here
*/
class FormValidator implements FormValidatorInterface
{
	protected $errors;
	protected $user_repo;

	function __construct(UserRepositoryInterface $user_repo) {
		$this->user_repo = $user_repo;
	}

	/**
	 *
	 * Validates the sign up form
	 *
	 * @param array params
	 * @return FormValidator
	 */	
	public function validateSignUpForm(array $params)
	{
		$rules = [
				'email' => v::noWhiteSpace()->notEmpty()->email()->isEmailAvailable($this->user_repo),
				'name' => v::notEmpty()->alpha(),
				'password' => v::noWhiteSpace()->notEmpty(),
		];

		foreach ($rules as $field => $rule) {
			try{

				$rule->setName(ucfirst($field))->assert($params[$field]);
			}
			catch(NestedValidationException $e){
				$this->errors[$field] = $e->getMessages();				
			}
			catch(\Exception $e){
				//do some rporting here
				$this->errors[$field] = 'Application error. Contact admin.';
				break;				
			}
		}
		/*
		  Put the errors into session, validationErrorsMiddleware
		  would take care of making it available to the view
		*/		 
		$_SESSION['errors'] = $this->errors;
		//return the validation object
		return $this;		
	}

	public function validateEmailVerify(array $params)
	{
		$rules = [
				'email' => v::noWhiteSpace()->notEmpty()->email(),
				'token' => v::notEmpty(),
		];

		foreach ($rules as $field => $rule) {
			try{
				$rule->setName(ucfirst($field))->assert(urldecode($params[$field]));
			}
			catch(NestedValidationException $e){
				$this->errors[$field] = $e->getMessages();				
			}
			catch(\Exception $e){
				//do some rporting here
				$this->errors[$field] = 'Application error. Contact admin.';
				break;				
			}
		}
		/*
		  Put the errors into session, validationErrorsMiddleware
		  would take care of making it available to the view
		*/		 
		$_SESSION['errors'] = $this->errors;
		//return the validation object
		return $this;
	}

	public function validateResendTokenForm(array $params)
	{
		$rules = [
				'email' => v::noWhiteSpace()->notEmpty()->email(),
				'token' => v::notEmpty(),
		];

		foreach ($rules as $field => $rule) {
			try{
				$rule->setName(ucfirst($field))->assert($params[$field]);
			}
			catch(NestedValidationException $e){
				$this->errors[$field] = $e->getMessages();				
			}
			catch(\Exception $e){
				//do some rporting here
				$this->errors[$field] = 'Application error. Contact admin.';
				break;				
			}
		}
		/*
		  Put the errors into session, validationErrorsMiddleware
		  would take care of making it available to the view
		*/		 
		$_SESSION['errors'] = $this->errors;
		//return the validation object
		return $this;
	}

	/**
	 *
	 * Returns the array of errors
	 *
	 * @return array
	 */	
	public function hasErrors()
	{
		return $this->errors;
	}	
}