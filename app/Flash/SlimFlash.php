<?php 
namespace SlimStarter\Flash;

use Slim\Flash\Messages;
use SlimStarter\Flash\Contracts\FlashInterface;
/**
* 
*/
class SlimFlash extends Messages implements FlashInterface
{
	/**
     * Get Flash Message
     *
     * @param string $key The key to get the message from
     * @return mixed|null Returns the message
     */
   /* public function getMessage($key)
    {
    	$this->addMessage('er', "new messages");
        $messages = $this->getMessages();
        return $this->storage['slimFlash']['info'];
        // If the key exists then return all messages or null
        return (isset($messages[$key])) ? $messages[$key] : null;
    }*/
	/*public $flash;

	function __construct(Messages $flash) {
		$this->flash = $flash;
	}

	public function addMessage($key, $message)
	{
		$this->flash->addMessage($key, $message);
	}

	public function getMessage($key)
	{
		return $this->flash->getMessage($key);
	}

	*
     * Has Flash Message
     *
     * @param string $key The key to get the message from
     * @return bool Whether the message is set or not
     
    public function hasMessage($key)
    {
    	var_dump(['ldfd',$this->getMessages()]);
    	die();
        $messages = $this->getMessages();
        return isset($messages[$key]);
    }*/
}