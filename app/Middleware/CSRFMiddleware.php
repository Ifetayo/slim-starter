<?php
namespace SlimStarter\Middleware;

/**
* 
*/
class CSRFMiddleware
{
	protected $csrf;
	protected $view;

	public function __construct($csrf, $view) {
		$this->csrf = $csrf;
		$this->view = $view;
	}
	
	public function __invoke($request, $response, $next){
		
		$this->view->getEnvironment()->addGlobal('csrf', [
				'field' => 
				'<input type="hidden" name="' .$this->csrf->getTokenNameKey() . '" value="'.$this->csrf->getTokenName(). '">
				<input type="hidden" name="' .$this->csrf->getTokenValueKey() . '" value="'.$this->csrf->getTokenValue(). '">'
			]);

		$response = $next($request, $response);
		return $response;
	}

}

