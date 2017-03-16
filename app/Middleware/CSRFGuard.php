<?php 
namespace SlimStarter\Middleware;

use Slim\Router;
use Slim\Csrf\Guard as Guard;
use SlimStarter\Services\Flash\Contracts\FlashInterface;
use SlimStarter\Middleware\Contracts\CSRFInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 *
 * @package default
 * @author 
 **/
class CSRFGuard extends Guard implements CSRFInterface
{
	private $flash;
	private $router;

	public function setFlashObject(FlashInterface $flash)
	{
		$this->flash = $flash;
	}

	public function setRouterObject(Router $router)
	{
		$this->router = $router;
	}

	/**
     * Setter for failureCallable
     *
     * @param mixed $failureCallable Value to set
     * @return $this
     */
    public function setFailureCallable($failureCallable)
    {
        $this->failureCallable = function (Request $request, Response $response, $next) {
        		$this->flash->addMessage('info', "Something went wrong :(. Do not panic, we are working to get it fixed. Why don't you try again later");
				return $response->withRedirect($this->router->pathFor('home'), 400);
            };
        return $this;
    }  
} // END CSRFGuard class