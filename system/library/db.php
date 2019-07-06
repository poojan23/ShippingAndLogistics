<?php

class DB
{
	private $driver;

	public function __construct($driver, $hostname, $username, $password, $database, $port = null)
	{
		$class = 'DB\\' . $driver;

		if (class_exists($class)) {
			$this->driver = new $class($hostname, $username, $password, $database, $port);
		} else {
			throw new \Exception('Error: Could not load database driver ' . $driver . '!');
		}
	}

	public function query($sql)
	{
		return $this->driver->query($sql);
	}

	public function escape($value)
	{
		return $this->driver->escape($value);
	}

	public function countAffected()
	{
		return $this->driver->countAffected();
	}

	public function lastInsertId()
	{
		return $this->driver->lastInsertId();
	}

	public function connected()
	{
		return $this->driver->connected();
	}
}
