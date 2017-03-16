<?php 
namespace SlimStarter\Services\Mail\Auth;

use Mailgun\Mailgun;
use SlimStarter\Models\User;
use SlimStarter\Services\Mail\Mailer;
use SlimStarter\Services\Mail\MailGunMessageBuilder;
use SlimStarter\Views\ViewsInterface as View;
use SlimStarter\Services\Mail\Contracts\AuthMailServiceInterface;
/**
* 
*/
class AuthMailer extends Mailer implements AuthMailServiceInterface
{
	/*protected $settings;
	protected $client;
	protected $view;

	function __construct(View $view, array $settings, Mailgun $mail_gun) {
		dd($mail_gun);
		$this->settings = $settings;
		$this->client = new Mailgun($settings['API_KEY']);
		$this->view = $view;
	}*/

	public function sendEmailVerification(User $user, $token)
	{
		$data = [
					'user' => $user,
					'token' => $token,
		];
		try {
			$message = new MailGunMessageBuilder($this->client->MessageBuilder(), $this->view);
		
			$message->to($user->email, $user->fullName());		
			$message->from($this->settings['FROM_ADDRESS'],'Slim Starter Support');
			$message->subject('Welcome, please verify your email');
			$message->body('emails\auth\verifyemail.twig', $data);

			$result = $this->client->sendMessage($this->settings['DOMAIN'], $message->getMessage());

			return $result->http_response_code === 200 ? true :  function() use ($result){
															//ideally you might want to do some error reporting here
															return false ;
													};
		} catch (\Exception $e) {
			//log error here
			return false;
		}		
	}
}