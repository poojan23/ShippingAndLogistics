<?php

class ModelInformationInformationGroup extends PT_Model
{
    public function getInformationGroup($information_group_id) {
        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information_group WHERE information_group_id = '" . (int)$information_group_id . "' AND status = '1'");
        
        return $query->row;
    }
    
    public function getInformationGroups() {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "information_group WHERE status = '1' ORDER BY sort_order ASC");
        
        return $query->rows;
    }
}


