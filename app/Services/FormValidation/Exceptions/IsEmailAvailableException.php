<?php 

namespace SlimStarter\Services\FormValidation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;
/**
* 
*/
class IsEmailAvailableException extends ValidationException
{
	
	public static $defaultTemplates = [
		self::MODE_DEFAULT => [

			self::STANDARD => 'Email is already taken.',

		],
	];
}