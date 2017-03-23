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
use SlimStarter\Controllers\Auth\RegistrationController;
use SlimStarter\Views\Concrete\RegistrationControllerView;
use SlimStarter\Controllers\Auth\Handlers\ActivationHandler;
use SlimStarter\Services\CustomExceptions\UserRecordNotSaved;
use SlimStarter\Controllers\Auth\Handlers\RegistrationHandler;
use SlimStarter\Services\FormValidation\FormValidatorInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;
use SlimStarter\Services\CustomExceptions\ActivationRecordNotSaved;
use SlimStarter\Views\Contract\RegistrationControllerViewInterface;
use SlimStarter\Services\Database\Contract\DatabaseInterface as DB;
use SlimStarter\Services\CustomExceptions\ErrorSendingVerificationEmail;

/**
* @coversDefaultClass  SlimStarter\Controllers\Auth\Handlers\RegistrationHandler
*/
class RegistrationHandlerTest extends BaseControllerTest
{
	protected $user_repo;

	public function setUp()
	{
		parent::setUp();
		$this->user_repo = $this->getMockBuilder(UserRepositoryInterface::class)->disableOriginalConstructor()->getMock();
		$this->activation_handler = $this->getMockBuilder(ActivationHandler::class)->disableOriginalConstructor()->getMock();
		$this->db = $this->getMockBuilder(DB::class)->getMock();
		$this->reg_handler = new RegistrationHandler($this->user_repo, $this->activation_handler, $this->db);
	}

	/**
	 * @covers ::registerUser
	 *
	 */	
	/** @test */
	public function redirect_to_call_back_could_not_create_user_if_user_could_not_be_create()
	{
		$expected_output = "User not saved";
		$params = ['email' => time().'@example.com', 'name' => 'Ifetayo', 'password' => 'password'];

		$call_back = $this->getMockBuilder(RegistrationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('couldNotCreateUserRecord')->will($this->returnValue($expected_output));

		$response = $this->reg_handler->registerUser($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::registerUser
	 *
	 */	
	/** @test */
	public function redirect_to_call_back_could_not_create_activation_if_activation_could_not_be_create()
	{
		$expected_output = "Activation not saved";
		$params = ['email' => time().'@example.com', 'name' => 'Ifetayo', 'password' => 'password'];
		$user = new User(['email' => time().'@example.com', 'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);

		$this->user_repo->expects($this->once())->method('registerUser')->will($this->returnValue($user));

		$call_back = $this->getMockBuilder(RegistrationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('couldNotCreateActivationRecord')->will($this->returnValue($expected_output));

		$response = $this->reg_handler->registerUser($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::registerUser
	 *
	 */	
	/** @test */
	public function redirect_to_call_back_could_not_send_verification_email()
	{
		$expected_output = "Could not sent verification email";
		$params = ['email' => time().'@example.com', 'name' => 'Ifetayo', 'password' => 'password'];
		$user = new User(['email' => time().'@example.com', 'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$token = rand(0, 120);

		$this->user_repo->expects($this->once())->method('registerUser')->will($this->returnValue($user));
		$this->activation_handler->expects($this->once())->method('createActivationToken')->will($this->returnValue($token));

		$call_back = $this->getMockBuilder(RegistrationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('couldNotSendVerificationEmail')->will($this->returnValue($expected_output));

		$response = $this->reg_handler->registerUser($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}
		
	/**
	 * @covers ::registerUser
	 *
	 */	
	/** @test */
	public function redirect_to_call_back_registration_complete_when_all_records_are_created_and_verification_email_is_sent()
	{
		$expected_output = "User record, activation record created and verification email sent";
		$params = ['email' => time().'@example.com', 'name' => 'Ifetayo', 'password' => 'password'];
		$user = new User(['email' => time().'@example.com', 'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$token = rand(0, 120);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0, 'activated' => 1]);

		$this->user_repo->expects($this->once())->method('registerUser')->will($this->returnValue($user));
		$this->activation_handler->expects($this->once())->method('createActivationToken')->will($this->returnValue($token));
		$this->activation_handler->expects($this->once())->method('sendEmailActivation')->will($this->returnValue(true));

		$call_back = $this->getMockBuilder(RegistrationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('registrationComplete')->will($this->returnValue($expected_output));

		$response = $this->reg_handler->registerUser($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}

	/**
	 * @covers ::createUserRecord
	 *
	 */	
	/** @test */
	public function throw_exception_when_user_record_is_not_created()
	{
		$params = ['email' => time().'@example.com', 'name' => 'Ifetayo', 'password' => 'password'];
		$this->expectException(UserRecordNotSaved::class);
		$response = $this->reg_handler->createUserRecord($params);
	}

	/**
	 * @covers ::createUserRecord
	 *
	 */	
	/** @test */
	public function return_class_instance_when_user_record_is_created()
	{
		$params = ['email' => time().'@example.com', 'name' => 'Ifetayo', 'password' => 'password'];
		$user = new User(['email' => time().'@example.com', 'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);

		$this->user_repo->expects($this->once())->method('registerUser')->will($this->returnValue($user));
		$response = $this->reg_handler->createUserRecord($params);
		$this->assertInstanceOf(RegistrationHandler::class, $response);
	}

	/**
	 * @covers ::createActivationRecord
	 *
	 */	
	/** @test */
	public function throw_exception_when_activation_record_is_not_created()
	{
		$this->expectException(ActivationRecordNotSaved::class);
		$response = $this->reg_handler->createActivationRecord(1);
	}

	/**
	 * @covers ::createActivationRecord
	 *
	 */	
	/** @test */
	public function return_class_instance_when_activation_record_is_created()
	{
		$this->activation_handler->expects($this->once())->method('createActivationToken')->will($this->returnValue(rand(0, 120)));
		$response = $this->reg_handler->createActivationRecord(1);
		$this->assertInstanceOf(RegistrationHandler::class, $response);
	}

	/**
	 * @covers ::sendVerificationEmail
	 *
	 */	
	/** @test */
	public function throw_exception_when_verification_email_could_not_be_sent()
	{
		$user = new User(['email' => time().'@example.com', 'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$token = rand(0, 120);

		$this->expectException(ErrorSendingVerificationEmail::class);
		$response = $this->reg_handler->sendVerificationEmail($user, $token);
	}

	/**
	 * @covers ::sendVerificationEmail
	 *
	 */	
	/** @test */
	public function return_class_instance_when_verification_email_is_sent()
	{
		$user = new User(['email' => time().'@example.com', 'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);
		$token = rand(0, 120);
		$this->activation_handler->expects($this->once())->method('sendEmailActivation')->will($this->returnValue(true));
		$response = $this->reg_handler->sendVerificationEmail($user, $token);

		$this->assertInstanceOf(RegistrationHandler::class, $response);
	}
}