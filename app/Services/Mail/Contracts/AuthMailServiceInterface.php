<?php 
namespace SlimStarter\Services\Mail\Contracts;

use SlimStarter\Models\User;
/**
 * undocumented class
 *
 * @package default
 * @author 
 **/
interface AuthMailServiceInterface
{
	public function sendEmailVerification(User $user, $token);	
} // END interface MailServiceInterface
