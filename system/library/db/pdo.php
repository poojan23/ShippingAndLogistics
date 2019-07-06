<?php
namespace DB;

final class PDO
{
	private $dbh;
	private $stmt;

	public function __construct($hostname, $username, $password, $database, $port = '3306')
	{
		try {
			$this->dbh = @new \PDO("mysql:host=" . $hostname . ";port=" . $port . ";dbname=" . $database, $username, $password, array(\PDO::ATTR_PERSISTENT => true));
		} catch (\PDOException $e) {
			throw new \Exception('Error: Could not make a database link using ' . $username . '@' . $hostname . '!');
		}

		$this->dbh->exec("SET NAMES 'utf8'");
		$this->dbh->exec("SET CHARACTER SET utf8");
		$this->dbh->exec("SET CHARACTER_SET_CONNECTION=utf8");
		$this->dbh->exec("SET SQL_MODE = ''");
	}

	public function execute()
	{
		try {
			if ($this->stmt && $this->stmt->execute()) {
				$data = array();

				while ($row = $this->stmt->fetch(\PDO::FETCH_ASSOC)) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$result->row = (isset($data[0])) ? $data[0] : array();
				$result->rows = $data;
				$result->num_rows = $this->stmt->rowCount();
			}
		} catch (\PDOException $e) {
			throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode());
		}
	}

	public function query($sql, $params = array())
	{
		$this->stmt = $this->dbh->prepare($sql);

		$result = false;

		try {
			if ($this->stmt && $this->stmt->execute($params)) {
				$data = array();

				while ($row = $this->stmt->fetch(\PDO::FETCH_ASSOC)) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$result->row = (isset($data[0]) ? $data[0] : array());
				$result->rows = $data;
				$result->num_rows = $this->stmt->rowCount();
			}
		} catch (\PDOException $e) {
			throw new \Exception('Error: ' . $e->getMessage() . ' Error Code : ' . $e->getCode() . ' <br />' . $sql);
		}

		if ($result) {
			return $result;
		} else {
			$result = new \stdClass();
			$result->row = array();
			$result->rows = array();
			$result->num_rows = 0;

			return $result;
		}
	}

	public function prepare($sql)
	{
		$this->stmt = $this->dbh->prepare($sql);
	}

	public function bindParam($parameter, $variable, $data_type = \PDO::PARAM_STR, $length = 0)
	{
		if ($length) {
			$this->stmt->bindParam($parameter, $variable, $data_type, $length);
		} else {
			$this->stmt->bindParam($parameter, $variable, $data_type);
		}
	}

	public function escape($value)
	{
		return str_replace(array("\\", "\0", "\n", "\r", "\x1a", "'", '"'), array("\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"'), $value);
	}

	public function countAffected()
	{
		if ($this->stmt) {
			return $this->stmt->rowCount();
		} else {
			return 0;
		}
	}

	public function lastInsertId()
	{
		return $this->dbh->lastInsertId();
	}

	public function isConnected()
	{
		if ($this->dbh) {
			return true;
		} else {
			return false;
		}
	}

	public function __destruct()
	{
		$this->dbh = null;
	}
}
