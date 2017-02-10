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

//session_start();
//var_dump(require __DIR__.'/../../../public/index.php');

class ControllersTest extends \PHPUnit_Framework_TestCase
{
    protected $response;
    protected $request;
    protected $app;
    protected $auth_controller;


	 public function __construct(){
       
        
        //$this->auth_controller = new AuthController($this->app->getContainer()->get(Twig::class), $this->app->getContainer()->get(FlashInterface::class));

		//NB: At this point the $this->app->run() method has not been called
		//Tests would be run with $app($request, $response) check the slim app class for more details
	}

    public function setUp()
    {
        $_SESSION = array();
        //$this->app->getContainer() = null;
        require __DIR__.'/../../../vendor/autoload.php';
        if (is_null($this->app)) {
            # code...
            $this->app= null;
            $this->app = new SlimStarter();
        //load dependencies
        $this->app->load($this->app);
        }
        
        var_dump($this->app->getContainer()->get('router')->getNamedRoute('auth.signup')->getName());
        var_dump('##########################################');
        $this->auth_controller = new AuthController($this->app->getContainer()->get(Twig::class), $this->app->getContainer()->get(FlashInterface::class));
    }

	/**
     *
     * Process the request
     * 
     * @param string $method
     * @param string $url
     * @param array $data
     *
     */    
	public function createRequestAndResponse($method, $url, array $data = [])
    {
        $this->request = $this->prepareRequest($method, $url, $data);
        $this->response = new Response();       
    }

    public function runApp()
    {
        //in other to test to run the application you can use either 1 or 2

        /* 1 */        
            //var_dump($this->app->getContainer()->set('request', $this->request));
            //die();
            //$this->app->getContainer()->set('request', $this->request);
            //$this->app->getContainer()->set('response', $this->response);
            //return $this->app->run(true);
        /* or 2 */
        //$app = $this->app;
        //$this->response = $app($this->request, $this->response);
        //return $this->response;
        return $this->app->process($this->request, $this->response);
    }

    /**
     *
     * Set up the request
     * 
     * @param string $method
     * @param string $url
     * @param array $data
     *
     * @return Slim\Http\Request
     */    
    private function prepareRequest($method, $url, array $data)
    {
        $env = Environment::mock([
            'SCRIPT_NAME' => '/index.php',
            'REQUEST_URI' => $url,
            'REQUEST_METHOD' => $method,
        ]);

        $query_data = explode('?', $url);

        if (isset($query_data[1])) {
            $env['QUERY_STRING'] = $query_data[1];
        }

        

        $request = Request::createFromEnvironment($env)->withParsedBody($data);
        return $request;
    }

     /** {@inheritdoc} */
    protected function tearDown()
    {
        var_dump('texpression');

        //$this->app = null;
        $this->response = null;
        $this->request = null;
    }
}