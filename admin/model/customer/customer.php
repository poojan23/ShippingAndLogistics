<?php

class ModelCustomerCustomer extends PT_Model
{
    public function addCustomer($data)
    {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET  date = '" . $this->db->escape((string)$data['date']) . "',parent_id = '" . (int)$data['parent_id'] . "',austin_governor_id = '" .(int)$data['austin_governor_id'] . "',customer_name = '" . $this->db->escape((string)$data['name']) . "',president = '" . $this->db->escape((string)$data['president']) . "',district_secretary = '" . $this->db->escape((string)$data['secretary']) . "',mobile = '" . $this->db->escape((string)$data['mobile']) . "',email = '" . $this->db->escape((string)$data['email']) . "', password = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "',website = '" . $this->db->escape((string)$data['website']) . "',image = '" . $this->db->escape((string)$data['image']) . "',ip = ':81',status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");

        return $query;
    }

    public function editCustomer($customer_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer SET  date = '" . $this->db->escape((string)$data['date']) . "',customer_name = '" . $this->db->escape((string)$data['name']) . "',president = '" . $this->db->escape((string)$data['president']) . "',district_secretary = '" . $this->db->escape((string)$data['secretary']) . "',mobile = '" . $this->db->escape((string)$data['mobile']) . "',email = '" . $this->db->escape((string)$data['email']) . "',website = '" . $this->db->escape((string)$data['website']) . "',status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE customer_id = '" . (int)$customer_id . "'");

        if (isset($data['image'])) {
            $this->db->query("UPDATE " . DB_PREFIX . "customer SET image = '" . $this->db->escape((string)$data['image']) . "' WHERE customer_id = '" . (int)$customer_id . "'");
        }
    }

    public function deleteCustomer($customer_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

        $this->cache->delete('customer');
    }

    public function getCustomer($customer_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE customer_id = '" . (int)$customer_id . "'");

        return $query->row;
    }
    public function getCustomers()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE status = '1' AND austin_governor_id = '" . $this->session->data['user_id'] . "'");

        return $query->rows;
    }
}
