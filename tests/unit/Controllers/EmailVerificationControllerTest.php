<?php
namespace SlimStarter\unit\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use SlimStarter\Models\User;
use SlimStarter\Views\ViewsInterface;
use SlimStarter\unit\BaseControllerTest;
use SlimStarter\Repositories\UserRepository;
use SlimStarter\Services\FormValidation\FormValidator;
use SlimStarter\Controllers\Auth\EmailVerificationController;
use SlimStarter\Views\Concrete\RegistrationControllerView;
use SlimStarter\Controllers\Auth\Handlers\EmailVerificationHandler;
use SlimStarter\Services\FormValidation\FormValidatorInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Views\Contract\RegistrationControllerViewInterface;




/**
* @coversDefaultClass  SlimStarter\Controllers\Auth\EmailVerificationControllerTest
*/
class EmailVerificationControllerTest extends BaseControllerTest
{
	protected $reg_view;
	protected $view;
	protected $reg_controller;

	public function setUp()
	{
		parent::setUp();
		$this->view = $this->getMockBuilder(ViewsInterface::class)->disableOriginalConstructor()->getMock();
		$this->reg_view = $this->getMockBuilder(RegistrationControllerView::class)->disableOriginalConstructor()->getMock();
		$this->email_controller = new EmailVerificationController($this->reg_view);
	}

