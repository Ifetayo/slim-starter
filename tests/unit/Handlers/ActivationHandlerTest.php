<?php 
namespace SlimStarter\unit\Handlers;

use Slim\Http\Request;
use Slim\Http\Response;
use SlimStarter\Models\User;
use SlimStarter\Models\Activation;
use SlimStarter\unit\BaseControllerTest;
use SlimStarter\Controllers\Auth\Handlers\ActivationHandler;
use SlimStarter\Services\Mail\Contracts\AuthMailServiceInterface;
use SlimStarter\Services\RandomGenerator\RandomGeneratorInterface;
use SlimStarter\Repositories\Contracts\ActivationRepositoryInterface;

/**
* @coversDefaultClass  SlimStarter\Controllers\Auth\Handlers\ActivationHandler
*/
class ActivationHandlerTest extends BaseControllerTest
{
	protected $activation_repo;
	protected $rand_gen;
	protected $mailer;

	public function setUp()
	{
		parent::setUp();

		$this->activation_repo = $this->getMockBuilder(ActivationRepositoryInterface::class)->disableOriginalConstructor()->getMock();
		$this->rand_gen = $this->getMockBuilder(RandomGeneratorInterface::class)->disableOriginalConstructor()->getMock();
		$this->mailer = $this->getMockBuilder(AuthMailServiceInterface::class)->disableOriginalConstructor()->getMock();

		$this->activation_handler = new ActivationHandler($this->activation_repo, $this->rand_gen, $this->mailer);
	}

	/**
	 * @covers ::createActivationToken
	 *
	 */	
	/** @test */
	public function return_false_if_activation_record_could_not_be_created()
	{
		$this->rand_gen->expects($this->once())->method('generateString')->will($this->returnValue(rand(0, 128)));
		$response = $this->activation_handler->createActivationToken(1);
		$this->assertFalse($response);
	}

	/**
	 * @covers ::createActivationToken
	 *
	 */	
	/** @test */
	public function return_token_if_activation_record_is_created()
	{
		$token = rand(0, 128);
		$this->rand_gen->expects($this->once())->method('generateString')->will($this->returnValue($token));
		$this->activation_repo->expects($this->once())->method('createActivationRecord')->will($this->returnValue(true));
		$response = $this->activation_handler->createActivationToken(1);
		
		$this->assertEquals($response, $token);
	}

