<?php

class ModelCustomerCustomer extends PT_Model
{
    public function addCustomer($data)
    {
        $customer_name = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$data['customer_group_id'] . "'");
        
       $query = $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET  customer_group_id = '" . (int)$data['customer_group_id'] . "',name = '" . $this->db->escape((string)$customer_name->row['short_form']) . "',area_id = '" . (int)$data['Area'] . "',mobile = '" . $this->db->escape((string)$data['mobile']) . "',email = '" . $this->db->escape((string)$data['email']) . "', password = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "',status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");

        return $query;
    }

    public function editCustomer($customer_id, $data)
    {
        $customer_name = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$data['customer_group_id'] . "'");

        $this->db->query("UPDATE " . DB_PREFIX . "customer SET  customer_group_id = '" . (int)$data['customer_group_id'] . "',name = '" . $this->db->escape((string)$customer_name->row['short_form']) . "',area_id = '" . (int)$data['Area'] . "',mobile = '" . $this->db->escape((string)$data['mobile']) . "',email = '" . $this->db->escape((string)$data['email']) . "', password = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "',status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW() WHERE customer_id = '" . (int)$customer_id . "'");

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
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer WHERE status = '1'");

        return $query->rows;
    }
}
