<?php 

namespace SlimStarter\Services\FormValidation\Exceptions;

use Respect\Validation\Exceptions\ValidationException;
/**
* 
*/
class UserExistsException extends ValidationException
{
	public static $defaultTemplates = [
		self::MODE_DEFAULT => [
			self::STANDARD => 'User doesn\'t exist.',
		],
	];
}