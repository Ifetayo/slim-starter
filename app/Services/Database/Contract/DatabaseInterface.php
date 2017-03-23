<?php 
namespace SlimStarter\Services\Database\Contract;

interface DatabaseInterface
{
	public function bootDB(array $settings);
	public function beginTransaction();
	public function rollback();
	public function commit();
}