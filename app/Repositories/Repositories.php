<?php 

namespace SlimStarter\Repositories;

/**
* Abstract class definition for all repositories.
* An abstract class has been created to boot
* Illuminate Eloquent. If you want to use a different ORM
* or library all you have to do is boot the setup from here
* and use the implementation in your repositories class.
* An example is given for PDO, if I want to use PDO instead of eloquent, all
* I have to do here is comment or remove the eloquent initialization in the construstor
* and write the initialization code for PDO or which ever ORM library I refer.
*/
abstract class Repositories
{
	private $capsule;
	function __construct(array $settings)
	{
		$this->capsule = new \Illuminate\Database\Capsule\Manager;
		$this->capsule->addConnection($settings);
		$this->capsule->setAsGlobal();
		$this->capsule->bootEloquent();
	}

	//Uncomment this block if you want to use PDO
	/*protected $pdo;
	function __construct(array $settings) {
		$this->pdo = new \PDO("mysql:host={$settings['host']};dbname={$settings['database']}", $settings['username'], $settings['password']);
	}*/
}