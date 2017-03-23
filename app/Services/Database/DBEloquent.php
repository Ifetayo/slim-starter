<?php 
namespace SlimStarter\Services\Database;

use Illuminate\Database\Capsule\Manager;
use SlimStarter\Services\Database\Contract\DatabaseInterface;

/**
* Eloquent Database Loader
*/
class DBEloquent implements DatabaseInterface
{
	protected $capsule;
	protected static $loaded = false;
	protected static $connection;

	public function bootDB(array $settings)
	{
		if (!self::$loaded) {
			$this->capsule = new Manager;
			$this->capsule->addConnection($settings);
			$this->capsule->setAsGlobal();
			$this->capsule->bootEloquent();

			self::$connection = $this->capsule->getConnection();

			self::$loaded = true;	
		}
	}

	public function beginTransaction()
	{
		return self::$connection->beginTransaction();
	}

	public function rollback()
	{
		return self::$connection->rollback();
	}

	public function commit()
	{
		return self::$connection->commit();
	}
}