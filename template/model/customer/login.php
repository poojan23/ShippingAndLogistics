<?php
class ModelCustomerLogin extends PT_Model {
	public function addCustomer($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "customer` SET customername = '" . $this->db->escape($data['customername']) . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', image = '" . $this->db->escape($data['image']) . "', status = '" . (int)$data['status'] . "', date_added = NOW()");
	
		return $this->db->getLastId();
	}

	public function editCustomer($customer_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET customername = '" . $this->db->escape($data['customername']) . "', customer_group_id = '" . (int)$data['customer_group_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', image = '" . $this->db->escape($data['image']) . "', status = '" . (int)$data['status'] . "' WHERE customer_id = '" . (int)$customer_id . "'");

		if ($data['password']) {
			$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "' WHERE customer_id = '" . (int)$customer_id . "'");
		}
	}

	public function editPassword($customer_id, $password) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function editCode($email, $code) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET code = '" . $this->db->escape($code) . "' WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}

	public function deleteCustomer($customer_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer` WHERE customer_id = '" . (int)$customer_id . "'");
	}

	public function getCustomer($customer_id) {
		$query = $this->db->query("SELECT *, (SELECT ug.name FROM `" . DB_PREFIX . "customer_group` ug WHERE ug.customer_group_id = u.customer_group_id) AS customer_group FROM `" . DB_PREFIX . "customer` u WHERE u.customer_id = '" . (int)$customer_id . "'");

		return $query->row;
	}

	public function getCustomerByCustomername($customername) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE customername = '" . $this->db->escape($customername) . "'");

		return $query->row;
	}

	public function getCustomerByEmail($email) {
		$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "customer` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row;
	}

	public function getCustomerByCode($code) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE code = '" . $this->db->escape($code) . "' AND code != ''");

		return $query->row;
	}

	public function getCustomers($data = array()) {
		$sql = "SELECT * FROM `" . DB_PREFIX . "customer`";

		$sort_data = array(
			'customername',
			'status',
			'date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY customername";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getTotalCustomers() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer`");

		return $query->row['total'];
	}

	public function getTotalCustomersByGroupId($customer_group_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE customer_group_id = '" . (int)$customer_group_id . "'");

		return $query->row['total'];
	}

	public function getTotalCustomersByEmail($email) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "customer` WHERE LCASE(email) = '" . $this->db->escape(utf8_strtolower($email)) . "'");

		return $query->row['total'];
	}
}