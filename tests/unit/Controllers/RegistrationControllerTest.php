<?php
namespace SlimStarter\unit\Controllers;

use Slim\Http\Request;
use Slim\Http\Response;
use SlimStarter\Models\User;
use SlimStarter\Views\ViewsInterface;
use SlimStarter\Repositories\UserRepository;
use SlimStarter\Services\FormValidation\FormValidator;
use SlimStarter\Controllers\Auth\RegistrationController;
use SlimStarter\Views\Concrete\RegistrationControllerView;
use SlimStarter\Controllers\Auth\Handlers\RegistrationHandler;
use SlimStarter\Services\FormValidation\FormValidatorInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Views\Contract\RegistrationControllerViewInterface;




/**
* @coversDefaultClass  SlimStarter\Controllers\Auth\RegistrationController
*/
class RegistrationControllerTest extends BaseControllerTest
{
	protected $reg_view;
	protected $view;
	protected $reg_controller;

	public function setUp()
	{
		parent::setUp();
		$this->view = $this->getMockBuilder(ViewsInterface::class)->disableOriginalConstructor()->getMock();
		$this->reg_view = $this->getMockBuilder(RegistrationControllerView::class)->disableOriginalConstructor()->getMock();
		$this->reg_controller = new RegistrationController($this->reg_view);
	}

	/**
	 * @covers ::getSignUp
	 */	
	/** @test */
	public function can_view_sign_up_page()
	{
		$expected_output = "Sign up page";		
		$this->reg_view->expects($this->once())->method('viewSignUpPage')->will($this->returnValue($expected_output));
		$response = $this->reg_controller->getSignUp();
		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::postSignUp
	 */
	/** @test */
	public function calls_redirect_to_signup_page_when_bad_input_happens_and_session_with_errors_must_have_validation_errors()
	{
		$expected_output = "Errors in sign up form";
		$dirty_params = ['email' => 'tester@example', 'name' => 'test', 'password' => 'password'];

		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$request->expects($this->once())->method('getParams')->will($this->returnValue($dirty_params));

		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));		

		$user_repo = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
		$user_repo->expects($this->any())->method('isUserAvailable')->will($this->returnValue(false));

		$form_validator = new FormValidator($user_repo);

		$reg_handler = $this->getMockBuilder(RegistrationHandler::class)->disableOriginalConstructor()->getMock();
		
		$response_result = $this->reg_controller->postSignUp($request, $form_validator, $reg_handler);

		$this->assertEquals($_SESSION['errors']['email'][0], 'Email must be valid email');
		$this->assertInstanceOf(FormValidatorInterface::class, $form_validator);
		$this->assertEquals($response_result, $expected_output);
	}

	/**
	 * @covers ::postSignUp
	 */
	/** @test */
	public function calls_registration_handler_if_validation_passes()
	{
		$expected_output = "user registration begins";
		$params = ['email' => time().'@example.com',
			        'name' => 'Ifetayo',
			        'password' => 'password',];

		$request = $this->getMockBuilder(Request::class)->disableOriginalConstructor()->getMock();
		$request->expects($this->once())->method('getParams')->will($this->returnValue($params));

		$user_repo = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
		$user_repo->expects($this->any())->method('isUserAvailable')->will($this->returnValue(true));

		$form_validator = new FormValidator($user_repo);

		$reg_handler = $this->getMockBuilder(RegistrationHandler::class)->disableOriginalConstructor()->getMock();
		$reg_handler->expects($this->once())->method('registerUser')->will($this->returnValue($expected_output));
		
		$response_result = $this->reg_controller->postSignUp($request, $form_validator, $reg_handler);
		$this->assertInstanceOf(FormValidatorInterface::class, $form_validator);
		$this->assertEquals($response_result, $expected_output);
	}

	/**
	 * @covers ::couldNotCreateUserRecord
	 */
	/** @test */
	public function redirect_to_signup_page_when_user_cannot_be_created_with_message_called()
	{
		$expected_output = "Redirected to signup page";

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));
		$response_result = $this->reg_controller->couldNotCreateUserRecord();

		$this->assertEquals($response_result, $expected_output);
	}

	/**
	 * @covers ::couldNotCreateActivationRecord
	 */
	/** @test */
	public function redirect_to_signup_page_when_activation_cannot_be_created_with_message_called()
	{
		$expected_output = "Redirected to signup page";

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));
		$response_result = $this->reg_controller->couldNotCreateActivationRecord();

		$this->assertEquals($response_result, $expected_output);
	}

	/**
	 * @covers ::couldNotSendVerificationEmail
	 */
	/** @test */
	public function could_not_send_user_verification_email()
	{
		$user = new User(['email' => 'ifetayo.agunbiade@gmail.com',
				        'first_name' => 'Ifetayo',
				        'last_name' => 'Agunbiade',
				        'password' => 'password',]);
		$token = 'some_r4ndom_numb3rs_4ndl$tt$r3';

		$expected_output = "Redirected to signup page when verification email not sent";

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));
		$response_result = $this->reg_controller->couldNotSendVerificationEmail($user, $token);

		$this->assertEquals($response_result, $expected_output);
	}

	/**
	 * @covers ::registrationComplete
	 */
	/** @test */
	public function when_registration_is_complete()
	{
		$user = new User(['email' => 'ifetayo.agunbiade@gmail.com',
				        'first_name' => 'Ifetayo',
				        'last_name' => 'Agunbiade',
				        'password' => 'password',]);
		$token = 'some_r4ndom_numb3rs_4ndl$tt$r3';

		$expected_output = "Redirected to signup page when registration process is complete";

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));
		$response_result = $this->reg_controller->registrationComplete($user, $token);

		$this->assertEquals($response_result, $expected_output);
	}

	/**
	 * @covers ::redirectHome
	 */
	/** @test */
	public function redirect_to_home_page()
	{
		$expected_output = "Home";

		$this->reg_view->expects($this->once())->method('redirectHome')->will($this->returnValue($expected_output));
		$response_result = $this->reg_controller->redirectHome();

		$this->assertEquals($response_result, $expected_output);
	}
}