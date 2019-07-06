<?php
namespace Account;

class Customer
{
	private $customer_id;
	private $firstname;
	private $date;
	private $president;
	private $email;
	private $mobile;
	private $website;
	private $image;

	public function __construct($registry)
	{
		$this->config = $registry->get('config');
		$this->db = $registry->get('db');
		$this->request = $registry->get('request');
		$this->session = $registry->get('session');

		if (isset($this->session->data['customer_id'])) {
			$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "club WHERE club_id = '" . (int)$this->session->data['customer_id'] . "' AND status = '1'");

			if ($customer_query->num_rows) {
				$this->customer_id = $customer_query->row['club_id'];
				$this->firstname = $customer_query->row['club_name'];
				$this->date = $customer_query->row['date'];
				$this->president = $customer_query->row['president'];
				$this->email = $customer_query->row['email'];
				$this->mobile = $customer_query->row['mobile'];
				$this->website = $customer_query->row['website'];
				$this->image = $customer_query->row['image'];
				$this->assistant_governor = $customer_query->row['assistant_governor'];
				$this->district_secretary = $customer_query->row['district_secretary'];


				$this->db->query("UPDATE " . DB_PREFIX . "club SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE club_id = '" . (int)$this->customer_id . "'");
			} else {
				$this->logout();
			}
		}
	}

	public function login($email, $password, $override = false)
	{
		$customer_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "club WHERE LOWER(email) = '" . $this->db->escape(utf8_strtolower($email)) . "' AND status = '1'");

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
					$this->db->query("UPDATE " . DB_PREFIX . "club SET password = '" . $this->db->escape(password_hash($password, PASSWORD_DEFAULT)) . "' WHERE club_id = '" . (int)$customer_query->row['club_id'] . "'");
				}
			}

			$this->session->data['customer_id'] = $customer_query->row['club_id'];
			$this->session->data['firstname'] = $customer_query->row['club_name'];
			$this->session->data['date'] = $customer_query->row['date'];
			$this->session->data['president'] = $customer_query->row['president'];
			$this->session->data['email'] = $customer_query->row['email'];
			$this->session->data['mobile'] = $customer_query->row['mobile'];
			$this->session->data['website'] = $customer_query->row['website'];
			$this->session->data['image'] = $customer_query->row['image'];
			$this->session->data['assistant_governor'] = $customer_query->row['assistant_governor'];
			$this->session->data['district_secretary'] = $customer_query->row['district_secretary'];

			$this->customer_id = $customer_query->row['club_id'];
			$this->firstname = $customer_query->row['club_name'];
			$this->date = $customer_query->row['date'];
			$this->president = $customer_query->row['president'];
			$this->email = $customer_query->row['email'];
			$this->mobile = $customer_query->row['mobile'];
			$this->website = $customer_query->row['website'];
			$this->image = $customer_query->row['image'];
			$this->assistant_governor = $customer_query->row['assistant_governor'];
			$this->district_secretary = $customer_query->row['district_secretary'];
			

			$this->db->query("UPDATE " . DB_PREFIX . "club SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE club_id = '" . (int)$this->customer_id . "'");

			return true;
		} else {
			return false;
		}
	}

	public function logout()
	{
		unset($this->session->data['customer_id']);

		$this->customer_id = '';
		$this->firstname = '';
		$this->date = '';
		$this->president = '';
		$this->email = '';
		$this->mobile = '';
		$this->website = '';
		$this->image = '';
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

	public function getDate()
	{
		return $this->date;
	}

	public function getPresident()
	{
		return $this->president;
	}

	public function getEmail()
	{
		return $this->email;
	}

	public function getMobile()
	{
		return $this->mobile;
	}

	public function getWebsite()
	{
		return $this->website;
	}

	public function getImage()
	{
		return $this->image;
	}
	public function getAssistant()
	{
		return $this->assistant_governor;
	}
	public function getDistrict()
	{
		return $this->district_secretary;
	}
	public function getGroupId()
	{
		
	}

	/*public function getBalance()
	{
		$query = $this->db->query("SELECT SUM(amount) AS total FROM " . DB_PREFIX . "club_transaction WHERE club_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}

	public function getRewardPoints()
	{
		$query = $this->db->query("SELECT SUM(points) AS total FROM " . DB_PREFIX . "club_reward WHERE club_id = '" . (int)$this->customer_id . "'");

		return $query->row['total'];
	}*/
}
