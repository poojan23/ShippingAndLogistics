<?php

class ModelCatalogCategory extends PT_Model
{
    public function addCategory($data)
    {
        $query = $this->db->query("INSERT INTO " . DB_PREFIX . "category SET  name = '" . $this->db->escape((string)$data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW(), date_added = NOW()");

        return $query;
    }

    public function editCategory($category_id, $data)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "category SET  name = '" . $this->db->escape((string)$data['name']) . "', sort_order = '" . (int)$data['sort_order'] . "', status = '" . (isset($data['status']) ? (int)$data['status'] : 0) . "', date_modified = NOW()WHERE category_id = '" . (int)$category_id . "'");
    }

    public function deleteCategory($category_id)
    {
        $this->db->query("DELETE FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");

        $this->cache->delete('category');
    }

    public function getCategory($category_id)
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category WHERE category_id = '" . (int)$category_id . "'");

        return $query->row;
    }
    public function getCategorys()
    {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "category WHERE status = '1'");

        return $query->rows;
    }
}
