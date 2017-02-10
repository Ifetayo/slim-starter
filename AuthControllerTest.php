<?php 
namespace SlimStarter\unit\Controllers;

use Slim\Flash\Messages;
use Slim\Router;
use \Slim\Views\Twig;
use SlimStarter\Controllers\Auth\AuthController;
/*use SlimStarter\Flash\Contracts\FlashInterface;
use SlimStarter\FormValidation\FormValidatorInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;*/
/*use Slim\Http\Request;
use Slim\Http\Response;*/
/**
* Auth Controller test
*/
class AuthControllerTest extends ControllersTest 
{
	//private $auth_controller;

	

	

	

    /** @test */
	public function cannot_register_a_user_with_incorrect_input()
	{
		$dirty_params = ['username' => 'tester', 'name' => 'test', 'password' => 'password'];

		$this->createRequestAndResponse('POST', '/signup', $dirty_params);
		$r = $this->runApp();		
		var_dump($r->getStatusCode());
		$this->assertEquals($_SESSION['errors']['email'], "Application error. Contact admin.");

		/*$dirty_params = ['email' => '', 'name' => 'test', 'password' => 'password'];
		$this->createRequestAndResponse('POST', '/signup', $dirty_params);
		$this->runApp();		
		$this->assertEquals($_SESSION['errors']['email'][0], "Email must not be empty");*/		
	}

	/** @test */
	public function returns_the_user_an_error_message_if_their_details_could_not_be_stored()
	{
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

		$this->auth_controller->postSignUp($this->request, $this->response, $user_repo, $form_validator, $router);

		$this->assertTrue(isset($_SESSION['slimFlash']['info']));
		$this->assertEquals($_SESSION['slimFlash']['info'][0], "Something went wrong :(. Do not panic, we are working to get it fixed. Why don't you try again later");
	}

	/** @test */
	public function can_register_a_user_with_correct_input()
	{
		//$_SESSION = array();
		//$this->request = null;
		//$this->response = null;
		$params = ['email' => 'tester@testing.com', 'name' => 'Tester SlimStarter', 'password' => 'testpassword'];

		$this->createRequestAndResponse('POST', '/signup', $params);

		$result = $this->runApp();
		var_dump($result->getStatusCode());
		die();

		//assert(true);
		//var_dump($this->response);
		//var_dump($_SESSION['errors']);
		//var_dump($this->container->get(Twig::class)->getEnvironment()->getGlobals());
		//die();
	}

	
	
}