<?php

class ModelCustomerCustomerGroup extends PT_Model
{
    public function addCustomerGroup($data)
    {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "customer_group SET  name = '" . $this->db->escape((string)$data['customer_name']) . "',short_form = '" . $this->db->escape((string)$data['short_form']) . "',  sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");
        $customer_group_id = $this->db->lastInsertId();
        return $query;
    }

    public function editCustomerGroup($customer_group_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer_group SET name = '" . $this->db->escape((string)$data['customer_name']) . "',short_form = '" . $this->db->escape((string)$data['short_form']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW() WHERE customer_group_id = '" . (int)$customer_group_id . "'");
    
        # SEO URL
        $this->db->query("DELETE FROM " . DB_PREFIX . "seo_url WHERE query = 'customer_group_id=" . (int)$customer_group_id . "'");

    }

    public function deleteCustomerGroup($customer_group_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");

        $this->cache->delete('customer_group');
    }

    public function getCustomerGroup($customer_group_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group WHERE customer_group_id = '" . (int)$customer_group_id . "'");

        return $query->row;
    }
    public function getCustomerGroups()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "customer_group");

        return $query->rows;
    }
        public function getCustomerGroupSeoUrls($customer_group_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'customer_group_id=" . (int)$customer_group_id . "'");

        return $query->row;
    }
}
