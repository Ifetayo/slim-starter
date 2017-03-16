<?php 
namespace SlimStarter\Services\Mail;

use SlimStarter\Views\ViewsInterface as View;
use Mailgun\Messages\MessageBuilder as Message;

/**
* 
*/
class MailGunMessageBuilder
{
	protected $message;
	protected $view;

	function __construct(Message $message, View $view)
	{
		$this->view = $view;
		$this->message = $message;
	}

	public function to($email, $full_name)
	{
		$this->message->addToRecipient($email, array('full_name' => $full_name));
	}

	public function from($address, $sender)
	{
		$this->message->setFromAddress($address, array('full_name' => $sender));
	}

	public function subject($subject)
	{
		$this->message->setSubject($subject);
	}

	public function body($template, $data)
	{
		$this->message->setHtmlBody($this->view->fetch($template, $data));
	}

	public function getMessage()
	{
		return $this->message->getMessage();
	}
}
