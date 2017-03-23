<?php 
namespace SlimStarter\Services\RandomGenerator;

use RandomLib\Generator;
/**
* 
*/
class RandomGenerator extends Generator implements RandomGeneratorInterface
{
	public function generateString($length, $characters = '')
	{
		return parent::generateString($length, $characters);
	}
}
