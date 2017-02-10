<?php 
namespace SlimStarter\unit\Controllers;

use Dotenv\Dotenv;
use Slim\Http\Uri;
use \Slim\Views\Twig;
use Slim\Http\Headers;
use Slim\Http\Request;
use Slim\Http\Response;
use Slim\Http\Environment;
use Slim\Http\RequestBody;
use SlimStarter\SlimStarter;
use Respect\Validation\Validator as v;
use Illuminate\Database\Capsule\Manager as Capsule;
use SlimStarter\Controllers\Auth\AuthController;
use SlimStarter\Flash\Contracts\FlashInterface;
use SlimStarter\FormValidation\FormValidatorInterface;
use SlimStarter\Repositories\Contracts\UserRepositoryInterface;


class AuthControllerTest extends \PHPUnit_Framework_TestCase
{
	protected $app;
	protected $kiss;
	public function setUp()
	{

		$_SESSION = array();
		//$app = null;
		$app = new SlimStarter();
		//$this->app = $app;
        //load dependencies
        $app->load($app);
        $this->app = $app;
        //return $app;
        //$this->app->getContainer() = null;
        
        //if (is_null($this->app)) {
            # code...
            //$this->app= null;
   
            /*$this->app = new SlimStarter();
        //load dependencies
        $this->app->load($this->app);*/
        //}
        
        //var_dump($this->app->getContainer()->get('router')->getNamedRoute('auth.signup')->getName());
        //var_dump('##########################################');
        //$this->auth_controller = new AuthController($this->app->getContainer()->get(Twig::class), $this->app->getContainer()->get(FlashInterface::class));
	}

	public function getIn()
	{
		if (is_null($this->app)) {
			$this->setUp();
		}
		return $this->app;
	}

	public function test1()
	{
		var_dump('t1');
		$app = $this->getIn();
		//$app = null;
		//$app = new SlimStarter;
        //load dependencies
        //$app->load($app);
        //$app = $app->getI();
        

		//var_dump($this->app->getContainer()->get('router'));
        //var_dump($app->getContainer()->get('router')->getNamedRoute('auth.signup')->getName());
        //$app->getContainer()->get('response')->withStatus(400)->write('test request');
        var_dump($app->getContainer()->get('router')->getNamedRoute('auth.signup')->getName());
        //die();
        //return $res->withStatus(400)->write('Bad Request');

        //die();
	//	$this->assertTrue(true);
	}

	public function test2()
	{
		var_dump('t2');
        //var_dump($this->app->getContainer()->get('router'));
        $app = $this->getIn();
        //$app = null;
		//$app = new SlimStarter;
		//$app->load($app);
        //$app = $app->getI();
        //var_dump($app);
        //load dependencies
        //return $app;
        var_dump($app->getContainer()->get('router')->getNamedRoute('auth.signup')->getName());

		$this->assertTrue(true);
	}

	public function tearDown()
	{
		

		# code...
	}
}