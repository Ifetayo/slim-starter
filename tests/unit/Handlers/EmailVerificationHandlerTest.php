<?php 
namespace SlimStarter\unit\Handlers;

use Slim\Http\Request;
use Slim\Http\Response;
use SlimStarter\Models\User;
use SlimStarter\Models\Activation;
use SlimStarter\Views\ViewsInterface;
use SlimStarter\unit\BaseControllerTest;
use SlimStarter\Repositories\UserRepository;
use SlimStarter\Services\FormValidation\FormValidator;
use SlimStarter\Views\Concrete\RegistrationControllerView;
use SlimStarter\Controllers\Auth\Handlers\ActivationHandler;
use SlimStarter\Services\CustomExceptions\UserRecordNotSaved;
use SlimStarter\Controllers\Auth\EmailVerificationController;
use SlimStarter\Services\FormValidation\FormValidatorInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Services\CustomExceptions\TokenMismatchException;
use SlimStarter\Services\CustomExceptions\TooManyEmailsException;
use SlimStarter\Services\CustomExceptions\ActivationRecordNotSaved;
use SlimStarter\Views\Contract\RegistrationControllerViewInterface;
use SlimStarter\Services\Database\Contract\DatabaseInterface as DB;
use SlimStarter\Controllers\Auth\Handlers\EmailVerificationHandler;
use SlimStarter\Services\CustomExceptions\UpdatingActivationException;
use SlimStarter\Services\CustomExceptions\ErrorSendingVerificationEmail;



/**
* @coversDefaultClass  SlimStarter\Controllers\Auth\Handlers\EmailVerificationHandler
*/
class EmailVerificationHandlerTest extends BaseControllerTest
{
	protected $user_repo;

	public function setUp()
	{
		parent::setUp();
		$this->user_repo = $this->getMockBuilder(UserRepositoryInterface::class)->disableOriginalConstructor()->getMock();
		$this->activation_handler = $this->getMockBuilder(ActivationHandler::class)->disableOriginalConstructor()->getMock();
		$this->db = $this->getMockBuilder(DB::class)->getMock();
		$this->ver_handler = new EmailVerificationHandler($this->user_repo, $this->activation_handler, $this->db);
	}

