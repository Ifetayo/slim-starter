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

session_start();
/**
 * @runTestsInSeparateProcesses
 */
abstract class ControllerTest extends \PHPUnit_Framework_TestCase
{
    protected $response;
    protected $request;
    protected $app;

	 public function setUp(){
       
        $_SESSION = array();
        date_default_timezone_set('UTC');
        require __DIR__.'/../../../vendor/autoload.php';        

        $this->app = new SlimStarter();
        //load dependencies
        $this->app->load($this->app);
		//NB: At this point the $this->app->run() method has not been called		
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
    
    /**
     * Run the app request
     *
     * @return @return Slim\Http\Response
     *
     */
    public function runApp()
    {
        //in other to test to run the application you can use either 1 or 2 or 3
        //Tests can be ran with either of the commented out lines check the slim app class for more details

        /* 1 using the run method*/        
        /*
        $this->app->getContainer()->set('request', $this->request);
        $this->app->getContainer()->set('response', $this->response);
        return $this->app->run(true);*/

        /* or 2 using the app invoke*/

        /*$app = $this->app;
        $this->response = $app($this->request, $this->response);
        return $this->response;*/

        /* or 3 using the process method*/

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
        $this->app = null;
        $this->response = null;
        $this->request = null;
    }
}