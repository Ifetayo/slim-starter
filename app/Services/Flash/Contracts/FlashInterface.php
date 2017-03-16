<?php 
namespace SlimStarter\Services\Flash\Contracts;
/**
 * 
 * @author Ifetayo Agunbiade
 **/

interface FlashInterface
{
	public function addMessage($type, $message);	
} // END interface FlashInterface