	/**
	 * @covers ::resendVerificationEmail
	 *
	 */	
	/** @test */
	public function redirect_to_no_user_found_if_the_user_record_could_not_be_found()
	{
		$expected_output = "User 404";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('noUserFound')->will($this->returnValue($expected_output));

		$response = $this->ver_handler->resendVerificationEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::resendVerificationEmail
	 *
	 */	
	/** @test */
	public function redirect_to_already_verified_if_user_is_already_verified()
	{
		$expected_output = "User already verified";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User();
		$user = $user->forceFill(['email' => time().'@example.com', 'email_verified' => true,'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('alreadyVerified')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));

		$response = $this->ver_handler->resendVerificationEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::resendVerificationEmail
	 *
	 */	
	/** @test */
	public function redirect_to_no_activation_found_if_the_user_activation_record_could_not_be_found()
	{
		$expected_output = "No activation record";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('noActivationFound')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));

		$response = $this->ver_handler->resendVerificationEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::resendVerificationEmail
	 *
	 */	
	/** @test */
	public function send_a_fresh_token_when_token_is_not_valid()
	{
		$expected_output = "token not valid, sending fresh token";

		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		$ver_handler = $this->getMockBuilder(EmailVerificationHandler::class)
						->setConstructorArgs(array($this->user_repo, $this->activation_handler, $this->db))
						->setMethods(array('refreshToken'))
						->getMock();
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		$this->user_repo->expects($this->once())->method('getActivation')->will($this->returnValue($activation));
		
		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();

		$ver_handler->expects($this->once())->method('refreshToken')->will($this->returnValue($expected_output));

		$response = $ver_handler->resendVerificationEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::resendVerificationEmail
	 *
	 */	
	/** @test */
	public function redirect_to_too_many_emails_sent_if_the_user_has_sent_too_many_emails()
	{
		$expected_output = "Too many emails sent";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('tooManyEmailSent')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		$this->user_repo->expects($this->once())->method('getActivation')->will($this->returnValue($activation));

		$this->activation_handler->expects($this->once())->method('checkEmailTokenValidity')->will($this->returnValue(true));

		$response = $this->ver_handler->resendVerificationEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::resendVerificationEmail
	 *
	 */	
	/** @test */
	public function redirect_to_could_not_send_email_if_the_verification_email_could_not_be_sent()
	{
		$expected_output = "Could not resend token via email";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('couldNotReSendVerificationEmail')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		$this->user_repo->expects($this->once())->method('getActivation')->will($this->returnValue($activation));
		
		$this->activation_handler->expects($this->once())->method('checkEmailTokenValidity')->will($this->returnValue(true));
		$this->activation_handler->expects($this->once())->method('emailThrottle')->will($this->returnValue(true));

		$response = $this->ver_handler->resendVerificationEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::resendVerificationEmail
	 *
	 */	
	/** @test */
	public function redirect_to_verification_email_has_been_sent_when_activation_record_resent_count_could_not_be_updated()
	{
		$expected_output = "verification email sent, when activation resent count could not be updated";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('verificationEmailHasBeenSent')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		$this->user_repo->expects($this->once())->method('getActivation')->will($this->returnValue($activation));
		
		$this->activation_handler->expects($this->once())->method('checkEmailTokenValidity')->will($this->returnValue(true));
		$this->activation_handler->expects($this->once())->method('emailThrottle')->will($this->returnValue(true));
		$this->activation_handler->expects($this->once())->method('sendEmailActivation')->will($this->returnValue(true));

		$response = $this->ver_handler->resendVerificationEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::resendVerificationEmail
	 *
	 */	
	/** @test */
	public function redirect_to_verification_email_has_been_sent_when_everything_checks_out()
	{
		$expected_output = "Verification email sent";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('verificationEmailHasBeenSent')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		$this->user_repo->expects($this->once())->method('getActivation')->will($this->returnValue($activation));
		
		$this->activation_handler->expects($this->once())->method('checkEmailTokenValidity')->will($this->returnValue(true));
		$this->activation_handler->expects($this->once())->method('emailThrottle')->will($this->returnValue(true));
		$this->activation_handler->expects($this->once())->method('sendEmailActivation')->will($this->returnValue(true));
		$this->activation_handler->expects($this->once())->method('setResentCount')->will($this->returnValue(true));

		$response = $this->ver_handler->resendVerificationEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::checkEmailThrottle
	 *
	 */	
	/** @test */
	public function throw_exception_when_user_has_sent_too_many_emails()
	{
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);
		$this->expectException(TooManyEmailsException::class);
		$response = $this->ver_handler->checkEmailThrottle($activation);
	}

	/**
	 * @covers ::checkEmailThrottle
	 *
	 */	
	/** @test */
	public function return_class_object_if_the_user_is_under_email_sent_limit()
	{
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);
		$this->activation_handler->expects($this->once())->method('emailThrottle')->will($this->returnValue(true));
		$response = $this->ver_handler->checkEmailThrottle($activation);
		$this->assertInstanceOf(EmailVerificationHandler::class, $response);
	}

	/**
	 * @covers ::setActivationResentCount
	 *
	 */	
	/** @test */
	public function throw_exception_when_activation_resent_count_could_not_be_updated()
	{
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);
		$this->expectException(UpdatingActivationException::class);
		$response = $this->ver_handler->setActivationResentCount($activation, 1);
	}

	/**
	 * @covers ::setActivationResentCount
	 *
	 */	
	/** @test */
	public function return_class_object_if_activation_resent_count_could_has_been_updated()
	{
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);
		$this->activation_handler->expects($this->once())->method('setResentCount')->will($this->returnValue(true));
		$response = $this->ver_handler->setActivationResentCount($activation, 1);
		$this->assertInstanceOf(EmailVerificationHandler::class, $response);
	}

	/**
	 * @covers ::verifyUserEmail
	 *
	 */	
	/** @test */
	public function redirect_no_user_found_if_user_record_could_not_be_found_when_verifying_email()
	{
		$expected_output = "no user found";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('noUserFound')->will($this->returnValue($expected_output));
			
		$response = $this->ver_handler->verifyUserEmail($params, $call_back);
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::verifyUserEmail
	 *
	 */	
	/** @test */
	public function redirect_when_user_has_already_been_verified_when_verifying_email()
	{
		$expected_output = "user already verified";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User();
		$user = $user->forceFill(['email' => time().'@example.com', 'email_verified' => true,'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('alreadyVerified')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		
		$response = $this->ver_handler->verifyUserEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::verifyUserEmail
	 *
	 */	
	/** @test */
	public function redirect_when_no_actiation_record_is_found_when_verifying_email()
	{
		$expected_output = "no activation record";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('noActivationFound')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));

		$response = $this->ver_handler->verifyUserEmail($params, $call_back);
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::verifyUserEmail
	 *
	 */	
	/** @test */
	public function send_a_fresh_token_when_token_has_expired()
	{
		$expected_output = "token has expired, sending fresh token";

		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		$ver_handler = $this->getMockBuilder(EmailVerificationHandler::class)
						->setConstructorArgs(array($this->user_repo, $this->activation_handler, $this->db))
						->setMethods(array('refreshToken'))
						->getMock();
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		$this->user_repo->expects($this->once())->method('getActivation')->will($this->returnValue($activation));

		$this->activation_handler->expects($this->once())->method('checkEmailTokenValidity')->will($this->returnValue(false));
		
		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();

		$ver_handler->expects($this->once())->method('refreshToken')->will($this->returnValue($expected_output));

		$response = $ver_handler->verifyUserEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::verifyUserEmail
	 *
	 */	
	/** @test */
	public function send_a_fresh_token_when_token_is_a_mismatch_when_verifying_email()
	{
		$expected_output = "token has mismatch, sending fresh token";

		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		$ver_handler = $this->getMockBuilder(EmailVerificationHandler::class)
						->setConstructorArgs(array($this->user_repo, $this->activation_handler, $this->db))
						->setMethods(array('refreshToken'))
						->getMock();
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		$this->user_repo->expects($this->once())->method('getActivation')->will($this->returnValue($activation));

		$this->activation_handler->expects($this->once())->method('checkEmailTokenValidity')->will($this->returnValue(true));
		
		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();

		$ver_handler->expects($this->once())->method('refreshToken')->will($this->returnValue($expected_output));

		$response = $ver_handler->verifyUserEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::verifyUserEmail
	 *
	 */	
	/** @test */
	public function redirect_when_user_record_could_not_be_updated_when_verifying_email()
	{
		$expected_output = "could not update user record activation record";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('couldNotUpdateUser')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		$this->user_repo->expects($this->once())->method('getActivation')->will($this->returnValue($activation));
		
		$this->activation_handler->expects($this->once())->method('checkEmailTokenValidity')->will($this->returnValue(true));
		$this->activation_handler->expects($this->once())->method('validateToken')->will($this->returnValue(true));

		$response = $this->ver_handler->verifyUserEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::verifyUserEmail
	 *
	 */	
	/** @test */
	public function redirect_when_user_token_has_been_verified()
	{
		$expected_output = "user verified";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('userVerified')->will($this->returnValue($expected_output));
		
		$this->user_repo->expects($this->once())->method('findUserByEmail')->will($this->returnValue($user));
		$this->user_repo->expects($this->once())->method('getActivation')->will($this->returnValue($activation));
		$this->user_repo->expects($this->once())->method('save')->will($this->returnValue(true));
		
		$this->activation_handler->expects($this->once())->method('checkEmailTokenValidity')->will($this->returnValue(true));
		$this->activation_handler->expects($this->once())->method('validateToken')->will($this->returnValue(true));

		$response = $this->ver_handler->verifyUserEmail($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::checkForTokenMismatch
	 *
	 */	
	/** @test */
	public function throw_exception_when_there_is_a_token_mismatch()
	{
		$token = rand(0, 128);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);
		$this->expectException(TokenMismatchException::class);
		$response = $this->ver_handler->checkForTokenMismatch($activation, $token);
	}

	/**
	 * @covers ::checkForTokenMismatch
	 *
	 */	
	/** @test */
	public function return_class_object_when_token_is_a_match()
	{
		$token = rand(0, 128);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$this->activation_handler->expects($this->once())->method('validateToken')->will($this->returnValue(true));
		$response = $this->ver_handler->checkForTokenMismatch($activation, $token);
		$this->assertInstanceOf(EmailVerificationHandler::class, $response);
	}

	/**
	 * @covers ::refreshToken
	 *
	 */	
	/** @test */
	public function redirect_when_activation_record_could_not_be_updated_with_new_token()
	{
		$expected_output = "activation record could not be activated with new token";
		$params = ['email' => time().'@example.com', 'token' => rand(0, 128)];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('couldNotUpdateActivationRecord')->will($this->returnValue($expected_output));
		
		$response = $this->ver_handler->refreshToken($user, $activation, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::refreshToken
	 *
	 */	
	/** @test */
	public function redirect_when_new_token_could_not_be_sent_via_verification_email()
	{
		$expected_output = "activation record could not be activated with new token";
		$token = rand(0, 128);
		$params = ['email' => time().'@example.com', 'token' => $token];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('couldNotReSendVerificationEmail')->will($this->returnValue($expected_output));
		
		$this->activation_handler->expects($this->once())->method('refreshToken')->will($this->returnValue($token));
		
		$response = $this->ver_handler->refreshToken($user, $activation, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::refreshToken
	 *
	 */	
	/** @test */
	public function send_user_a_fresh_token()
	{
		$expected_output = "new token sent via email";
		$token = rand(0, 128);
		$params = ['email' => time().'@example.com', 'token' => $token];
		
		$user = new User(['email' => time().'@example.com','first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0]);

		$call_back = $this->getMockBuilder(EmailVerificationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('newTokenSent')->will($this->returnValue($expected_output));
		
		$this->activation_handler->expects($this->once())->method('refreshToken')->will($this->returnValue($token));
		$this->activation_handler->expects($this->once())->method('sendEmailActivation')->will($this->returnValue(true));
		
		$response = $this->ver_handler->refreshToken($user, $activation, $call_back);

		$this->assertEquals($response, $expected_output);
	}
}