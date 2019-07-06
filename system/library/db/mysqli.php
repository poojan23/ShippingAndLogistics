<?php
namespace DB;

final class MySQLi
{
	private $dbh;
	private $connected;

	public function __construct($hostname, $username, $password, $database, $port = '3306')
	{
		try {
			mysqli_report(MYSQLI_REPORT_STRICT);

			$this->dbh = @new \mysqli($hostname, $username, $password, $database, $port);
		} catch (\mysqli_sql_exception $e) {
			throw new \Exception('Error: Could not make a database link using ' . $username . '@' . $hostname . '!');
		}

		$this->dbh->set_charset("utf8");
		$this->dbh->query("SET SQL_MODE = ''");
	}

	public function query($sql)
	{
		$query = $this->dbh->query($sql);

		if (!$this->dbh->errno) {
			if ($query instanceof \mysqli_result) {
				$data = array();

				while ($row = $query->fetch_assoc()) {
					$data[] = $row;
				}

				$result = new \stdClass();
				$result->num_rows = $query->num_rows;
				$result->row = isset($data[0]) ? $data[0] : array();
				$result->rows = $data;

				$query->close();

				return $result;
			} else {
				return true;
			}
		} else {
			throw new \Exception('Error: ' . $this->dbh->error  . '<br />Error No: ' . $this->dbh->errno . '<br />' . $sql);
		}
	}

	public function escape($value)
	{
		return $this->dbh->real_escape_string($value);
	}

	public function countAffected()
	{
		return $this->dbh->affected_rows;
	}

	public function lastInsertId()
	{
		return $this->dbh->insert_id;
	}

	public function isConnected()
	{
		return $this->dbh->ping();
	}

	public function __destruct()
	{
		if ($this->dbh) {
			$this->dbh->close();
		}
	}
}
