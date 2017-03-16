<?php 
namespace SlimStarter\Controllers\Auth\Contracts;

use SlimStarter\Models\User;
/**
 * Contract interface for regsitering users
 *
 * @author Ifetayo Agunbiade
 **/
interface RegistrationInterface
{
	public function createUserRecord(array $params);
	public function createActivationRecord($user_id);
	public function sendVerificationEmail(User $user, $token);	
} // END interface RegistrationInterface