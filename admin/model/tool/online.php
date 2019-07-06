<?php

class ModelToolOnline extends PT_Model
{
    public function getTotalOnlines()
    {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "unique_visitor");

        return $query->row['total'];
    }
}
