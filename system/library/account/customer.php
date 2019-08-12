<?php
namespace Account;

class Customer
{
	private $customer_id;
	private $customer_group_id;
	private $firstname;
	private $area_id;
	private $email;
	private $mobile;

	public function __construct($registry)
	{
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['customer_id'])) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");

			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['customer_id'];
				$this->customer_group_id = $customer_query->row['customer_group_id'];
				$this->firstname = $customer_query->row['name'];
				$this->	area_id = $customer_query->row['area_id'];
				$this->email = $customer_query->row['email'];
				$this->mobile = $customer_query->row['mobile'];


//				$this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password, $override = false)
	{
		$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND status = '1'");

		if ($customer_query->num_rows) {
			if (!$override) {
				if (password_verify($password, $customer_query->row['password'])) {
					$rehash = password_needs_rehash($customer_query->row['password'], PASSWORD_DEFAULT);
				}  elseif ($customer_query->row['password'] == md5($password)) {
					$rehash = true;
				} else {
					return false;
				}

				if ($rehash) {
					$this->db->query("UPDATE " . DB_PREFIX . "customer SET password = '" . $this->db->escape(password_hash($password, PASSWORD_DEFAULT)) . "' WHERE customer_id = '" . (int)$customer_query->row['customer_id'] . "'");
				}
			}

			$this->session->data['customer_id'] = $customer_query->row['customer_id'];
			$this->session->data['customer_group_id'] = $customer_query->row['customer_group_id'];
			$this->session->data['firstname'] = $customer_query->row['name'];
			$this->session->data['area_id'] = $customer_query->row['area_id'];
			$this->session->data['email'] = $customer_query->row['email'];

			$this->customer_id = $customer_query->row['customer_id'];
			$this->customer_group_id = $customer_query->row['customer_group_id'];
			$this->firstname = $customer_query->row['name'];
			$this->area_id = $customer_query->row['area_id'];
			$this->email = $customer_query->row['email'];
			$this->mobile = $customer_query->row['mobile'];
			

//			$this->db->query("UPDATE " . DB_PREFIX . "customer SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE customer_id = '" . (int)$this->customer_id . "'");

			return true;
		} else {
			return false;
		}
	}

	public function logout()
	{
		unset($this->session->data['customer_id']);

		$this->customer_id = '';
		$this->customer_group_id = '';
		$this->firstname = '';
		$this->area_id = '';
		$this->email = '';
		$this->mobile = '';
	}

	public function isLogged()
	{
		return $this->customer_id;
	}

	public function getId()
	{
		return $this->customer_id;
	}

	public function getFirstName()
	{
		return $this->firstname;
	}

	public function getGroupId()
	{
		return $this->customer_group_id;
	}

	public function getAreaId()
	{
		return $this->area_id;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getMobile()
	{
		return $this->mobile;
	}

}
