<?php 
namespace SlimStarter\Services\Mail;

use Mailgun\Mailgun;
use SlimStarter\Views\ViewsInterface as View;
/**
* 
*/
abstract class Mailer
{
	protected $client;
	protected $view;
	protected $settings;

	function __construct(View $view, array $settings, Mailgun $client) {
		$this->client = $client;
		$this->view = $view;
		$this->settings = $settings;
	}
}