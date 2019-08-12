<?php

class ModelInformationInformation extends PT_Model {

    public function getInformationByGroupId($information_group_id) {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_group ig ON (i.information_group_id = ig.information_group_id) LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE i.information_group_id = '" . (int) $information_group_id . "' AND  i.status = '1' ORDER BY i.sort_order ASC");

        return $query->row;
    }
    
    public function getInformationsByGroupId($information_group_id) {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_group ig ON (i.information_group_id = ig.information_group_id) LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE i.information_group_id = '" . (int) $information_group_id . "' AND  i.status = '1' ORDER BY i.sort_order ASC");

        return $query->rows;
    }

    public function getInformation($information_id) {

        $query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "information i LEFT JOIN " . DB_PREFIX . "information_description id ON (i.information_id = id.information_id) WHERE i.information_id = '" . (int) $information_id . "' AND i.status = '1'");

        return $query->rows;
    }

    
}
