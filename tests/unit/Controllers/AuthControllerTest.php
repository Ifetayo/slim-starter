<?php 
namespace SlimStarter\unit\Controllers;

use Slim\Flash\Messages;
use Slim\Router;
use \Slim\Views\Twig;
use SlimStarter\Controllers\Auth\AuthController;
use SlimStarter\Flash\Contracts\FlashInterface;
use SlimStarter\FormValidation\FormValidatorInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Repositories\UserRepository;

class AuthControllerTest extends ControllerTest 
{
	/** @test */
	public function cannot_register_a_user_with_incorrect_input()
	{
		$dirty_params = ['username' => 'tester', 'name' => 'test', 'password' => 'password'];

		$this->createRequestAndResponse('POST', '/signup', $dirty_params);
		$response = $this->runApp();		

		$this->assertEquals($_SESSION['errors']['email'], "Application error. Contact admin.");

		$dirty_params = ['email' => '', 'name' => 'test', 'password' => 'password'];
		$this->createRequestAndResponse('POST', '/signup', $dirty_params);
		$this->runApp();		
		$this->assertEquals($_SESSION['errors']['email'][0], "Email must not be empty");		
	}

	/** @test */
	public function returns_the_user_an_error_message_if_their_details_could_not_be_stored()
	{
		$auth_controller = new AuthController($this->app->getContainer()->get(Twig::class), $this->app->getContainer()->get(FlashInterface::class));

		$_SESSION = array();
		//the first test tests for validation and correct input
		//this test tests for the path coverage where the storedetails fails
		//that is where the returned user is null after submission
		$params = ['email' => 'tester@testing.com', 'password' => 'testpassword', 'name' => 'Tester SlimStarter'];
		//instantiate the request and response objects
		$this->createRequestAndResponse('POST', '/signup', $params);
		//mock the validator
		$form_validator = $this->getMockBuilder(FormValidatorInterface::class)->getMock();
		$form_validator->expects($this->any())->method('validateSignUpForm')->will($this->returnValue($form_validator));
		//mock the user repository
		$user_repo = $this->getMockBuilder(UserRepositoryInterface::class)->getMock();
		$user_repo->expects($this->any())->method('isUserAvailable')->will($this->returnValue(true));

		$router = $this->getMockBuilder(Router::class)->getMock();
		$router->expects($this->any())->method('pathFor')->will($this->returnValue('auth.signup'));

		$auth_controller->postSignUp($this->request, $this->response, $user_repo, $form_validator, $router);

		$this->assertTrue(isset($_SESSION['slimFlash']['info']));
		$this->assertEquals($_SESSION['slimFlash']['info'][0], "Something went wrong :(. Do not panic, we are working to get it fixed. Why don't you try again later");
	}

	/** @test */
	public function can_register_a_user_with_correct_input()
	{
		$time = time();
		$params = ['email' => $time.'@testing.com', 'name' => 'Tester SlimStarter', 'password' => 'testpassword'];

		$this->createRequestAndResponse('POST', '/signup', $params);

		$result = $this->runApp();
		$this->assertSame($result->getStatusCode(), 302);

		$this->assertTrue(isset($_SESSION['slimFlash']['success']));
		$this->assertEquals($_SESSION['slimFlash']['success'][0], "Your registration has been successfully. An email has been sent to the provided email. You would need to verify your email before you can login");
	}

	/*public function can_route_to_get_sign_up_page()
	{
		$this->createRequestAndResponse('GET', '/signup');
		$response = $this->runApp();
		$view = $this->app->getContainer()->get(Twig::class);
		$expected_output = $view->fetch('auth\signup.twig');
		
		$this->assertEquals($response->getBody()->__toString(), $expected_output);
	}*/

	/** @test */
	public function can_get_sign_up_page_view()
	{
		$auth_controller = new AuthController($this->app->getContainer()->get(Twig::class), $this->app->getContainer()->get(FlashInterface::class));

		$response = new \Slim\Http\Response;
		$response = $auth_controller->getSignUp($response);

		$view = $this->app->getContainer()->get(Twig::class);
		$expected_output = $view->fetch('auth\signup.twig');

		$this->assertEquals($response->getBody()->__toString(), $expected_output);
	}

	/** @test */
	public function can_get_sign_in_page()
	{
		$auth_controller = new AuthController($this->app->getContainer()->get(Twig::class), $this->app->getContainer()->get(FlashInterface::class));

		$response = new \Slim\Http\Response;
		$response = $auth_controller->getSignIn($response);

		$view = $this->app->getContainer()->get(Twig::class);
		$expected_output = $view->fetch('auth\signin.twig');

		$this->assertEquals($response->getBody()->__toString(), $expected_output);
	}

	
	
}