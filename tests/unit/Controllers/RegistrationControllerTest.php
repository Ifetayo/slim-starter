<?php
namespace SlimStarter\unit\Controllers;

use SlimStarter\Views\ViewsInterface;
use SlimStarter\Repositories\UserRepository;
use SlimStarter\Services\FormValidation\FormValidator;
//use Psr\Http\Message\ResponseInterface as Response;
use SlimStarter\Services\FormValidation\FormValidatorInterface;
//use Psr\Http\Message\ServerRequestInterface as Request;
use SlimStarter\Controllers\Auth\RegistrationController;
use SlimStarter\Views\Concrete\RegistrationControllerView;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Views\Contract\RegistrationControllerViewInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use SlimStarter\Controllers\Auth\Handlers\RegistrationHandler;



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

	/** @test */
	public function can_view_sign_up_page()
	{
		$expected_output = "Sign up page";		
		$this->reg_view->expects($this->once())->method('viewSignUpPage')->will($this->returnValue($expected_output));
		$response = $this->reg_controller->getSignUp();
		$this->assertEquals($response, $expected_output);
	}

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

	/** @test */
	public function redirect_to_signup_page_when_user_cannot_be_created_with_message_called()
	{
		$expected_output = "Redirected to signup page";

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));
		$response_result = $this->reg_controller->couldNotCreateUserRecord();

		$this->assertEquals($response_result, $expected_output);
	}

	/** @test */
	public function redirect_to_signup_page_when_activation_cannot_be_created_with_message_called()
	{
		$expected_output = "Redirected to signup page";

		$this->reg_view->expects($this->once())->method('withMessage')->will($this->returnValue($this->reg_view));
		$this->reg_view->expects($this->once())->method('redirectToSignUpPage')->will($this->returnValue($expected_output));
		$response_result = $this->reg_controller->couldNotCreateActivationRecord();

		$this->assertEquals($response_result, $expected_output);
	}
}