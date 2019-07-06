<?php
namespace DB;

final class PgSQL
{
	private $dbh;

	public function __construct($hostname, $username, $password, $database, $port = '5432')
	{
		$this->dbh = @pg_connect('hostname=' . $hostname . ' port=' . $port .  ' username=' . $username . ' password='	. $password . ' database=' . $database);

		if (!$this->dbh) {
			throw new \Exception('Error: Could not make a database link using ' . $username . '@' . $hostname);
		}

		pg_query($this->dbh, "SET CLIENT_ENCODING TO 'UTF8'");
	}

	public function query($sql)
	{
		$resource = pg_query($this->dbh, $sql);

		if ($resource) {
			if (is_resource($resource)) {
				$i = 0;

				$data = array();

				while ($result = pg_fetch_assoc($resource)) {
					$data[$i] = $result;

					$i++;
				}

				pg_free_result($resource);

				$query = new \stdClass();
				$query->row = isset($data[0]) ? $data[0] : array();
				$query->rows = $data;
				$query->num_rows = $i;

				unset($data);

				return $query;
			} else {
				return true;
			}
		} else {
			throw new \Exception('Error: ' . pg_result_error($this->dbh) . '<br />' . $sql);
		}
	}

	public function escape($value)
	{
		return pg_escape_string($this->dbh, $value);
	}

	public function countAffected()
	{
		return pg_affected_rows($this->dbh);
	}

	public function isConnected()
	{
		if (pg_connection_status($this->dbh) == PGSQL_CONNECTION_OK) {
			return true;
		} else {
			return false;
		}
	}

	public function getLastId()
	{
		$query = $this->query("SELECT LASTVAL() AS `id`");

		return $query->row['id'];
	}

	public function __destruct()
	{
		if ($this->isConnected()) {
			pg_close($this->dbh);
		}
	}
}