	/**
	 * @covers ::emailVerify
	 */	
	/** @test */
	public function redirect_home_when_request_params_are_bad_or_incomplete()
	{
		$expected_output = "Redirected home";	
		$dirty_params = ['token' => '', 'email' => time().'@example.com'];	

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectHome')->will($this->returnValue($expected_output));
		
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$request->expects($this->once())->method('getParams')->will($this->returnValue($dirty_params));

		$user_repo = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
		$form_validator = new FormValidator($user_repo);

		$verify_handler = $this->getMockBuilder(EmailVerificationHandler::class)->disableOriginalConstructor()->getMock();
		

		$response = $this->email_controller->emailVerify($request, $form_validator,  $verify_handler);
		$this->assertEquals($_SESSION['errors']['token'][0], 'Token must not be empty');
		$this->assertInstanceOf(FormValidatorInterface::class, $form_validator);
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::emailVerify
	 */	
	/** @test */
	public function begin_processing_with_correct_request_params_are_given()
	{
		$expected_output = "Process in email EmailVerificationHandler started";	
		$params = ['token' => rand(1, 120), 'email' => time().'@example.com'];	

		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$request->expects($this->once())->method('getParams')->will($this->returnValue($params));

		$user_repo = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
		$form_validator = new FormValidator($user_repo);

		$verify_handler = $this->getMockBuilder(EmailVerificationHandler::class)->disableOriginalConstructor()->getMock();
		$verify_handler->expects($this->once())->method('verifyUserEmail')->will($this->returnValue($expected_output));

		$response = $this->email_controller->emailVerify($request, $form_validator,  $verify_handler);
		
		$this->assertInstanceOf(FormValidatorInterface::class, $form_validator);
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::resendToken
	 */	
	/** @test */
	public function redirect_to_signup_page_when_request_params_are_bad_or_incomplete()
	{
		$expected_output = "Redirected to signup page";	
		$dirty_params = ['token' => '', 'email' => time().'@example.com'];	

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));
		
		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$request->expects($this->once())->method('getParams')->will($this->returnValue($dirty_params));

		$user_repo = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
		$form_validator = new FormValidator($user_repo);

		$verify_handler = $this->getMockBuilder(EmailVerificationHandler::class)->disableOriginalConstructor()->getMock();

		$response = $this->email_controller->resendToken($request, $form_validator,  $verify_handler);
		$this->assertEquals($_SESSION['errors']['token'][0], 'Token must not be empty');
		$this->assertInstanceOf(FormValidatorInterface::class, $form_validator);
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::emailVerify
	 */	
	/** @test */
	public function begin_resending_token_to_user_with_correct_request_params_are_given()
	{
		$expected_output = "Process of resending token started";	
		$params = ['token' => rand(1, 120), 'email' => time().'@example.com'];	

		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$request->expects($this->once())->method('getParams')->will($this->returnValue($params));

		$user_repo = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
		$form_validator = new FormValidator($user_repo);

		$verify_handler = $this->getMockBuilder(EmailVerificationHandler::class)->disableOriginalConstructor()->getMock();
		$verify_handler->expects($this->once())->method('resendVerificationEmail')->will($this->returnValue($expected_output));

		$response = $this->email_controller->resendToken($request, $form_validator,  $verify_handler);
		
		$this->assertInstanceOf(FormValidatorInterface::class, $form_validator);
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::emailVerify
	 */	
	/** @test */
	public function verification_email_has_been_sent_to_the_user()
	{
		$expected_output = "verfication email re-sent";

		$user = new User(['email' => 'ifetayo.agunbiade@gmail.com',
				        'first_name' => 'Ifetayo',
				        'last_name' => 'Agunbiade',
				        'password' => 'password',]);
		$token = rand(1, 120);

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));

		$response = $this->email_controller->verificationEmailHasBeenSent($user, $token);	
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::noUserFound
	 */	
	/** @test */
	public function no_user_found()
	{
		$expected_output = "no user record found";
		
		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectHome')->will($this->returnValue($expected_output));

		$response = $this->email_controller->noUserFound();	
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::tooManyEmailSent
	 */	
	/** @test */
	public function when_user_has_sent_too_many_emails()
	{
		$expected_output = "too many emails sent";
		
		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectHome')->will($this->returnValue($expected_output));

		$response = $this->email_controller->tooManyEmailSent();	
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::noActivationFound
	 */	
	/** @test */
	public function when_no_activation_record_is_found()
	{
		$expected_output = "no activation record found";		
		
		$this->reg_view->expects($this->once())->method('redirectHome')->will($this->returnValue($expected_output));

		$response = $this->email_controller->noActivationFound();	
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::couldNotUpdateActivationRecord
	 */	
	/** @test */
	public function when_activation_record_could_not_be_updated()
	{
		$expected_output = "activation record could not be updated";		
		
		$this->reg_view->expects($this->once())->method('redirectHome')->will($this->returnValue($expected_output));

		$response = $this->email_controller->couldNotUpdateActivationRecord();	
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::couldNotReSendVerificationEmail
	 */	
	/** @test */
	public function when_verification_email_could_not_be_sent()
	{
		$expected_output = "could not re-send verification email";

		$user = new User(['email' => 'ifetayo.agunbiade@gmail.com',
				        'first_name' => 'Ifetayo',
				        'last_name' => 'Agunbiade',
				        'password' => 'password',]);
		$token = rand(1, 120);

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));

		$response = $this->email_controller->couldNotReSendVerificationEmail($user, $token);	
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::newTokenSent
	 */	
	/** @test */
	public function when_a_new_token_is_generated()
	{
		$expected_output = "new token generated and sent to user";

		$user = new User(['email' => 'ifetayo.agunbiade@gmail.com',
				        'first_name' => 'Ifetayo',
				        'last_name' => 'Agunbiade',
				        'password' => 'password',]);
		$token = rand(1, 120);

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));

		$response = $this->email_controller->newTokenSent($user, $token);	
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::alreadyVerified
	 */	
	/** @test */
	public function when_user_is_already_verified()
	{
		$expected_output = "already verified";

		$this->reg_view->expects($this->once())->method('redirectHome')->will($this->returnValue($expected_output));

		$response = $this->email_controller->alreadyVerified();	
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::couldNotUpdateUser
	 */	
	/** @test */
	public function when_user_could_not_be_updated()
	{
		$expected_output = "could not update user record";

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));

		$response = $this->email_controller->couldNotUpdateUser();	
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::userVerified
	 */	
	/** @test */
	public function when_user_has_been_verified()
	{
		$expected_output = "user has been verified";

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectHome')->will($this->returnValue($expected_output));

		$response = $this->email_controller->userVerified();	
		$this->assertEquals($response, $expected_output);
	}
}