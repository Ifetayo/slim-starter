<?php 
namespace SlimStarter\Services\FormValidation;
/**
* 
*/
interface FormValidatorInterface
{
	public function validateSignUpForm(array $params);
	public function validateEmailVerify(array $params);
	public function validateResendTokenForm(array $params);	
	public function hasErrors();
}