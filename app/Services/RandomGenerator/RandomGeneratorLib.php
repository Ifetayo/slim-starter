<?php 
namespace SlimStarter\Services\RandomGenerator;

use RandomLib\Factory;
/**
* 
*/
class RandomGeneratorLib extends Factory
{
	/* Override */	
	/**
	 * 
     * Get a generator for the requested strength
     *
     * @param Strength $strength The requested strength of the random number
     *
     * @throws RuntimeException If an appropriate mixing strategy isn't found
     *
     * @return Generator The instantiated generator
     */
    public function getGenerator(\SecurityLib\Strength $strength)
    {
        $sources = $this->findSources($strength);
        $mixer   = $this->findMixer($strength);

        return new RandomGenerator($sources, $mixer);
    }
}