	/**
	 * @covers ::refreshToken
	 *
	 */	
	/** @test */
	public function return_false_if_activation_record_could_not_be_updated_with_a_fresh_token()
	{
		$token = rand(0, 128);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0, 'activated' => 1]);

		$this->rand_gen->expects($this->once())->method('generateString')->will($this->returnValue($token));
		$this->activation_repo->expects($this->once())->method('save')->will($this->returnValue(null));
		$response = $this->activation_handler->refreshToken($activation);
		
		$this->assertFalse($response);
	}

	/**
	 * @covers ::refreshToken
	 *
	 */	
	/** @test */
	public function return_token_if_activation_record_has_been_updated_with_a_fresh_token()
	{
		$token = rand(0, 128);
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0, 'activated' => 1]);

		$this->rand_gen->expects($this->once())->method('generateString')->will($this->returnValue($token));
		$this->activation_repo->expects($this->once())->method('save')->will($this->returnValue(true));
		$response = $this->activation_handler->refreshToken($activation);
		
		$this->assertEquals($response, $token);
	}
	
	/**
	 * @covers ::sendEmailActivation
	 *
	 */	
	/** @test */
	public function return_false_if_verification_email_was_not_sent()
	{
		$token = rand(0, 128);
		$user = new User(['email' => time().'@example.com', 'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);

		$this->mailer->expects($this->once())->method('sendEmailVerification')->will($this->returnValue(false));
		$response = $this->activation_handler->sendEmailActivation($user, $token);
		
		$this->assertFalse($response);
	}

	/**
	 * @covers ::sendEmailActivation
	 *
	 */	
	/** @test */
	public function return_true_if_verification_email_was_sent()
	{
		$token = rand(0, 128);
		$user = new User(['email' => time().'@example.com', 'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);

		$this->mailer->expects($this->once())->method('sendEmailVerification')->will($this->returnValue(true));
		$response = $this->activation_handler->sendEmailActivation($user, $token);
		
		$this->assertTrue($response);
	} //setResentCount(Activation $activation, $value)

	/**
	 * @covers ::setResentCount
	 *
	 */	
	/** @test */
	public function return_false_if_activation_resent_count_could_not_be_updated()
	{
		$value = 1;
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0, 'activated' => 1]);

		$this->activation_repo->expects($this->once())->method('save')->will($this->returnValue(false));
		$response = $this->activation_handler->setResentCount($activation, $value);
		
		$this->assertFalse($response);
	}

	/**
	 * @covers ::setResentCount
	 *
	 */	
	/** @test */
	public function return_true_if_activation_resent_count_was_updated()
	{
		$value = 1;
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0, 'activated' => 1]);

		$this->activation_repo->expects($this->once())->method('save')->will($this->returnValue(true));
		$response = $this->activation_handler->setResentCount($activation, $value);
		
		$this->assertTrue($response);
	}

	/**
	 * @covers ::checkEmailTokenValidity
	 *
	 */	
	/** @test */
	public function return_false_if_token_has_expired()
	{
		$value = 1;
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0, 'activated' => 1]);
		//set a later date
		$activation->setUpdatedAt('2017-03-16 23:46:54');
		$response = $this->activation_handler->checkEmailTokenValidity($activation);
		
		$this->assertFalse($response);
	}

	/**
	 * @covers ::checkEmailTokenValidity
	 *
	 */	
	/** @test */
	public function return_true_if_token_has_not_expired()
	{
		$value = 1;
		$activation = new Activation(['token_hash' => time(), 'user_id' => 1, 'resent_count' => 0, 'activated' => 1]);
		$activation->setUpdatedAt(date('Y-m-d H:i:s', time()));
		$response = $this->activation_handler->checkEmailTokenValidity($activation);
		
		$this->assertTrue($response);
	}

	/**
	 * @covers ::validateToken
	 *
	 */	
	/** @test */
	public function return_false_if_token_is_a_mismatch()
	{
		$token = rand(0, 128);
		$hash = password_hash(rand(0, 128), PASSWORD_BCRYPT);

		$activation = new Activation(['token_hash' => $hash, 'user_id' => 1, 'resent_count' => 0, 'activated' => 1]);		
		$response = $this->activation_handler->validateToken($activation, $token);
		
		$this->assertFalse($response);
	}

	/**
	 * @covers ::validateToken
	 *
	 */	
	/** @test */
	public function return_true_if_token_is_matches()
	{
		$token = rand(0, 128);
		$hash = password_hash($token, PASSWORD_BCRYPT);

		$activation = new Activation(['token_hash' => $hash, 'user_id' => 1, 'resent_count' => 0, 'activated' => 1]);		
		$response = $this->activation_handler->validateToken($activation, $token);
		
		$this->assertTrue($response);
	}

	/**
	 * @covers ::emailThrottle
	 *
	 */	
	/** @test */
	public function return_false_if_user_has_sent_4_or_more_emails_under_one_day()
	{
		$activation = new Activation(['token_hash' => rand(0, 128), 'user_id' => 1, 'resent_count' => 4, 'activated' => 0]);		
		$activation->setUpdatedAt(date('Y-m-d H:i:s', time()));
		$response = $this->activation_handler->emailThrottle($activation);
		$this->assertFalse($response);
	}

	/**
	 * @covers ::emailThrottle
	 *
	 */	
	/** @test */
	public function return_true_if_user_has_not_up_to_4_or_more_emails()
	{
		$activation = new Activation(['token_hash' => rand(0, 128), 'user_id' => 1, 'resent_count' => 4, 'activated' => 0]);		
		$activation->setUpdatedAt(date('Y-m-d H:i:s', time()));
		$response = $this->activation_handler->emailThrottle($activation);
		$this->assertFalse($response);
	}
	/**
	 * @covers ::registerUser
	 *
	 */	
	/** @test */
	/*public function redirect_to_call_back_could_not_create_activation_if_activation_could_not_be_create()
	{
		$expected_output = "Activation not saved";
		$params = ['email' => time().'@example.com', 'name' => 'Ifetayo', 'password' => 'password'];
		$user = new User(['email' => time().'@example.com', 'first_name' => 'Ifetayo', 'last_name' => 'Agunbiade', 'password' => 'password']);

		$this->user_repo->expects($this->once())->method('registerUser')->will($this->returnValue($user));

		$call_back = $this->getMockBuilder(RegistrationController::class)->disableOriginalConstructor()->getMock();
		$call_back->expects($this->once())->method('couldNotCreateActivationRecord')->will($this->returnValue($expected_output));

		$response = $this->reg_handler->registerUser($params, $call_back);

		$this->assertEquals($response, $expected_output);
	}*/
}